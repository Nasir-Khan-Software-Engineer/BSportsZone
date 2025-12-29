<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\LoyaltyCard;
class LoyaltyCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Take 2 customers
        $customers = Customer::take(2)->get();

        foreach ($customers as $customer) {
            // Numeric card number: 12 digits
            $cardNumber = mt_rand(100000000000, 999999999999);

            LoyaltyCard::create([
                'customer_id' => $customer->id,
                'posid' => 1,
                'card_number' => $cardNumber,
                'valid_until' => Carbon::now()->addYear(),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
