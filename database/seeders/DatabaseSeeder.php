<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        /*  $this->call(CustomerSeeder::class);
            $this->call(PlanSeeder::class);
            $this->call(RouterSeeder::class);
            $this->call(BoxSeeder::class); */

        // $this->call([
        //     RouterSeeder::class,
        //     BoxSeeder::class,
        //     PlanSeeder::class,
        //     CustomerSeeder::class,
        //     ServiceSeeder::class
        // ]);

        $this->call([
            NumberSeeder::class,
            CitySeeder::class,
            AdminUserSeeder::class,
            ReasonSeeder::class,

        ]);


    }
}
