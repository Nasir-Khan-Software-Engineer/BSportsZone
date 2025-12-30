<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(Sales_items::class, 'sales_id');
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
        static::deleting(function ($sales) {
            if ($sales->isForceDeleting()) {
                // Permanently delete related records
                $sales->items()->forceDelete();
                $sales->payments()->forceDelete();
            } else {
                // Soft delete related records
                $sales->items()->delete();
                $sales->payments()->delete();
            }
        });

        static::restoring(function ($sales) {
            // Restore related records
            $sales->items()->withTrashed()->restore();
            $sales->payments()->withTrashed()->restore();
        });
    }

    // loyalty history 
    public function loyaltyHistories()
    {
        return $this->hasMany(LoyaltyHistory::class, 'sales_id', 'id');
    }
}
