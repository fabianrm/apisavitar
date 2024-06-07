<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reasons = [
            ['name' => 'COMBUSTIBLE', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'VIATICOS', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'PAGO DE SERVICIOS', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'PAGO DE INTERNET', 'type' => 'FIJO', 'status' => true],
            ['name' => 'UTILES DE OFICINA', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'RECIBOS DE CLIENTES', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'PLANILLAS', 'type' => 'FIJO', 'status' => true],
            ['name' => 'PAGO DE ALQUILER', 'type' => 'FIJO', 'status' => true],
            ['name' => 'COMPRA DE EQUIPOS', 'type' => 'VARIABLE', 'status' => true],
            ['name' => 'PEAJES', 'type' => 'VARIABLE', 'status' => true],
        ];

        DB::table('reasons')->insert($reasons);
    }
}
