<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function () {

    Route::patch('services/{contract}/update-plan', [ServiceController::class, 'updatePlan']);
    Route::post('invoices/generate', [InvoiceController::class, 'generateInvoicesMonth']);
    Route::get('invoices/search', [InvoiceController::class, 'searchInvoices']);
    Route::get('invoices/export', [InvoiceController::class, 'exportInvoices']);

    Route::get('services/by-customer/{customer_id}', [ServiceController::class, 'getServicesByCustomer']);
    Route::get('/customers-with-contracts', [CustomerController::class, 'getCustomersWithContracts']);

   // Route::get('invoices/searchq', [InvoiceController::class, 'searchInvoices2']);

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('boxs', BoxController::class);
    Route::apiResource('routers', RouterController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('equipments', EquipmentController::class);
    
    Route::get('/ports/{box_id}', [ServiceController::class, 'getPorts']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
  

});
