<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('slug');

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['admin']->id,
                'phone' => '+2349129964046',
                'status' => 'active',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['manager']->id,
                'phone' => '+2347066423469',
                'status' => 'active',
            ],
            [
                'name' => 'Estate Manager',
                'email' => 'estate@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['estate_manager']->id,
                'phone' => '+2348012345678',
                'status' => 'active',
            ],
            [
                'name' => 'Realtor User',
                'email' => 'realtor@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['realtor']->id,
                'phone' => '+2348012345679',
                'status' => 'active',
            ],
            [
                'name' => 'Marketer User',
                'email' => 'marketer@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['marketer']->id,
                'phone' => '+2348012345680',
                'status' => 'active',
            ],
            [
                'name' => 'Field Agent',
                'email' => 'field@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['field_agent']->id,
                'phone' => '+2348012345681',
                'status' => 'active',
            ],
            [
                'name' => 'HR Officer',
                'email' => 'hr@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['hr']->id,
                'phone' => '+2348012345682',
                'status' => 'active',
            ],
            [
                'name' => 'Legal Officer',
                'email' => 'legal@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['legal_officer']->id,
                'phone' => '+2348012345683',
                'status' => 'active',
            ],
            [
                'name' => 'Client User',
                'email' => 'client@nichhomes.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['client']->id,
                'phone' => '+2348012345684',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
