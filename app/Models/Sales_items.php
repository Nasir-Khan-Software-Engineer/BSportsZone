<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sales_items extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function items()
    {
        return $this->hasMany(Sales_items::class, 'sales_id');
    }

    public function service()
    {
        return $this->belongsTo(Product::class, 'product_id')->where('type', 'Service');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->where('type', 'Product');
    }

    public function staff()
    {
        return $this->belongsTo(Employee::class, 'staff_id');
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
