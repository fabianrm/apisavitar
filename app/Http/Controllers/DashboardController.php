<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Invoice;

class DashboardController extends Controller
{

    public function getSummary()
    {
        // Número de clientes
        //$activeCustomers = Customer::where('status', 1)->count();

        // Número de clientes activos
        $activeCustomers = Customer::whereHas('services', function ($query) {
            $query->where('status', 'activo');
        })->count();

        // Número de planes activos
        $activePlans = Plan::whereHas('services', function ($query) {
            $query->where('status', 'activo');
        })->count();

        // Número de contratos activos
        //$activeServices = Service::where('status', 'activo')->count();

        // Número de contratos activos
        $pendingInvoices = Invoice::where('status', 'pendiente')->count();

        // Número de facturas vencidas
        $overdueInvoices = Invoice::where('status', 'vencida')->count();

        return response()->json([
            'data' => [
                'activeCustomers' => $activeCustomers,
                'activePlans' => $activePlans,
                'overdueInvoices' => $overdueInvoices,
                'pendingInvoices' => $pendingInvoices,
            ]
        ]);
    }
}
