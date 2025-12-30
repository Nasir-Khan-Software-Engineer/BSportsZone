<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDesignation extends Model
{
    use HasFactory;

    protected $fillable = [
        'POSID',
        'name',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}
