<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoyaltySetting;

class LoyaltySettingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'posid' => 1,
                'minimum_purchase_amount' => 10000,
                'validity_period_months' => 12,
                'max_visits' => 10,
                'max_visits_per_day' => 1,
                'rules_text' => 'Earn discounts on each visit. Complete all visits to receive additional benefits.',
            ],
            [
                'posid' => 2,
                'minimum_purchase_amount' => 10000,
                'validity_period_months' => 12,
                'max_visits' => 8,
                'max_visits_per_day' => 1,
                'rules_text' => 'Special loyalty privileges available for returning customers.',
            ],
            [
                'posid' => 3,
                'minimum_purchase_amount' => 10000,
                'validity_period_months' => 6,
                'max_visits' => 6,
                'max_visits_per_day' => 1,
                'rules_text' => 'Get rewarded on every purchase you make.',
            ],
        ];

        foreach ($data as $item) {
            LoyaltySetting::updateOrCreate(
                ['posid' => $item['posid']],
                $item
            );
        }
    }
}
