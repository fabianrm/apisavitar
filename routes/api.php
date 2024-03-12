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
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('boxs', BoxController::class);
    Route::apiResource('routers', RouterController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('invoices', InvoiceController::class);

});
