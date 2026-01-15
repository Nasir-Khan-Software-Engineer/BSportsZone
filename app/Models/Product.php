<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'POSID',
        'code',
        'name',
        'slug',
        'type',
        'unit_id',
        'brand_id',
        'image',
        'price',
        'description',
        'discount_type',
        'discount_value',
        'staff_id',
        'created_by',
        'updated_by',
        'seo_keyword',
        'seo_description',
        'is_published',
    ];

    protected $casts = [
        'price' => 'double',
        'discount_value' => 'double',
        'is_published' => 'boolean',
    ];

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

    public function salesItemProducts(){
        return $this->hasMany(Sales_items::class, 'product_id')->where('type', 'Product');
    }

    public function salesItemServices(){
        return $this->hasMany(Sales_items::class, 'product_id')->where('type', 'Service');
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

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function defaultImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_default', true);
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

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_related_products', 'product_id', 'related_product_id')
            ->withTimestamps();
    }

    public function relatedToProducts()
    {
        return $this->belongsToMany(Product::class, 'product_related_products', 'related_product_id', 'product_id')
            ->withTimestamps();
    }
}
