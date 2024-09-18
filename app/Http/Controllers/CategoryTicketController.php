<?php

namespace App\Http\Controllers;

use App\Models\CategoryTicket;
use App\Http\Requests\StoreCategoryTicketRequest;
use App\Http\Requests\UpdateCategoryTicketRequest;
use App\Http\Resources\CategoryTicketCollection;
use App\Http\Resources\CategoryTicketResource;

class CategoryTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoryTicket::all();
        return new CategoryTicketCollection($categories);
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
    public function store(StoreCategoryTicketRequest $request)
    {
        $category = CategoryTicket::create($request->validated());
        return new CategoryTicketResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //return new CategoryTicketResource($categoryTicket);
        $category = CategoryTicket::findOrFail($id);
        return new CategoryTicketResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryTicket $categoryTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryTicketRequest $request, $id)
    {
        $category = CategoryTicket::findOrFail($id);
        $category->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryTicket $categoryTicket)
    {
        
    }
}
