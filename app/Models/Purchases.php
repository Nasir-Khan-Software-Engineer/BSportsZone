<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchases extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(Purchase_items::class, 'purchase_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments()
    {
        return $this->hasMany(SalesPayment::class, 'sales_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerId', 'id');
    }
    
    protected static function booted()
    {
        static::deleting(function ($purchase) {
            if ($purchase->isForceDeleting()) {
                // Permanently delete related records
                $purchase->items()->forceDelete();
                $purchase->payments()->forceDelete();
            } else {
                // Soft delete related records
                $purchase->items()->delete();
                $purchase->payments()->delete();
            }
        });

        static::restoring(function ($purchase) {
            // Restore related records
            $purchase->items()->withTrashed()->restore();
            $purchase->payments()->withTrashed()->restore();
        });
    }

    // loyalty history 
    public function loyaltyHistories()
    {
        return $this->hasMany(LoyaltyHistory::class, 'sales_id', 'id');
    }
}
