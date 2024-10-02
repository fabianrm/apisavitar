<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Invoice;
use Carbon\Carbon;

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
        $activePlans = Plan::where('status', true)->count();

        // Número de pagos del día
        $totalPaidDay = Invoice::whereDate('paid_dated', Carbon::today())
            ->where('status', 'pagada')
            ->count();

        // Número de pagos del día
        $paidDaySum = Invoice::whereDate('paid_dated', Carbon::today())
            ->where('status', 'pagada')
            ->sum('amount');

        // Número de pagos del mes
        $paidMonthSum = Invoice::whereMonth('paid_dated', Carbon::now()->month) // Mes actual
            ->whereYear('paid_dated', Carbon::now()->year)   // Año actual
            ->where('status', 'pagada')
            ->sum('amount');

        // Pagos pendientes y vencidos del año
        $overduePaidSum = Invoice::whereIn('status', ['pendiente', 'vencida'])
            ->sum('price');


        // Número de facturas pendientes
        $pendingInvoices = Invoice::where('status', 'pendiente')->count();

        // Número de facturas vencidas
        $overdueInvoices = Invoice::where('status', 'vencida')->count();

        return response()->json([
            'data' => [
                'activeCustomers' => $activeCustomers,
                'activePlans' => $activePlans,
                'overdueInvoices' => $overdueInvoices,
                'pendingInvoices' => $pendingInvoices,
                'totalPaidDay' => $totalPaidDay,
                'paidDaySum' => $paidDaySum,
                'paidMonthSum' => $paidMonthSum,
                'overduePaidSum' => $overduePaidSum,
            ]
        ]);
    }
}
