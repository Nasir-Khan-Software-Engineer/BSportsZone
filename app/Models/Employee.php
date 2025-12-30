<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'POSID',
        'name',
        'phone',
        'date_of_birth',
        'gender',
        'designation_id',
        'job_title',
        'hire_date',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
        'date_of_birth',
        'hire_date',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'date_of_birth' => 'date',
        'hire_date' => 'date',
    ];

    public function designation()
    {
        return $this->belongsTo(EmployeeDesignation::class, 'designation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function reviews()
    {
        return $this->hasMany(EmployeeReview::class, 'employee_id');
    }

    public function services()
    {
        return $this->hasMany(Product::class, 'staff_id')
                    ->where('type', 'Service');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'staff_id')
                    ->where('type', 'Product');
    }

    public function todayAttendance()
    {
        $today = Carbon::today()->format('Y-m-d');
        return $this->hasOne(Attendance::class, 'employee_id')
            ->where('attendance_date', $today)
            ->where('status', 'present');
    }

    public function scopeActiveAndPresentToday($query)
    {
        return $query
            ->where('status', 'active')
            ->whereHas('todayAttendance');
    }
}
