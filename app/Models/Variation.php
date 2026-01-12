<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'is_default',
        'tagline',
        'description',
        'selling_price',
        'stock',
        'status',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'selling_price' => 'double',
        'stock' => 'integer',
        'discount_value' => 'double',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(\App\Models\PurchaseItem::class, 'product_variant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeNotClosed($query)
    {
        return $query->where('status', '!=', 'closed');
    }
}
