<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Purchase_items extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchase_id');
    }

    public function items()
    {
        return $this->hasMany(Purchase_items::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function beautician()
    {
        return $this->belongsTo(Employee::class, 'beautician_id');
    }
}
