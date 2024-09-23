<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Material;
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

    //Módulo ISP
    Route::get('invoices/search', [InvoiceController::class, 'searchInvoices']);
    Route::get('invoices/export', [InvoiceController::class, 'exportInvoices']);
    Route::get('services/by-customer/{customer_id}', [ServiceController::class, 'getServicesByCustomer']);
    Route::get('customers-with-contracts', [CustomerController::class, 'getCustomersWithContracts']);
    Route::get('export-customers', [CustomerController::class, 'exportCustomers']);
    Route::get('invoices/{id}/receipt', [InvoiceController::class, 'generateReceiptPDF']);

    Route::get('entries/stock', [EntryDetailController::class, 'getStockSummary']);


    //Modulo Soporte
    Route::apiResource('support/update-status', TicketController::class);


    //Rutas autenticadas
    Route::middleware(['auth:sanctum'])->group(function () {


        Route::get('customers/check-exists', [CustomerController::class, 'checkIfExistsByDocumentNumber']);
        Route::get('services/check-equipment', [ServiceController::class, 'checkServicesByEquipment']);
        Route::patch('services/{contract}/update-plan', [ServiceController::class, 'updatePlan']);
        Route::patch('services/{id}/update-box-port', [ServiceController::class, 'updateBoxAndPort']);
        Route::patch('services/{id}/suspend', [ServiceController::class, 'suspend']);
        Route::patch('services/{id}/update-equipment', [ServiceController::class, 'updateEquipment']);
        Route::patch('customer/{id}/suspend', [CustomerController::class, 'suspend']);
        Route::post('materials/uploadfile', [MaterialController::class, 'uploadFile']);

        Route::get('/user/permissions', [PermissionController::class, 'getUserPermissions']);

        Route::post('invoices/generate', [InvoiceController::class, 'generateInvoicesMonth']);
        Route::post('invoices/generateByService/{id}', [InvoiceController::class, 'generateInvoicesByService']);
        Route::get('invoices/paid-report', [InvoiceController::class, 'getPaidInvoicesReport']);
        Route::patch('invoices/{id}/cancel-invoice', [InvoiceController::class, 'cancelInvoice']);
        Route::post('expenses/generate-next-month', [ExpenseController::class, 'generateNextMonthFixedExpenses']);
        Route::get('expenses/report', [ExpenseController::class, 'expenseReport']);

        Route::apiResource('services', ServiceController::class);
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('reasons', ReasonController::class);
        Route::apiResource('brands', BrandController::class);
        // Route::apiResource('users', AuthController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::get('summary', [DashboardController::class, 'getSummary']);

        //Módulo almacen

        Route::apiResource('materials', MaterialController::class);
        Route::apiResource('entries', EntryController::class);
        Route::apiResource('outputs', OutputController::class);

        //Modulo Soporte - Auth
        Route::patch('tickets/{ticketId}/assign-technician', [TicketController::class, 'assignTechnician']);
        Route::patch('tickets/{ticketId}/update-status', [TicketController::class, 'updateStatus']);
        Route::post('tickets/{ticketId}/attachments', [TicketController::class, 'addAttachment']);
        Route::get('tickets/{ticketID}/attachments', [TicketController::class, 'getAttachments']);

        Route::apiResource('support', TicketController::class);
        Route::apiResource('categories-support', CategoryTicketController::class);
    });

    Route::apiResource('users', AuthController::class);

    Route::get('/destination-use/{destinationId}', [DestinationController::class, 'getMaterialsByDestination']);

    //Route::apiResource('outputs', OutputController::class);
    Route::apiResource('destinations', DestinationController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('entry-details', EntryDetailController::class);


    Route::get('ports/{box_id}', [ServiceController::class, 'getPorts']);

    Route::apiResource('plans', PlanController::class);
    Route::apiResource('boxs', BoxController::class);
    Route::apiResource('routers', RouterController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('equipments', EquipmentController::class);
    Route::apiResource('enterprises', EnterpriseController::class);

    //Módulo almacen
    //Route::apiResource('materials', MaterialController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('presentations', PresentationController::class);
    Route::apiResource('warehouses', WarehouseController::class);

    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('documents', DocumentController::class);
    Route::apiResource('entry-types', EntryTypeController::class);

    //Roles
    Route::post('roles/{roleId}/permissions', [PermissionRoleController::class, 'assignPermissionsToRole']);
    Route::get('roles/{roleId}/permissions', [PermissionRoleController::class, 'getPermissionsByRole']);
    Route::delete('roles/{roleId}/permissions/{permissionId}', [PermissionRoleController::class, 'removePermissionFromRole']);

    //Login
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('role-user', RoleUserController::class);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
