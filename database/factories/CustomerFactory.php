<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('es_ES');

        // Generar un tipo de cliente aleatorio
        $typeCustomer = $faker->randomElement(['natural', 'juridica']);
        // Generar un documento de identidad basado en el tipo de cliente
        $documentoIdentidad = '';
        if ($typeCustomer === 'natural') {
            $documentoIdentidad = $faker->numberBetween(10000000, 99999999); // DNI de 8 dígitos
        } else {
            $documentoIdentidad = $faker->numberBetween(10000000000, 99999999999); // RUC de 11 dígitos
        }

        $nameCustomer = $typeCustomer === 'natural' ? $faker->name() : $faker->company();


        return [
            'type' => $typeCustomer,
            'document_number' => $documentoIdentidad,
            'name' => $nameCustomer,
            'address' => $faker->streetAddress(),
            'phone_number' => $faker->phoneNumber(),
            'email' => $faker->email(),
            'status' => $faker->boolean(),

        ];
    }
}
