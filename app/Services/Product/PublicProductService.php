<?php

namespace App\Services\Product;

use Illuminate\Support\Str;

class PublicProductService implements IPublicProductService
{
    /**
     * Format products for public display
     * 
     * @param mixed $products Collection or Paginator of products
     * @return mixed Formatted products
     */
    public function formatProducts($products)
    {
        foreach ($products as $product) {
            $product->short_name = Str::limit($product->name, 30);
            
            // 1️⃣ Calculate product price after discount
            $price = $product->price;

            if ($product->discount_type && $product->discount_value) {
                if ($product->discount_type === 'percentage') {
                    $price -= ($product->price * $product->discount_value / 100);
                } else {
                    $price -= $product->discount_value;
                }
            }

            $product->price_after_discount = round(max(0, $price), 2);

            // 2️⃣ Get default variation
            $defaultVariation = $product->variations->where('is_default', 1)->first();

            if ($defaultVariation) {
                // 3️⃣ Calculate default variation price after discount
                $vPrice = $defaultVariation->selling_price;

                if ($defaultVariation->discount_type && $defaultVariation->discount_value) {
                    if ($defaultVariation->discount_type === 'percentage') {
                        $vPrice -= ($defaultVariation->selling_price * $defaultVariation->discount_value / 100);
                    } else {
                        $vPrice -= $defaultVariation->discount_value;
                    }
                }

                $defaultVariation->price_after_discount = round(max(0, $vPrice), 2);

                // 4️⃣ Attach variation info to product
                $product->default_variation_id = $defaultVariation->id;
                $product->default_variation_tagline = $defaultVariation->tagline;
                $product->default_variation_selling_price = $defaultVariation->selling_price;
                $product->default_variation_discount_type = $defaultVariation->discount_type;
                $product->default_variation_discount_value = $defaultVariation->discount_value;
                $product->default_variation_price_after_discount = $defaultVariation->price_after_discount;
            } else {
                // Fallback if no default variation exists
                $product->default_variation_id = null;
                $product->default_variation_tagline = null;
                $product->default_variation_selling_price = null;
                $product->default_variation_discount_type = null;
                $product->default_variation_discount_value = null;
                $product->default_variation_price_after_discount = null;
            }
        }

        return $products;
    }
}
