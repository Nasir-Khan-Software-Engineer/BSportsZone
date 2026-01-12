<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | SALE #1 (Fully Paid)
            |--------------------------------------------------------------------------
            */
            $sale1Id = DB::table('sales')->insertGetId([
                'POSID'           => 1,
                'invoice_code'        => 'ORD-' . Str::random(6),
                'customerId'   => 1,
                'discount_type'     => 'fixed',
                'discount_value'     => 0,

                'discount_amount' => 0,
                'total_amount'    => 1000,
                'adjustmentAmt' => 0,
                
                'payment_status'  => 'paid',
                'sale_status'     => 'completed',

                'created_by'      => 1,
                'updated_by'      => 1,
                'created_at'      => now(),
                'updated_at'      => now(),


                'sales_from' => 'offline',
            ]);

            DB::table('sales_items')->insert([
                [
                    'POSID'           => 1,
                    'sales_id'            => $sale1Id,
                    'product_id'         => 11,
                    'variation_id' => 1,
                    'variant_tagline'    => 'XXL Size-1',
                    'quantity'                => 1,
                    'selling_price'         => 250,
                    'product_price'         => 250,
                    'created_at'         => now(),
                ]
            ]);

            /*
            |--------------------------------------------------------------------------
            | SALE #2 (Partially Paid)
            |--------------------------------------------------------------------------
            */
            $sale2Id = DB::table('sales')->insertGetId([
                'POSID'           => 1,
                'invoice_code'        => 'ORD-' . Str::random(6),
                'customerId'   => 2,
                'discount_type'     => 'fixed',
                'discount_value'     => 0,

                'discount_amount' => 0,
                'total_amount'    => 1000,
                'adjustmentAmt' => 0,
                
                'payment_status'  => 'paid',
                'sale_status'     => 'completed',

                'created_by'      => 1,
                'updated_by'      => 1,
                'created_at'      => now(),
                'updated_at'      => now(),


                'sales_from' => 'offline',
            ]);

            DB::table('sales_items')->insert([
                [
                    'POSID'           => 1,
                    'sales_id'            => $sale2Id,
                    'product_id'         => 11,
                    'variation_id' => 2,
                    'variant_tagline'    => 'L Size-2',
                    'quantity'                => 1,
                    'selling_price'         => 200,
                    'product_price'         => 200,
                    'created_at'         => now(),
                ]
            ]);

        });
    }
}
