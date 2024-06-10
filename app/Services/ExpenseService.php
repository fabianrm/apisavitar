<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{

    public function generateNextMonthFixedExpenses()
    {
        $currentDate = Carbon::now();
        $total = 0;

        // Obtener todos los códigos de gasto únicos de tipo 'fijo'
        $fixedExpenseCodes = Expense::select('expense_code')
            ->whereHas('reasons', function ($query) {
                $query->where('type', 'fijo')->where('status', true);
            })
            ->distinct()
            ->get();

        foreach ($fixedExpenseCodes as $fixedExpenseCode) {
            // Obtener la última factura generada para este código de gasto
            $lastExpense = Expense::where('expense_code', $fixedExpenseCode->expense_code)
                ->where('status', true)
                ->orderBy('date', 'desc')
                ->first();

            if (!$lastExpense) {
                // No hay gastos previos para este código de gasto, así que saltamos a la siguiente iteración
                continue;
            }

            $currentMonthExpense = Carbon::parse($lastExpense->date)->month;
            // \Log::info("currentMonthExpense=> {$currentMonthExpense}");
            $currentMonth = $currentDate->month;
            // \Log::info("currentMonth=> {$currentMonth}");

            if ($currentMonthExpense == ($currentMonth + 1)) {
                continue;
            }

            $nextMonthExpenseDate = Carbon::parse($lastExpense->date)->addMonth();

            // Verificar si ya existe una factura para este código de gasto en el mes siguiente
            $exists = Expense::where('expense_code', $fixedExpenseCode->expense_code)
                ->whereMonth('date', $nextMonthExpenseDate->month)
                ->whereYear('date', $nextMonthExpenseDate->year)
                ->exists();

            if (!$exists) {
                // Crear la nueva factura para el mes siguiente
                Expense::create([
                    'description' => $lastExpense->description,
                    'amount' => $lastExpense->amount,
                    'reason_id' => $lastExpense->reason_id,
                    'expense_code' => $lastExpense->expense_code,
                    'voutcher' => '-', // Aquí puedes ajustar según sea necesario
                    'note' => 'Factura generada automáticamente',
                    'date' => $nextMonthExpenseDate->toDateString(),
                    'date_paid' => null,
                    'status' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
                $total++;
            }
        }
        return [
           'data' => [
                'total' => $total,
                'message' => 'Fixed expenses for next month generated successfully'
           ]
        ];

    }

}
