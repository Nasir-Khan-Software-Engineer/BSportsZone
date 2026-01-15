<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLifecycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'POSID',
        'sales_id',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
