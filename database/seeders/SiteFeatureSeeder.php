<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteFeature;
use App\Models\Accountinfo;

class SiteFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'feature_name' => 'SEPERATED_SERVICE_AND_SERVICE',
                'description' => 'Enable separate services and services',
                'is_active_global' => true,
            ],
            [
                'feature_name' => 'TEST_DISCOUNT_MANAGEMENT',
                'description' => 'Enable applying discounts',
                'is_active_global' => true,
            ],
            [
                'feature_name' => 'TEST_REFUND_PROCESSING',
                'description' => 'Enable refund processing for POS transactions',
                'is_active_global' => true,
            ],
            [
                'feature_name' => 'TEST_REPORT_VIEW',
                'description' => 'View sales and inventory reports',
                'is_active_global' => true,
            ],
            [
                'feature_name' => 'TEST_LOYALTY_REWARD',
                'description' => 'Manage customer loyalty points',
                'is_active_global' => true,
            ],
            [
                'feature_name' => 'ENABLED_LOYALTY',
                'description' => 'Enables customer loyalty features in this system',
                'is_active_global' => true,
            ],
        ];

        $featureIds = [];
        foreach ($features as $feature) {
            $sf = SiteFeature::updateOrCreate(
                ['feature_name' => $feature['feature_name']], 
                $feature
            );
            $featureIds[] = $sf->id;
        }

        $account = AccountInfo::find(1);
        $account->sitefeatures()->sync($featureIds);
        $this->command->info("All features assigned to POSID 1.");
    }
}
