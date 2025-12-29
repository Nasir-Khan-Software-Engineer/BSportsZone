<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    use HasFactory;
    protected $table = 'loyalty_settings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'posid',
        'minimum_purchase_amount',
        'validity_period_months',
        'max_visits',
        'max_visits_per_day',
        'rules_text',
        'minimum_purchase_amount_applies_for'
    ];


    public function accountInfo()
    {
        return $this->belongsTo(AccountInfo::class, 'posid', 'posid');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Optional: updater relationship
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
}
