<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AccessRightSeeder::class);
        $this->call(AccountinfoSeeder::class);
        $this->call(LoyaltySettingSeeder::class);
        $this->call(SiteFeatureSeeder::class);
        $this->call(RoleSeeder::class);
        
        $this->call(UserSeeder::class);
        $this->call(MakeAllUsersAdminSeeder::class);
        $this->call(AssignAllAdminAccessRightsSeeder::class);

        $this->call(POSSettingsSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ShopSeeder::class);
        $this->call(UnitSeeder::class);

        $this->call(EmployeeDesignationSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(EmployeeReviewSeeder::class);

        $this->call(ServiceSeeder::class);
        $this->call(ProductWithVariationSeeder::class);

        $this->call(EmployeeSmsReportAccessRightSeeder::class);
    }
}
