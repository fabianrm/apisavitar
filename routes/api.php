<?php
namespace App\Http\Controllers;

use App\Models\Brand;
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

    Route::get('invoices/search', [InvoiceController::class, 'searchInvoices']);
    Route::get('invoices/export', [InvoiceController::class, 'exportInvoices']);

    Route::get('services/by-customer/{customer_id}', [ServiceController::class, 'getServicesByCustomer']);
    Route::get('customers-with-contracts', [CustomerController::class, 'getCustomersWithContracts']);
    Route::get('export-customers', [CustomerController::class, 'exportCustomers']);

    Route::apiResource('brands', BrandController::class);


    //Rutas autenticadas
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('customers/check-exists', [CustomerController::class, 'checkIfExistsByDocumentNumber']);
        Route::patch('services/{contract}/update-plan', [ServiceController::class, 'updatePlan']);
        Route::post('invoices/generate', [InvoiceController::class, 'generateInvoicesMonth']);
        Route::get('invoices/paid-report', [InvoiceController::class, 'getPaidInvoicesReport']);
        Route::post('expenses/generate-next-month', [ExpenseController::class, 'generateNextMonthFixedExpenses']);
        Route::get('expenses/report', [ExpenseController::class, 'expenseReport']);

        Route::apiResource('services', ServiceController::class);
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('reasons', ReasonController::class);
      
        Route::get('summary', [DashboardController::class, 'getSummary']);

        // Otras rutas protegidas...
    });


    Route::apiResource('plans', PlanController::class);
    Route::apiResource('boxs', BoxController::class);
    Route::apiResource('routers', RouterController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('equipments', EquipmentController::class);

    Route::get('ports/{box_id}', [ServiceController::class, 'getPorts']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


});
