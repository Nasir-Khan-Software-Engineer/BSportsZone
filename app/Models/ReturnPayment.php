<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'return_payments';

    protected $fillable = [
        'POSID',
        'return_id',
        'payment_method',
        'payment_via',
        'amount',
        'transaction_id',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function return(): BelongsTo
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
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
