<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {

       // Role::truncate();

        $roles = [
            [
                'POSID' => 1,
                'name' => 'Admin',
                'description' => 'Full access to all modules',
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'POSID' => 1,
                'name' => 'Receptionist',
                'description' => 'Access to sales and invoice modules only',
                'created_by' => null,
                'updated_by' => null,
            ],


            [
                'POSID' => 2,
                'name' => 'Admin',
                'description' => 'Full access to all modules',
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'POSID' => 2,
                'name' => 'Receptionist',
                'description' => 'Access to sales and invoice modules only',
                'created_by' => null,
                'updated_by' => null,
            ],

            [
                'POSID' => 3,
                'name' => 'Admin',
                'description' => 'Full access to all modules',
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'POSID' => 3,
                'name' => 'Receptionist',
                'description' => 'Access to sales and invoice modules only',
                'created_by' => null,
                'updated_by' => null,
            ]

        ]; // end roles array

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['POSID' => $role['POSID'], 'name' => $role['name']],
                $role
            );
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}
