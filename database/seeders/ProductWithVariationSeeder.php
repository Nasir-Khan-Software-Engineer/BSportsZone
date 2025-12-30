<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class ProductWithVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $POSID = 1;
        $createdBy = 1;

        // Get existing suppliers, brands, categories, and units
        $suppliers = Supplier::where('POSID', $POSID)->pluck('id')->toArray();
        $brands = Brand::where('POSID', $POSID)->pluck('id')->toArray();
        $categories = Category::where('POSID', $POSID)->pluck('id')->toArray();
        $units = Unit::where('POSID', $POSID)->pluck('id')->toArray();

        // Size options for taglines
        $sizeOptions = ['M Size', 'L Size', 'XL Size', 'S Size', 'XXL Size'];
        $taglineCounter = 1; // Global counter to ensure unique taglines

        // Create 20 products
        for ($i = 1; $i <= 20; $i++) {
            // Random supplier
            $supplierId = !empty($suppliers) ? $suppliers[array_rand($suppliers)] : null;
            $brandId = !empty($brands) ? $brands[array_rand($brands)] : null;
            $unitId = !empty($units) ? $units[array_rand($units)] : null;

            // Create product
            $product = new Product();
            $product->POSID = $POSID;
            $product->code = 'PRD-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $product->name = 'Product ' . $i;
            $product->type = 'Product';
            $product->price = rand(100, 5000); // Random price between 100 and 5000
            $product->description = 'This is a description for Product ' . $i;
            $product->supplier_id = $supplierId;
            $product->brand_id = $brandId;
            $product->unit_id = $unitId;
            $product->created_by = $createdBy;
            $product->save();

            // Attach random categories (1-3 categories per product)
            $productCategories = array_rand($categories, min(rand(1, 3), count($categories)));
            if (!is_array($productCategories)) {
                $productCategories = [$productCategories];
            }
            foreach ($productCategories as $categoryIndex) {
                if (isset($categories[$categoryIndex])) {
                    $product->categories()->attach($categories[$categoryIndex]);
                }
            }

            // Create 1-3 variations for each product
            $variationCount = rand(1, 3);
            $usedSizes = [];

            for ($j = 1; $j <= $variationCount; $j++) {
                // Select a unique size that hasn't been used for this product
                $availableSizes = array_diff($sizeOptions, $usedSizes);
                if (empty($availableSizes)) {
                    // If all sizes are used, use a numbered variant
                    $sizeTag = 'Variant ' . $j;
                } else {
                    $sizeTag = $availableSizes[array_rand($availableSizes)];
                    $usedSizes[] = $sizeTag;
                }

                // Ensure unique tagline globally - append unique number to maintain readability
                // Format: "M Size-1", "L Size-2", etc. to ensure global uniqueness
                $uniqueTagline = $sizeTag . '-' . $taglineCounter;
                $taglineCounter++;

                $variation = new Variation();
                $variation->product_id = $product->id;
                $variation->tagline = $uniqueTagline;
                $variation->description = 'Variation description for ' . $sizeTag . ' of ' . $product->name;
                $variation->cost_price = rand(50, max(50, (int)($product->price * 0.7))); // Cost price less than selling price
                $variation->selling_price = rand(max($variation->cost_price, (int)($product->price * 0.8)), (int)($product->price * 1.2));
                $variation->stock = rand(0, 100);
                $variation->status = 'active';
                $variation->save();
            }
        }
    }
}

