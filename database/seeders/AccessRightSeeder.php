<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccessRight;

class AccessRightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accessRights = [

            // POS
            [
                'title' => 'POS -> Access POS Terminal',
                'route_name' => 'pos.index',
                'short_id' => 'pos_terminal',
                'description' => 'Allows viewing POS terminal',
            ],

            // Services
            [
                'title' => 'Service-> View Service List',
                'route_name' => 'service.index',
                'short_id' => 'service_view',
                'description' => 'Allows viewing service list',
            ],
            [
                'title' => 'Service-> View Service Details',
                'route_name' => 'service.show',
                'short_id' => 'service_show',
                'description' => 'Allows viewing service details',
            ],
            [
                'title' => 'Service-> Store Service',
                'route_name' => 'service.store',
                'short_id' => 'service_store',
                'description' => 'Allows storing new service',
            ],
            [
                'title' => 'Service-> Edit Service',
                'route_name' => 'service.edit',
                'short_id' => 'service_edit',
                'description' => 'Allows editing service',
            ],
            [
                'title' => 'Service-> Update Service',
                'route_name' => 'service.update',
                'short_id' => 'service_update',
                'description' => 'Allows updating service',
            ],
            [
                'title' => 'Service-> Delete Service',
                'route_name' => 'service.destroy',
                'short_id' => 'service_delete',
                'description' => 'Allows deleting service',
            ],

            // Service Brands
            [
                'title' => 'Services->Brands-> View Brands List',
                'route_name' => 'service.brand.index',
                'short_id' => 'brand_view',
                'description' => 'Allows viewing brands list',
            ],
            [
                'title' => 'Services->Brands-> Store Brand',
                'route_name' => 'service.brand.store',
                'short_id' => 'brand_store',
                'description' => 'Allows storing new brand',
            ],
            [
                'title' => 'Services->Brands-> Update Brand',
                'route_name' => 'service.brand.update',
                'short_id' => 'brand_update',
                'description' => 'Allows updating brand',
            ],
            [
                'title' => 'Services->Brands-> Delete Brand',
                'route_name' => 'service.brand.destroy',
                'short_id' => 'brand_delete',
                'description' => 'Allows deleting brand',
            ],

            // Service Categories
            [
                'title' => 'Services->Categories-> View Categories List',
                'route_name' => 'service.category.index',
                'short_id' => 'category_view',
                'description' => 'Allows viewing category list',
            ],
            [
                'title' => 'Services->Categories-> Store New Category',
                'route_name' => 'service.category.store',
                'short_id' => 'category_store',
                'description' => 'Allows storing new categories',
            ],
            [
                'title' => 'Services->Categories-> Update Category',
                'route_name' => 'service.category.update',
                'short_id' => 'category_update',
                'description' => 'Allows updating categories',
            ],
            [
                'title' => 'Services->Categories-> Delete Category',
                'route_name' => 'service.category.destroy',
                'short_id' => 'category_delete',
                'description' => 'Allows deleting categories',
            ],

            // Service Units
            [
                'title' => 'Services->Units-> View Units List',
                'route_name' => 'service.unit.index',
                'short_id' => 'unit_view',
                'description' => 'Allows viewing service units',
            ],
            [
                'title' => 'Services->Units-> Store New Unit',
                'route_name' => 'service.unit.store',
                'short_id' => 'unit_store',
                'description' => 'Allows storing new unit',
            ],
            [
                'title' => 'Services->Units-> Update Unit',
                'route_name' => 'service.unit.update',
                'short_id' => 'unit_update',
                'description' => 'Allows updating unit',
            ],
            [
                'title' => 'Services->Units-> Delete Unit',
                'route_name' => 'service.unit.destroy',
                'short_id' => 'unit_delete',
                'description' => 'Allows deleting unit',
            ],

            // Customers
            [
                'title' => 'Sales->Customers-> View Customers List',
                'route_name' => 'sales.customer.index',
                'short_id' => 'customer_view',
                'description' => 'Allows viewing customers list',
            ],
            [
                'title' => 'Sales->Customers-> Store New Customer',
                'route_name' => 'sales.customer.store',
                'short_id' => 'customer_store',
                'description' => 'Allows storing new customer',
            ],
            [
                'title' => 'Sales->Customers-> View Customer Details',
                'route_name' => 'sales.customer.details',
                'short_id' => 'customer_details',
                'description' => 'Allows viewing customer details',
            ],
            [
                'title' => 'Sales->Customers-> Edit Customer',
                'route_name' => 'sales.customer.edit',
                'short_id' => 'customer_edit',
                'description' => 'Allows editing customer',
            ],
            [
                'title' => 'Sales->Customers-> Update Customer',
                'route_name' => 'sales.customer.update',
                'short_id' => 'customer_update',
                'description' => 'Allows updating customer',
            ],
            [
                'title' => 'Sales->Customers-> Delete Customer',
                'route_name' => 'sales.customer.destroy',
                'short_id' => 'customer_delete',
                'description' => 'Allows deleting customer',
            ],
            [
                'title' => 'Sales->Customers-> Show Phone Number',
                'route_name' => 'show_phone',
                'short_id' => 'show_phone',
                'description' => 'Allows viewing full customer phone numbers',
            ],
            
            // loyalty 
            [
                'title' => 'Sales->Customers-> Loyalty Details',
                'route_name' => 'sales.customer.loyalty',
                'short_id' => 'loyalty_details',
                'description' => 'Allows viewing customer loyalty details, includeing history.',
            ],

            [
                'title' => 'Sales->Customers-> Loyalty Card Store',
                'route_name' => 'sales.customer.loyalty.cards.store',
                'short_id' => 'loyalty_card_store',
                'description' => 'Allows storing new loyalty card',
            ],

            [
                'title' => 'Sales->Customers-> Loyalty Card Update',
                'route_name' => 'sales.customer.loyalty.cards.update',
                'short_id' => 'loyalty_card_update',
                'description' => 'Allows updating loyalty card',
            ],

            // Sales
            [
                'title' => 'Sales-> View Sales List',
                'route_name' => 'sales.sale.index',
                'short_id' => 'sale_view',
                'description' => 'Allows viewing sales',
            ],
            [
                'title' => 'Sales-> View Sale Details',
                'route_name' => 'sales.sale.show',
                'short_id' => 'sale_show',
                'description' => 'Allows viewing sale details',
            ],
            [
                'title' => 'Sales-> Delete Sale',
                'route_name' => 'sales.sale.destroy',
                'short_id' => 'sale_delete',
                'description' => 'Allows deleting sale',
            ],

            // Reports
            [
                'title' => 'Reports->Sales-> View Sales Report(Details)',
                'route_name' => 'reports.sales.details',
                'short_id' => 'report_sales_details',
                'description' => 'Allows viewing details sales report',
            ],
            [
                'title' => 'Reports->Sales-> Download Sales Report(Details)',
                'route_name' => 'reports.sales.details.download',
                'short_id' => 'report_sales_download',
                'description' => 'Allows downloading details sales report',
            ],
            [
                'title' => 'Reports->Expense-> View Expense Report(Details)',
                'route_name' => 'reports.expense.details',
                'short_id' => 'report_expense_details',
                'description' => 'Allows viewing expense expense report',
            ],
            [
                'title' => 'Reports->Expense-> Download Expense Report(Details)',
                'route_name' => 'reports.expense.details.download',
                'short_id' => 'report_expense_download',
                'description' => 'Allows downloading details expense report',
            ],
            

            // Setup Roles
            [
                'title' => 'Setup->Role-> View Roles List',
                'route_name' => 'setup.role.index',
                'short_id' => 'role_view',
                'description' => 'Allows viewing roles list',
            ],
            [
                'title' => 'Setup->Role-> Store New Role',
                'route_name' => 'setup.role.store',
                'short_id' => 'role_store',
                'description' => 'Allows storing new roles',
            ],
            [
                'title' => 'Setup->Role-> View Role Details',
                'route_name' => 'setup.role.show',
                'short_id' => 'role_show',
                'description' => 'Allows viewing role details',
            ],
            [
                'title' => 'Setup->Role-> Edit Role',
                'route_name' => 'setup.role.edit',
                'short_id' => 'role_edit',
                'description' => 'Allows editing roles',
            ],
            [
                'title' => 'Setup->Role-> Update Role',
                'route_name' => 'setup.role.update',
                'short_id' => 'role_update',
                'description' => 'Allows updating roles',
            ],
            [
                'title' => 'Setup->Role-> Delete Role',
                'route_name' => 'setup.role.destroy',
                'short_id' => 'role_delete',
                'description' => 'Allows deleting roles',
            ],

            // Setup Users
            [
                'title' => 'Setup->User-> View Users List',
                'route_name' => 'setup.user.index',
                'short_id' => 'user_view',
                'description' => 'Allows viewing users list',
            ],
            [
                'title' => 'Setup->User-> View User Details',
                'route_name' => 'setup.user.show',
                'short_id' => 'user_show',
                'description' => 'Allows viewing user details',
            ],
            [
                'title' => 'Setup->User-> Store New User',
                'route_name' => 'setup.user.store',
                'short_id' => 'user_store',
                'description' => 'Allows storing new users',
            ],
            [
                'title' => 'Setup->User-> Update User',
                'route_name' => 'setup.user.update',
                'short_id' => 'user_update',
                'description' => 'Allows updating users',
            ],
            [
                'title' => 'Setup->User-> Delete User',
                'route_name' => 'setup.user.destroy',
                'short_id' => 'user_delete',
                'description' => 'Allows deleting users',
            ],

            // account setup
            [
              'title' => 'Setup->Account-> View Account Setup',
              'route_name' => 'setup.account.index',
              'short_id' => 'setup_account_view',
              'description' => 'Allows viewing account setup',
            ],
            [
              'title' => 'Setup->Account-> Update Company Information',
              'route_name' => 'setup.account.update',
              'short_id' => 'setup_account_update',
              'description' => 'Allows updating account setup',
            ],
            [
              'title' => 'Setup->Account-> Update POS Information',
              'route_name' => 'setup.posinfo.update',
              'short_id' => 'setup_posinfo_update',
              'description' => 'Allows updating POS information setup',
            ],


            // Utilities - Expenses Categories
            [
                'title' => 'Utilities->Expense Categories-> View Expense Categories List',
                'route_name' => 'utilities.expense.category.index',
                'short_id' => 'expense_category_view',
                'description' => 'Allows viewing expense categories list',
            ],
            [
                'title' => 'Utilities->Expense Categories-> Store New Expense Category',
                'route_name' => 'utilities.expense.category.store',
                'short_id' => 'expense_category_store',
                'description' => 'Allows storing new expense categories',
            ],
            [
                'title' => 'Utilities->Expense Categories-> Update Expense Category',
                'route_name' => 'utilities.expense.category.update',
                'short_id' => 'expense_category_update',
                'description' => 'Allows updating expense categories',
            ],
            [
                'title' => 'Utilities->Expense Categories-> Delete Expense Category',
                'route_name' => 'utilities.expense.category.destroy',
                'short_id' => 'expense_category_delete',
                'description' => 'Allows deleting expense categories',
            ],
            // Utilities - Expenses
            [
                'title' => 'Utilities->Expenses-> View Expenses List',
                'route_name' => 'utilities.expenses.index',
                'short_id' => 'expense_view',
                'description' => 'Allows viewing expenses list',
            ],
            [
                'title' => 'Utilities->Expenses-> Store New Expense',
                'route_name' => 'utilities.expenses.store',
                'short_id' => 'expense_store',
                'description' => 'Allows storing new expenses',
            ],
            [
                'title' => 'Utilities->Expenses-> Update Expense',
                'route_name' => 'utilities.expenses.update',
                'short_id' => 'expense_update',
                'description' => 'Allows updating expenses',
            ],
            [
                'title' => 'Utilities->Expenses-> Delete Expense',
                'route_name' => 'utilities.expenses.destroy',
                'short_id' => 'expense_delete',
                'description' => 'Allows deleting expenses',
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

        $this->command->info('✅ All access rights seeded successfully!');
    }
}
