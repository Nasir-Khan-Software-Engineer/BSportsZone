<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesPayment extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'sales_payments';
    public $incrementing = false; // because composite PK
    protected $primaryKey = null;

    protected $fillable = [
        'posid',
        'id',
        'sales_id',
        'payment_method',
        'payment_via',
        'paid_amount',
        'transaction_id',
        'note',
        'created_by',
        'updated_by',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'sales_id', 'id')
                    ->where('posid', $this->posid);
    }
    
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
