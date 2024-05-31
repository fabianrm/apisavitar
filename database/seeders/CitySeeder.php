<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear un array con los nombres de las ciudades
        $cities = [
            'PUEBLO NUEVO',
            'CORALES',
            'MIRAMAR',
            'VICHAYAL',
            'PUERTO PIZARRO'
        ];

        // Insertar cada ciudad en la tabla 'cities'
        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => $city,
                'status'=> 1
            ]);
        }
    }
}
