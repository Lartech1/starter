<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Manager', 'slug' => 'manager'],
            ['name' => 'Estate Manager', 'slug' => 'estate_manager'],
            ['name' => 'Realtor', 'slug' => 'realtor'],
            ['name' => 'Marketer', 'slug' => 'marketer'],
            ['name' => 'Field Agent', 'slug' => 'field_agent'],
            ['name' => 'HR Officer', 'slug' => 'hr'],
            ['name' => 'Legal Officer', 'slug' => 'legal_officer'],
            ['name' => 'Client', 'slug' => 'client'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
