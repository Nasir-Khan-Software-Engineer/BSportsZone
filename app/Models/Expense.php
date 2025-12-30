<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'POSID', 'shopid', 'categoryId', 'title', 'amount', 'note', 'expenseDate', 'created_by', 'updated_by'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'categoryId', 'id');
    }
        // Created by relation
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // Updated by relation
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    // Example accessors for formatted info (optional)
    public function getCreatorNameAttribute()
    {
        return $this->creator?->name; // Assuming User has a 'name' column
    }

    public function getUpdaterNameAttribute()
    {
        return $this->updater?->name;
    }
}
