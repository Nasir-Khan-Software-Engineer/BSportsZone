<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeReview;
use Carbon\Carbon;

class EmployeeReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posId = 1; // Default posid
        $createdBy = 1; // Default admin user ID
        
        // Remove all existing reviews
        $deletedCount = EmployeeReview::where('posid', $posId)->delete();
        $this->command->info("Deleted {$deletedCount} existing reviews.");
        
        // Get first 5 employees
        $employees = Employee::where('posid', $posId)
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();
        
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }
        
        // Review titles and details templates (5 reviews per employee)
        $reviewTemplates = [
            [
                'title' => 'Excellent Performance',
                'status' => 'positive',
                'details' => 'Employee has shown exceptional dedication and professionalism. Consistently meets deadlines and exceeds expectations.',
            ],
            [
                'title' => 'Good Team Player',
                'status' => 'positive',
                'details' => 'Works well with the team and contributes positively to group projects. Always willing to help colleagues.',
            ],
            [
                'title' => 'Needs Improvement',
                'status' => 'negative',
                'details' => 'Employee needs to improve punctuality and communication skills. Some areas require additional training.',
            ],
            [
                'title' => 'Attendance Warning',
                'status' => 'warning',
                'details' => 'Multiple late arrivals and absences noted. Please ensure regular attendance and inform in advance if unable to attend.',
            ],
            [
                'title' => 'Outstanding Customer Service',
                'status' => 'positive',
                'details' => 'Received multiple positive customer feedback. Demonstrates excellent customer service skills and maintains high standards.',
            ],
        ];
        
        $reviewCounter = 0;
        
        // Create 5 reviews for each of the first 5 employees
        foreach ($employees as $employee) {
            $this->command->info("Creating reviews for employee: {$employee->name} (ID: {$employee->id})");
            
            foreach ($reviewTemplates as $template) {
                // Generate review date (between 1 week and 6 months ago, spread out)
                $daysAgo = rand(7, 180);
                $reviewDate = Carbon::now()->subDays($daysAgo);
                
                EmployeeReview::create([
                    'posid' => $posId,
                    'employee_id' => $employee->id,
                    'review_date' => $reviewDate->format('Y-m-d'),
                    'title' => $template['title'],
                    'status' => $template['status'],
                    'details' => $template['details'],
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy,
                ]);
                
                $reviewCounter++;
            }
            
            $this->command->info("  ✅ Created 5 reviews for {$employee->name}");
        }
        
        $this->command->info("✅ Total reviews created: {$reviewCounter} (5 reviews × {$employees->count()} employees)");
    }
}
