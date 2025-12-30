<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    use HasFactory;

    protected $table = 'sms_histories';

    protected $fillable = [
        'POSID',
        'to_number',
        'from_number',
        'source',
        'message_length',
        'sms_count',
    ];

    // Relation to POS/account
    public function account()
    {
        return $this->belongsTo(Accountinfo::class, 'POSID', 'POSID');
    }
}
