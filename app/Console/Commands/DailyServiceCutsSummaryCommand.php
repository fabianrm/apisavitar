<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\Suspension;
use App\Services\TelegramNotifierService;
use Illuminate\Console\Command;

class DailyServiceCutsSummaryCommand extends Command
{
    protected $signature = 'billing:daily-cuts-summary';

    protected $description = 'Envía a Telegram el resumen diario de suspensiones y cortes de servicio';

    public function handle(TelegramNotifierService $telegram): int
    {
        $today = now()->startOfDay();

        $suspensions = Suspension::withoutGlobalScopes()
            ->whereDate('created_at', $today)
            ->with(['service.customers', 'service.routers'])
            ->get()
            ->pluck('service')
            ->filter();

        $terminations = Service::withoutGlobalScopes()
            ->where('status', 'terminado')
            ->whereDate('end_date', $today)
            ->with(['customers', 'routers'])
            ->get();

        $telegram->sendDailyServiceCutsSummary($suspensions, $terminations);

        return self::SUCCESS;
    }
}
