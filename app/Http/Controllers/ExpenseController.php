<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseCollection;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;

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
        return new ExpenseResource(Expense::create($request->all()));
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
        //
    }
}
