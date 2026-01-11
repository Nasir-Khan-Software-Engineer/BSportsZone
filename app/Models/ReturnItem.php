<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;

    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'sales_item_id',
        'qty',
        'is_sellable',
        'unit_price',
    ];

    protected $casts = [
        'qty' => 'integer',
        'is_sellable' => 'boolean',
        'unit_price' => 'decimal:2',
    ];

    public function return(): BelongsTo
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function salesItem(): BelongsTo
    {
        return $this->belongsTo(Sales_items::class, 'sales_item_id');
    }
}
