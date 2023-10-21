<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = [
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'member', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'maid', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert the data into the roles table
        UserRoles::insert($roles);
    }
}
