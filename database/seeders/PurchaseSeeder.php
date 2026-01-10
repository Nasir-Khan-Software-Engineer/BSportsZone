<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $POSID = 1;
        $createdBy = 1;

        // Get the last product
        $lastProduct = Product::where('POSID', $POSID)
            ->where('type', 'Product')
            ->latest()
            ->first();

        if (!$lastProduct) {
            $this->command->warn('No product found. Please run ProductWithVariationSeeder first.');
            return;
        }

        // Get product variations
        $variations = $lastProduct->variations;
        
        if ($variations->isEmpty()) {
            $this->command->warn('No variations found for the last product. Please ensure the product has variations.');
            return;
        }

        // Get supplier (use product's supplier or get first available supplier)
        $supplierId = $lastProduct->supplier_id;
        if (!$supplierId) {
            $supplier = Supplier::where('POSID', $POSID)->first();
            if (!$supplier) {
                $this->command->warn('No supplier found. Please run SupplierSeeder first.');
                return;
            }
            $supplierId = $supplier->id;
        }

        // Create 2 purchases
        for ($i = 1; $i <= 2; $i++) {
            DB::transaction(function () use ($POSID, $createdBy, $lastProduct, $supplierId, $variations, $i) {
                // Calculate totals
                $totalQty = 0;
                $totalCostPrice = 0;
                $costPrice = 300;
                $stock = 30;

                foreach ($variations as $variation) {
                    $totalQty += $stock;
                    $totalCostPrice += $costPrice * $stock;
                }

                // Create purchase
                $purchase = new Purchase();
                $purchase->POSID = $POSID;
                $purchase->purchase_date = now()->toDateString();
                $purchase->invoice_number = 'INV-' . str_pad($i, 6, '0', STR_PAD_LEFT);
                $purchase->name = 'Purchase ' . $i . ' - ' . $lastProduct->name;
                $purchase->supplier_id = $supplierId;
                $purchase->product_id = $lastProduct->id;
                $purchase->total_qty = $totalQty;
                $purchase->total_cost_price = $totalCostPrice;
                $purchase->description = 'Purchase seeder for ' . $lastProduct->name;
                $purchase->status = 'confirmed';
                $purchase->created_by = $createdBy;
                $purchase->save();

                // Create purchase items for each variation
                foreach ($variations as $variation) {
                    $purchaseItem = new PurchaseItem();
                    $purchaseItem->purchase_id = $purchase->id;
                    $purchaseItem->product_variant_id = $variation->id;
                    $purchaseItem->cost_price = $costPrice;
                    $purchaseItem->purchased_qty = $stock;
                    $purchaseItem->unallocated_qty = $stock; // Initially same as purchased_qty
                    $purchaseItem->status = 'reserved';
                    $purchaseItem->save();
                }
            });
        }

        $this->command->info('Successfully created 2 purchases with purchase items for the last product.');
    }
}
