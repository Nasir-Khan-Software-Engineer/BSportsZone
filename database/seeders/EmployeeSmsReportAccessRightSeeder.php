<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccessRight;

class EmployeeSmsReportAccessRightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accessRights = [

            // SMS Reports - All reports from Revenue to SMS
            [
                'title' => 'Reports->Discount & Adjustment-> View Discount & Adjustment Report(Summary)',
                'route_name' => 'reports.discount-adjustment.details',
                'short_id' => 'report_discount_adjustment_details',
                'description' => 'Allows viewing discount & adjustment summary report',
            ],
            [
                'title' => 'Reports->Discount & Adjustment-> Download Discount & Adjustment Report(Summary)',
                'route_name' => 'reports.discount-adjustment.details.download',
                'short_id' => 'report_discount_adjustment_download',
                'description' => 'Allows downloading discount & adjustment summary report',
            ],
            [
                'title' => 'Reports->Revenue-> View Revenue Report',
                'route_name' => 'reports.revenue.details',
                'short_id' => 'report_revenue_details',
                'description' => 'Allows viewing revenue report',
            ],
            [
                'title' => 'Reports->Revenue-> Download Revenue Report',
                'route_name' => 'reports.revenue.details.download',
                'short_id' => 'report_revenue_download',
                'description' => 'Allows downloading revenue report',
            ],
            [
                'title' => 'Reports->Net Profit-> View Net Profit Report',
                'route_name' => 'reports.net-profit.details',
                'short_id' => 'report_net_profit_details',
                'description' => 'Allows viewing net profit report',
            ],
            [
                'title' => 'Reports->Net Profit-> Download Net Profit Report',
                'route_name' => 'reports.net-profit.details.download',
                'short_id' => 'report_net_profit_download',
                'description' => 'Allows downloading net profit report',
            ],
            [
                'title' => 'Reports->Customer-> View Customer Report',
                'route_name' => 'reports.customer.details',
                'short_id' => 'report_customer_details',
                'description' => 'Allows viewing customer report',
            ],
            [
                'title' => 'Reports->Customer-> Download Customer Report',
                'route_name' => 'reports.customer.details.download',
                'short_id' => 'report_customer_download',
                'description' => 'Allows downloading customer report',
            ],
            [
                'title' => 'Reports->Employee-> View Employee Report',
                'route_name' => 'reports.employee.details',
                'short_id' => 'report_employee_details',
                'description' => 'Allows viewing employee report',
            ],
            [
                'title' => 'Reports->Employee-> Download Employee Report',
                'route_name' => 'reports.employee.details.download',
                'short_id' => 'report_employee_download',
                'description' => 'Allows downloading employee report',
            ],
            [
                'title' => 'Reports->Staff-> View Staff Report',
                'route_name' => 'reports.staff.details',
                'short_id' => 'report_staff_details',
                'description' => 'Allows viewing staff report',
            ],
            [
                'title' => 'Reports->Staff-> Download Staff Report',
                'route_name' => 'reports.staff.details.download',
                'short_id' => 'report_staff_download',
                'description' => 'Allows downloading staff report',
            ],
            [
                'title' => 'Reports->SMS-> View SMS History Report',
                'route_name' => 'reports.sms.history',
                'short_id' => 'report_sms_history',
                'description' => 'Allows viewing SMS history report',
            ],
            [
                'title' => 'Reports->SMS-> Download SMS History Report',
                'route_name' => 'reports.sms.history.download',
                'short_id' => 'report_sms_download',
                'description' => 'Allows downloading SMS history report',
            ],

            // Employee Module
            [
                'title' => 'Employee-> View Employees List',
                'route_name' => 'employee.index',
                'short_id' => 'employee_view',
                'description' => 'Allows viewing employees list',
            ],
            [
                'title' => 'Employee-> Store Employee',
                'route_name' => 'employee.store',
                'short_id' => 'employee_store',
                'description' => 'Allows storing new employee',
            ],
            [
                'title' => 'Employee-> View Employee Details',
                'route_name' => 'employee.details',
                'short_id' => 'employee_details',
                'description' => 'Allows viewing employee details',
            ],
            [
                'title' => 'Employee-> Edit Employee',
                'route_name' => 'employee.edit',
                'short_id' => 'employee_edit',
                'description' => 'Allows editing employee',
            ],
            [
                'title' => 'Employee-> Update Employee',
                'route_name' => 'employee.update',
                'short_id' => 'employee_update',
                'description' => 'Allows updating employee',
            ],
            [
                'title' => 'Employee-> Delete Employee',
                'route_name' => 'employee.destroy',
                'short_id' => 'employee_delete',
                'description' => 'Allows deleting employee',
            ],

            // Employee Attendance
            [
                'title' => 'Employee Attendance-> View Attendance Data',
                'route_name' => 'attendance.data',
                'short_id' => 'attendance_data',
                'description' => 'Allows viewing attendance data',
            ],
            [
                'title' => 'Employee Attendance-> Save Attendance',
                'route_name' => 'attendance.save',
                'short_id' => 'attendance_save',
                'description' => 'Allows saving attendance',
            ],
            [
                'title' => 'Employee Attendance-> Auto open attendance modal',
                'route_name' => 'attendance.check-today-status',
                'short_id' => 'attendance_save_today_attendance',
                'description' => 'Allows auto opening attendance modal from dashboard',
            ],

            // Employee Review
            [
                'title' => 'Employee Review-> Store Review',
                'route_name' => 'employee.review.store',
                'short_id' => 'employee_review_store',
                'description' => 'Allows storing employee review',
            ],
            [
                'title' => 'Employee Review-> Update Review',
                'route_name' => 'employee.review.update',
                'short_id' => 'employee_review_update',
                'description' => 'Allows updating employee review',
            ],
            [
                'title' => 'Employee Review-> Delete Review',
                'route_name' => 'employee.review.destroy',
                'short_id' => 'employee_review_delete',
                'description' => 'Allows deleting employee review',
            ],
        ];

        foreach ($accessRights as $right) {
            AccessRight::updateOrCreate(
                [
                    'route_name' => $right['route_name'],
                    'short_id'   => $right['short_id'],
                ],
                $right
            );
        }

        $this->command->info('✅ Employee SMS Report access rights seeded successfully!');
    }
}

