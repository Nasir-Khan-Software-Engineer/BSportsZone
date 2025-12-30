<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posId = 1; // Default POSID
        $createdBy = 1; // Default admin user ID
        
        // Get all designations for the POSID
        $designations = EmployeeDesignation::where('POSID', $posId)->get();
        
        if ($designations->isEmpty()) {
            $this->command->warn('No designations found. Please run EmployeeDesignationSeeder first.');
            return;
        }
        
        $employeeCounter = 1;
        
        foreach ($designations as $designation) {
            // Determine number of employees to create
            $count = strtolower($designation->name) === 'staff' ? 15 : 2;
            
            for ($i = 1; $i <= $count; $i++) {
                // Generate date of birth (between 18 and 60 years ago)
                $age = rand(18, 60);
                $dateOfBirth = Carbon::now()->subYears($age)->subDays(rand(0, 365));
                
                // Generate hire date (between 1 month and 5 years ago)
                $hireDate = Carbon::now()->subMonths(rand(1, 60));
                
                // Random gender
                $genders = ['Male', 'Female', 'Other'];
                $gender = $genders[array_rand($genders)];
                
                // Job title based on designation
                $jobTitle = $designation->name;
                
                Employee::create([
                    'POSID' => $posId,
                    'name' => 'Demo Employee ' . $employeeCounter,
                    'date_of_birth' => $dateOfBirth->format('Y-m-d'),
                    'gender' => $gender,
                    'designation_id' => $designation->id,
                    'job_title' => $jobTitle,
                    'hire_date' => $hireDate->format('Y-m-d'),
                    'status' => 'Active',
                    'note' => 'Demo employee for testing purposes',
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy,
                ]);
                
                $employeeCounter++;
            }
            
            $this->command->info("Created {$count} employees for designation: {$designation->name}");
        }
        
        $this->command->info("✅ Total employees created: " . ($employeeCounter - 1));
    }
}
