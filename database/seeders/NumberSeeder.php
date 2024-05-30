<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un array con los números del 1 al 16
        $numbers = range(1, 16);

        // Insertar cada número en la tabla 'numbers'
        foreach ($numbers as $number) {
            DB::table('numbers')->insert([
                'port_number' => $number,
            ]);
        }
    }
}
