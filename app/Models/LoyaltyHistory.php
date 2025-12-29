<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'posid',
        'card_id',
        'sales_id',
        'discount_type',
        'discount_value',
        'discount_amount',
        'created_by',
        'updated_by',
        'note',
        'isSkipped'
    ];

    public function account()
    {
        return $this->belongsTo(AccountInfo::class, 'posid');
    }

    public function card()
    {
        return $this->belongsTo(LoyaltyCard::class, 'card_id');
    }

    public function sale()
    {
        return $this->belongsTo(Purchases::class, 'sales_id');
    }

    // Link to creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Link to updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
