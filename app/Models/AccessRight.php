<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRight extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'route_name', 'short_id', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_access_right');
    }

}
