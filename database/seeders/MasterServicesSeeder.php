<?php

namespace Database\Seeders;

use App\Models\MasterServices;
use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['service_name' => 'Cooking', 'created_at' => now(), 'updated_at' => now()],
            ['service_name' => 'Cleaning', 'created_at' => now(), 'updated_at' => now()],
            ['service_name' => 'Gardening', 'created_at' => now(), 'updated_at' => now()],
            ['service_name' => 'Laundry', 'created_at' => now(), 'updated_at' => now()],
            ['service_name' => 'Babysitting', 'created_at' => now(), 'updated_at' => now()],
            ['service_name' => 'Washing', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert the data into the roles table
        MasterServices::insert($roles);
    }
}
