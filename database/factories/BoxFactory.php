<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Box>
 */
class BoxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $ptos = ($this->faker->randomDigitNotZero()) + 5;
        $ptosDisponibles = $ptos - 3;

        return [
            'name' => $this->faker->userName(),
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'reference' => $this->faker->word(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'total_ports' => $ptos,
            'available_ports' => $ptosDisponibles,
            'status' => $this->faker->boolean(true),
        ];
    }
}
