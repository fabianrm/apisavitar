<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Customer::factory()
            ->count(10)
            ->hasServices(3)
            ->create();

        Customer::factory()
            ->count(5)
            ->hasServices(2)
            ->create();

        Customer::factory()
            ->count(20)
            ->hasServices(5)
            ->create();

        Customer::factory()
            ->count(15)
            ->create();
    }
}
