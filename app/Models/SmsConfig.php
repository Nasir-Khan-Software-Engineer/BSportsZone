<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsConfig extends Model
{
    use HasFactory;

    protected $table = 'sms_configs';

    protected $fillable = [
        'posid',
        'base_url',
        'username',
        'api_key',
        'sender_id',
        'campaign_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
