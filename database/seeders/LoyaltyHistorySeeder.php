<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoyaltyHistory;
use App\Models\LoyaltyCard;
use App\Models\Sales;
use Carbon\Carbon;

class LoyaltyHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cards = LoyaltyCard::all();

        foreach ($cards as $index => $card) {
            $customer_id = $card->customer_id;
            $historyCount = $index === 0 ? 5 : 10;
            $sales = Sales::where('posid', $card->posid)->where('customerId', $customer_id)->take($historyCount)->get();

            foreach ($sales as $sale) {
                LoyaltyHistory::create([
                    'posid' => $card->posid,
                    'card_id' => $card->id,
                    'sales_id' => $sale->id,
                    'discount_type' => 'Percentage',
                    'discount_value' => rand(5, 20),
                    'discount_amount' => $sale->total_amount * rand(5,20)/100,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now()->subDays(rand(1,30)),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
