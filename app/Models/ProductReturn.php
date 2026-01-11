<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'returns';

    protected $fillable = [
        'POSID',
        'customer_id',
        'sale_id',
        'reason',
        'note',
        'status',
        'total_amount',
        'total_payable_atm',
        'adjustment_amt',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_payable_atm' => 'decimal:2',
        'adjustment_amt' => 'decimal:2',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReturnPayment::class, 'return_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
