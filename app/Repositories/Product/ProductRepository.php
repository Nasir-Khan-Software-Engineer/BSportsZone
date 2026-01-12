<?php

namespace App\Repositories\Product;
use App\Models\Product;

class ProductRepository implements IProductRepository
{
    public function searchProduct($posId, $serviceName = null, $categoryId = null)
    {
        return Product::with([
                'variations' => fn ($q) => $q->active(),
                'TodaysStaff:id,name'
            ])
            ->select(
                'products.id',
                'products.name',
                'products.code',
                'products.POSID',
                'products.image',
                'products.staff_id',
                'products.type',
                'products.updated_at'
            )
            ->where('products.POSID', $posId)
            ->where('products.type', 'Product')

            // search by name or code
            ->when($serviceName, function ($query, $serviceName) {
                $query->where(function ($q) use ($serviceName) {
                    $q->where('products.name', 'like', "%{$serviceName}%")
                    ->orWhere('products.code', 'like', "%{$serviceName}%");
                });
            })

            // filter by category
            ->when(!empty($categoryId) && $categoryId != 0, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })

            ->orderBy('products.updated_at', 'desc')
            ->limit(30)
            ->get()

            // flatten variations as products
            ->flatMap(function ($product) {
                return $product->variations->map(function ($variation) use ($product) {
                    return (object) [
                        'id'           => $product->id,
                        'code'         => $product->code,
                        'variation_id' => $variation->id,
                        'name'         => $product->name,
                        'type'         => $product->type,
                        'posid'        => $product->POSID,
                        'image'        => $product->image,

                        // variation data
                        'stock'        => $variation->stock ?? 0,
                        'price'        => $variation->selling_price ?? 0,
                        'tagline'      => $variation->tagline,
                        'discount_type'     => $variation->discount_type ?? "Fixed",
                        'discount_value'    => $variation->discount_value ?? 0,

                        // optional staff (POS usage)
                        'staff'        => $product->TodaysStaff->name ?? null,
                        'staff_id'     => $product->staff_id ?? null,
                    ];
                });
            })
            ->values();
    }

}
