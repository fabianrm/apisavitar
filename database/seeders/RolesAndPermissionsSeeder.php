<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = Role::create(['name' => 'Administrador']);
        $user = Role::create(['name' => 'Usuario']);

        $dashboard = Permission::create(['name' => 'Inicio', 'icon' => 'home', 'route' => '/dashboard/home/home']);
        $factibilidad = Permission::create(['name' => 'Factibilidad técnica', 'icon' => 'wifi_tethering', 'route' => '/dashboard/factibillity/factibillity-info']);
        $customers = Permission::create(['name' => 'Clientes', 'icon' => 'people', 'route' => '/dashboard/customer/customers']);
        $plans = Permission::create(['name' => 'Planes', 'icon' => 'cast_connected', 'route' => '/dashboard/plan/plans']);
        $routers = Permission::create(['name' => 'Routers', 'icon' => 'router', 'route' => '/dashboard/router/routers']);
        $boxs = Permission::create(['name' => 'Cajas', 'icon' => 'inbox', 'route' => '/dashboard/box/boxes']);
        $equipment = Permission::create(['name' => 'Equipos', 'icon' => 'devices', 'route' => '/dashboard/equipment/equipments']);
        $contracts = Permission::create(['name' => 'Contratos', 'icon' => 'alternate_email', 'route' => '/dashboard/contract/contracts']);
        $invoices = Permission::create(['name' => 'Facturación', 'icon' => 'credit_card', 'route' => '/dashboard/invoices/invoices']);

        $expenses = Permission::create(['name' => 'Gastos', 'icon' => 'paid', 'route' => '']);
        $fixes = Permission::create(['name' => 'Fijos', 'icon' => 'payments', 'route' => '/dashboard/expenses/fixes', 'parent_id' => $expenses->id]);
        $variables = Permission::create(['name' => 'Variables', 'icon' => 'paid', 'route' => '/dashboard/expenses/variables', 'parent_id' => $expenses->id]);

        $reports = Permission::create(['name' => 'Reportes', 'icon' => 'bar_chart', 'route' => '']);
        $incomeReport = Permission::create(['name' => 'Ingresos', 'icon' => 'area_chart', 'route' => '/dashboard/reports/report', 'parent_id' => $reports->id]);
        $monthlySales = Permission::create(['name' => 'Ventas por mes', 'icon' => 'date_range', 'route' => '/dashboard/reports/monthly-sales', 'parent_id' => $reports->id]);

        $admin->permissions()->attach(Permission::all());
    }
}
