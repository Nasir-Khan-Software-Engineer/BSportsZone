<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\POSSettings;

class POSSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        POSSettings::create([
            'POSID' => 1, // POS ID
            'adjustment_min' => -5,
            'adjustment_max' => 5,
            //'created_by' => 1, // admin user
            //'updated_by' => 1, // admin user
        ]);

        POSSettings::create([
            'POSID' => 2, // POS ID
            'adjustment_min' => -5,
            'adjustment_max' => 5,
            //'created_by' => 3, // admin user
            //'updated_by' => 3, // admin user
        ]);
       
    }
}
