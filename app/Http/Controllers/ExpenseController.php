<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseService;
use App\Services\UtilService;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\ExpenseCollection;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::all();
        return new ExpenseCollection($expenses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {

        $expenseService = app(UtilService::class);

        // Genera un código único para el cliente
        $uniqueCode = $expenseService->generateUniqueCodeExpense('EC');

        // Almacena el nuevo cliente con el código único
        $expense = new Expense($request->all());
        $expense->expense_code = $uniqueCode;
        $expense->save();

        // Retorna una respuesta:
        return new ExpenseResource($expense);


        //return new ExpenseResource(Expense::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->all());
        return new ExpenseResource($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expenses)
    {
        $expenses->deleteOrFail();

        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Gasto eliminado correctamente'
            ]
        ]);
    }

    /**
     * Generar gastos fijos
     */
    public function generateNextMonthFixedExpenses()
    {
        $expenseService = app(ExpenseService::class);

        $result = $expenseService->generateNextMonthFixedExpenses();
        return response()->json($result);
    }


    /***
     * Reporte de egresos
     */

    public function expenseReport(Request $request)
    {
        // Validar las fechas
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->query('end_date'))->endOfDay();

        // Obtener los gastos en el rango de fechas
        $expenses = Expense::whereBetween('date_paid', [$startDate, $endDate])
            ->with('reasons') // Asegurarse de cargar la relación necesaria
            ->get();

        // Calcular la suma de la columna amount
        $totalAmount = $expenses->sum('amount');

        // Formatear la respuesta
        $expenseResource = ExpenseResource::collection($expenses);

        return response()->json([
            'data' => $expenseResource,
            'totalAmount' => $totalAmount,
        ]);
    }

 




}
