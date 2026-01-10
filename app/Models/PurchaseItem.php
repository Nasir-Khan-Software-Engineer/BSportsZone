<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_variant_id',
        'cost_price',
        'purchased_qty',
        'unallocated_qty',
        'status',
    ];

    protected $casts = [
        'cost_price' => 'double',
        'purchased_qty' => 'integer',
        'unallocated_qty' => 'integer',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'product_variant_id');
    }

    /**
     * Check if this item is editable (no allocation has occurred)
     */
    public function isEditable(): bool
    {
        return $this->purchased_qty == $this->unallocated_qty;
    }
}
