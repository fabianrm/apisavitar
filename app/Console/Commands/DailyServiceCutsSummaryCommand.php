<?php

namespace App\Console\Commands;

use App\Models\Enterprise;
use App\Models\Service;
use App\Models\Suspension;
use App\Services\TelegramNotifierService;
use Illuminate\Console\Command;

class DailyServiceCutsSummaryCommand extends Command
{
    protected $signature = 'billing:daily-cuts-summary';

    protected $description = 'Envía a Telegram el resumen diario de suspensiones y cortes de servicio, por empresa';

    public function handle(TelegramNotifierService $telegram): int
    {
        $today = now()->startOfDay();

        $enterprises = Enterprise::whereNotNull('telegram_bot_token')
            ->whereNotNull('telegram_chat_id')
            ->get();

        foreach ($enterprises as $enterprise) {
            $suspensions = Suspension::withoutGlobalScopes()
                ->where('enterprise_id', $enterprise->id)
                ->whereDate('created_at', $today)
                ->with(['service.customers', 'service.routers'])
                ->get()
                ->pluck('service')
                ->filter();

            $terminations = Service::withoutGlobalScopes()
                ->where('enterprise_id', $enterprise->id)
                ->where('status', 'terminado')
                ->whereDate('end_date', $today)
                ->with(['customers', 'routers'])
                ->get();

            $telegram->sendDailyServiceCutsSummary($enterprise, $suspensions, $terminations);
        }

        return self::SUCCESS;
    }
}
