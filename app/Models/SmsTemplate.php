<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $table = 'sms_templates';

    protected $fillable = [
        'posid',
        'template',
        'created_by',
        'updated_by',
    ];

    // Relation to POS/account
    public function account()
    {
        return $this->belongsTo(Accountinfo::class, 'posid', 'POSID');
    }

    // Relation to user who created
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation to user who updated
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
