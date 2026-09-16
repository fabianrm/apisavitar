<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Models\RouterMetric;
use App\Services\MikrotikService;
use App\Services\TelegramNotifierService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MonitorMikrotikConnectivityCommand extends Command
{
    protected $signature = 'mk:monitor-connectivity';

    protected $description = 'Verifica la conectividad de los Mikrotik con túnel activo, guarda métricas de CPU/memoria/disco y avisa por Telegram si se cae o se recupera';

    /**
     * Cuántas corridas seguidas fallidas hacen falta antes de avisar,
     * para no alertar por un timeout puntual del router.
     */
    private const FAILS_BEFORE_ALERT = 2;

    /**
     * Cuánto tiempo conservar el historial de métricas.
     */
    private const METRICS_RETENTION_DAYS = 30;

    public function handle(TelegramNotifierService $telegram): int
    {
        $routers = Router::withoutGlobalScopes()
            ->where('ip', 'like', '10.100.100.%')
            ->get();

        foreach ($routers as $router) {
            $mk = null;
            $isUp = false;

            try {
                $mk = MikrotikService::forRouter($router);
                $isUp = $mk->verificarConexion();
            } catch (\Throwable $e) {
                $isUp = false;
            }

            Cache::put($router->connectivityCacheKey(), [
                'up' => $isUp,
                'checked_at' => now()->toIso8601String(),
            ], now()->addHours(2));

            if ($isUp && $mk) {
                $this->recordMetric($router, $mk);
            }

            $failsKey = "mk_monitor_fails_{$router->id}";
            $alertedKey = "mk_monitor_alerted_{$router->id}";

            if ($isUp) {
                Cache::forget($failsKey);

                if (Cache::pull($alertedKey, false)) {
                    $telegram->sendRouterConnectivity($router, true);
                }

                continue;
            }

            $fails = (int) Cache::get($failsKey, 0) + 1;
            Cache::put($failsKey, $fails, now()->addHours(2));

            if ($fails === self::FAILS_BEFORE_ALERT) {
                Cache::put($alertedKey, true, now()->addHours(2));
                $telegram->sendRouterConnectivity($router, false);
            }
        }

        return self::SUCCESS;
    }

    private function recordMetric(Router $router, MikrotikService $mk): void
    {
        $info = $mk->getResourceInfo();

        if (! $info) {
            return;
        }

        RouterMetric::create([
            'router_id' => $router->id,
            'cpu_load' => (int) ($info['cpu-load'] ?? 0),
            'mem_used' => (int) ($info['total-memory'] ?? 0) - (int) ($info['free-memory'] ?? 0),
            'mem_total' => (int) ($info['total-memory'] ?? 0),
            'disk_used' => (int) ($info['total-hdd-space'] ?? 0) - (int) ($info['free-hdd-space'] ?? 0),
            'disk_total' => (int) ($info['total-hdd-space'] ?? 0),
            'uptime' => $info['uptime'] ?? null,
            'recorded_at' => now(),
        ]);

        RouterMetric::where('router_id', $router->id)
            ->where('recorded_at', '<', now()->subDays(self::METRICS_RETENTION_DAYS))
            ->delete();
    }
}
