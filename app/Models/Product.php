<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public function brand(): BelongsTo{
        return $this->BelongsTo(Brand::class,'brand_id');
    }

    public function unit(): BelongsTo{
        return $this->BelongsTo(Unit::class,'unit_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function sales_items()
    {
        return $this->hasMany(Sales_items::class, 'product_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function staff()
    {
        return $this->belongsTo(Employee::class, 'staff_id');
    }

    public function TodaysStaff()
    {
        return $this->belongsTo(Employee::class, 'staff_id')
            ->activeAndPresentToday();
    }

    public function variations()
    {
        return $this->hasMany(Variation::class, 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
