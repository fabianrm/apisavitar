<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Router>
 */
class RouterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip' => $this->faker->localIpv4(),
            'usuario' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'port' => $this->faker->randomNumber(),
            'api_connection' => $this->faker->word(),
            'status' => $this->faker->boolean(true),
        ];
    }
}
