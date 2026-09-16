<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Services\MikrotikService;
use App\Services\TelegramNotifierService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MonitorMikrotikConnectivityCommand extends Command
{
    protected $signature = 'mk:monitor-connectivity';

    protected $description = 'Verifica la conectividad de los Mikrotik con túnel activo y avisa por Telegram si se cae o se recupera';

    /**
     * Cuántas corridas seguidas fallidas hacen falta antes de avisar,
     * para no alertar por un timeout puntual del router.
     */
    private const FAILS_BEFORE_ALERT = 2;

    public function handle(TelegramNotifierService $telegram): int
    {
        $routers = Router::withoutGlobalScopes()
            ->where('ip', 'like', '10.100.100.%')
            ->get();

        foreach ($routers as $router) {
            try {
                $isUp = MikrotikService::forRouter($router)->verificarConexion();
            } catch (\Throwable $e) {
                $isUp = false;
            }

            Cache::put($router->connectivityCacheKey(), [
                'up' => $isUp,
                'checked_at' => now()->toIso8601String(),
            ], now()->addHours(2));

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
}
