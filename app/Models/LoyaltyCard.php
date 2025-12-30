<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'POSID',
        'card_number',
        'valid_until',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'valid_until',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationships
     */

    // Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // POS AccountInfo
    public function account()
    {
        return $this->belongsTo(AccountInfo::class, 'POSID');
    }

    // Loyalty histories
    public function histories()
    {
        return $this->hasMany(LoyaltyHistory::class, 'card_id');
    }

    // Creator user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updater user
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
}
