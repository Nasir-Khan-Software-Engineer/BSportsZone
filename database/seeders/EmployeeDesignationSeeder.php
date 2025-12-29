<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeDesignation;
use App\Models\Accountinfo;

class EmployeeDesignationSeeder extends Seeder
{
    public function run(): void
    {
        // Common designations for all POS
        $designations = [
            'Beautician',
            'Receptionist',
            'Manager',
            'Accountant',
            'Cleaner',
            'Assistant',
            'Supervisor',
            'Cashier',
            'Technician',
            'Driver',
            'Janitor',
            'Security Guard',
            'Chef',
            'Waiter',
            'Waitress',
            'Cook',
            'Dishwasher',
            'Housekeeper',
        ];

        // Get all unique posids
        $posIds = Accountinfo::distinct()->pluck('posid');

        foreach ($posIds as $posId) {
            foreach ($designations as $designation) {

                EmployeeDesignation::firstOrCreate(
                    [
                        'posid' => $posId,
                        'name'  => $designation,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

            }
        }
    }
}
