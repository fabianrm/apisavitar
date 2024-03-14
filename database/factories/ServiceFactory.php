<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Router;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'router_id' => Router::factory(),
            'plan_id' => Plan::factory(),
            'box_id' => Box::factory(),
            'port_number' => $this->faker->randomDigitNotZero(),
            'registration_date' => $this->faker->dateTime(),
            'billing_date' => $this->faker->randomDigitNotZero(),
            'recurrent' => $this->faker->boolean(),
            'due_date' => $this->faker->randomDigitNotZero(),
            'address_instalation' => $this->faker->address(),
            'reference' => $this->faker->word(),
            'city' => $this->faker->city(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'is_active' => $this->faker->boolean(),
            'status' => $this->faker->boolean(),

        ];
    }
}
