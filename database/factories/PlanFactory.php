<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $namePlan = $this->faker->randomElement(['Plan básico', 'Plan empresa']);


        if ($namePlan === 'Plan empresa') {

            $down = $this->faker->randomElement(['50M']);
            $upl = $this->faker->randomElement(['30M']);
            $pri = $this->faker->randomElement([100]);
        } else {
            $down = $this->faker->randomElement(['10M']);
            $upl = $this->faker->randomElement(['5M']);
            $pri = $this->faker->randomElement([50]);
        }



        return [
            'name' => $namePlan,
            'download' => $down,
            'upload' => $upl,
            'price' => $pri,
            'guaranteed_speed' => $this->faker->randomElement(['40']),
            'priority' => $this->faker->randomElement(['Normal']),
            'burst_limit' => $this->faker->randomElement(['0']),
            'burst_threshold' => $this->faker->randomElement(['0']),
            'burst_time' => $this->faker->randomElement(['0']),
            'status' => $this->faker->boolean(true),


        ];
    }
}
