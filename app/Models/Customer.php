<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchases::class,'customerId');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function loyaltyCards() {
        return $this->hasMany(LoyaltyCard::class, 'customer_id');
    }

    public function latestCard()
    {
        return $this->belongsTo(LoyaltyCard::class, 'latest_card_id');
    }
}
