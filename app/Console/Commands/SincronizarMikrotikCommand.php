<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class SincronizarMikrotikCommand extends Command
{
    protected $signature = 'mk:sync';
    protected $description = 'Sincroniza estados de contratos con MikroTik';

    public function handle(MikrotikService $mikrotikService)
    {
        $this->info('Iniciando sincronización de contratos...');

        $result = $mikrotikService->sincronizarEstadosContratos();

        if ($result['success']) {
            $this->info("Sincronización exitosa. Contratos procesados: {$result['processed']}");
            return 0;
        }

        $this->error("Error: {$result['error']}");
        return 1;
    }
}
