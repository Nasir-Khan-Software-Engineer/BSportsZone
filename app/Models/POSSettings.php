<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSSettings extends Model
{
    use HasFactory;
        protected $table = 'pos_settings';

    protected $fillable = [
        'POSID',
        'adjustment_min',
        'adjustment_max',
        'created_by',
        'updated_by',
    ];

    // Relation to POS/account
    public function account()
    {
        return $this->belongsTo(AccountInfo::class, 'POSID', 'POSID');
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
