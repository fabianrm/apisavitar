<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function () {

    // Rutas públicas (no autenticadas)
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::apiResource('enterprises', EnterpriseController::class);

    // Check server time
    Route::get('check-time', function () {
        return [
            'php_time' => now()->format('Y-m-d H:i:s'),
            'server_time' => DB::select('SELECT NOW() as time')[0]->time
        ];
    });

    // Rutas autenticadas
    Route::middleware(['auth:sanctum'])->group(function () {
        // Usuario y autenticación
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::delete('logout', [AuthController::class, 'logout']);
        Route::patch('users/change-pass', [AuthController::class, 'updatePassword']);

        Route::apiResource('users', AuthController::class);
        Route::get('/user/permissions', [PermissionController::class, 'getUserPermissions']);

        // Dashboard
        Route::get('summary', [DashboardController::class, 'getSummary']);

        // Módulo Clientes
        Route::prefix('customers')->group(function () {
            Route::get('check-exists', [CustomerController::class, 'checkIfExistsByDocumentNumber']);
            Route::get('by-dni', [CustomerController::class, 'getCustomerActiveStatus']);
            Route::get('with-contracts', [CustomerController::class, 'getCustomersWithContracts']);
            Route::patch('{id}/suspend', [CustomerController::class, 'suspend']);
            Route::get('export', [CustomerController::class, 'exportCustomers']);
        });
        Route::apiResource('customers', CustomerController::class);

        // Módulo Servicios
        Route::prefix('services')->group(function () {
            Route::get('by-customer/{customer_id}', [ServiceController::class, 'getServicesByCustomer']);
            Route::get('check-equipment', [ServiceController::class, 'checkServicesByEquipment']);
            Route::patch('{contract}/update-plan', [ServiceController::class, 'updatePlan']);
            Route::patch('{contract}/add-promo', [ServiceController::class, 'updatePromo']);
            Route::patch('{id}/update-box-port', [ServiceController::class, 'updateBoxAndPort']);
            Route::patch('{id}/update-vlan', [ServiceController::class, 'updateVlan']);
            Route::patch('{id}/update-equipment', [ServiceController::class, 'updateEquipment']);
            Route::patch('{id}/update-user', [ServiceController::class, 'updateUser']);
            Route::patch('{id}/finish', [ServiceController::class, 'terminate']);
        });
        Route::apiResource('services', ServiceController::class);
        Route::get('ports/{box_id}', [BoxController::class, 'getPorts']);

        // Módulo Facturación
        Route::prefix('invoices')->group(function () {
            Route::get('search', [InvoiceController::class, 'searchInvoices']);
            Route::get('recordatory', [InvoiceController::class, 'recordatory']);
            Route::get('recordatoryoverdue', [InvoiceController::class, 'recordatoryOverdue']);
            Route::get('expired-active-services', [InvoiceController::class, 'getExpiredActiveServices']);
            Route::post('reminder/{id}', [InvoiceController::class, 'markReminderSent']);
            Route::post('reminderoverdue/{id}', [InvoiceController::class, 'sendReminderOverdue']);
            Route::get('export', [InvoiceController::class, 'exportInvoices']);
            Route::get('export-resumen', [InvoiceController::class, 'exportInvoicesResumen']);
            Route::get('{id}/receipt', [InvoiceController::class, 'generateReceiptPDF']);
            Route::get('monthly-paid-amounts', [InvoiceController::class, 'getMonthlyPaidAmounts']);
            Route::post('generate', [InvoiceController::class, 'generateInvoicesMonth']);
            Route::post('generateByService/{id}', [InvoiceController::class, 'generateInvoicesByService']);
            Route::get('paid-report', [InvoiceController::class, 'getPaidInvoicesReport']);
            Route::patch('{id}/paid-invoice', [InvoiceController::class, 'paidInvoice']);
            Route::patch('{id}/cancel-invoice', [InvoiceController::class, 'cancelInvoice']);
        });
        Route::apiResource('invoices', InvoiceController::class);

        // Módulo Gastos
        Route::prefix('expenses')->group(function () {
            Route::post('generate-next-month', [ExpenseController::class, 'generateNextMonthFixedExpenses']);
            Route::get('report', [ExpenseController::class, 'expenseReport']);
        });
        Route::apiResource('expenses', ExpenseController::class);

        // Módulo Almacén
        Route::prefix('materials')->group(function () {
            Route::get('stock', [MaterialController::class, 'getStockSummary']);
            Route::post('uploadfile', [MaterialController::class, 'uploadFile']);
        });
        Route::apiResource('materials', MaterialController::class);
        Route::apiResource('entries', EntryController::class);
        Route::apiResource('outputs', OutputController::class);
        Route::apiResource('kardex', KardexController::class);
        Route::get('materials/locations/{id}', [EntryDetailController::class, 'showLocations']);
        Route::get('/destination-use/{destinationId}', [DestinationController::class, 'getMaterialsByDestination']);
        Route::apiResource('entry-details', EntryDetailController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('presentations', PresentationController::class);
        Route::apiResource('warehouses', WarehouseController::class);
        Route::apiResource('suppliers', SupplierController::class);
        Route::apiResource('documents', DocumentController::class);
        Route::apiResource('entry-types', EntryTypeController::class);
        Route::apiResource('destinations', DestinationController::class);

        // Módulo Soporte
        Route::prefix('tickets')->group(function () {
            Route::patch('{ticketId}/assign-technician', [TicketController::class, 'assignTechnician']);
            Route::patch('{ticketId}/update-status', [TicketController::class, 'updateStatus']);
            Route::post('{ticketId}/attachments', [TicketController::class, 'addAttachment']);
            Route::get('{ticketID}/attachments', [TicketController::class, 'getAttachments']);
        });
        Route::apiResource('support/update-status', TicketController::class);
        Route::apiResource('support', TicketController::class);
        Route::apiResource('categories-support', CategoryTicketController::class);

        // Módulo Suspensiones
        Route::prefix('suspensions')->group(function () {
            Route::patch('{id}/reactive', [SuspensionController::class, 'reactivation']);
        });
        Route::apiResource('suspensions', SuspensionController::class);

        //Módulo Promociones
        Route::prefix('promotions')->group(function () {});
        Route::apiResource('promotions', PromotionController::class);

        // Roles y Permisos
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('roles', RoleController::class);
        Route::patch('add-admin', [RoleUserController::class, 'addRoleUser']);
        Route::apiResource('role-user', RoleUserController::class);
        Route::prefix('roles')->group(function () {
            Route::post('{roleId}/permissions', [PermissionRoleController::class, 'assignPermissionsToRole']);
            Route::get('{roleId}/permissions', [PermissionRoleController::class, 'getPermissionsByRole']);
            Route::delete('{roleId}/permissions/{permissionId}', [PermissionRoleController::class, 'removePermissionFromRole']);
        });

        //Equipos

        // Recursos generales
        Route::apiResource('brands', BrandController::class);
        Route::get('equipments/available', [EquipmentController::class, 'available']);
        Route::apiResource('equipments', EquipmentController::class);
        Route::apiResource('boxs', BoxController::class);
        Route::apiResource('plans', PlanController::class);
        Route::apiResource('routers', RouterController::class);
        Route::apiResource('cities', CityController::class);
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('reasons', ReasonController::class);
    });
});
