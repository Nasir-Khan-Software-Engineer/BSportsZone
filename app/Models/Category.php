<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    protected $fillable = [
        'POSID',
        'name',
        'slug',
        'title',
        'keyword',
        'description',
        'logo',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->where('type', 'Product');
    }

    public function services()
    {
        return $this->belongsToMany(Product::class)->where('type', 'Service');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
