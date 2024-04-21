<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Box::factory()
        ->count(20)
        ->create();
    }
}
