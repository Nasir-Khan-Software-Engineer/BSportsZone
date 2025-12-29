<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountinfo extends Model
{
    use HasFactory;

    protected $table = 'accountinfos';
    protected $primaryKey = 'POSID';
    public $incrementing = false;

    public function posSettings()
    {
        return $this->hasOne(POSSettings::class, 'posid', 'POSID');
    }

    public function sitefeatures()
    {
        return $this->belongsToMany(SiteFeature::class, 'pos_feature', 'posid', 'feature_id');
    }

    public function loyaltySettings()
    {
        return $this->hasOne(LoyaltySetting::class, 'posid', 'POSID');
    }
}
