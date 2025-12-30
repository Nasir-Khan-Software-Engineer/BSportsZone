<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';
    
    protected $fillable = [
        'POSID', 'title', 'created_by', 'updated_by'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function expenses(){
        return $this->hasMany(Expense::class, 'categoryId');
    }
    
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updater(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
