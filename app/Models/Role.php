<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['POSID', 'name', 'description', 'created_by', 'updated_by'];

    public function accessRights()
    {
        return $this->belongsToMany(AccessRight::class, 'role_access_right');
    }

     // Check if role is protected (Admin)
    public function isProtected($name): bool
    {
        return strtolower($this->name) === strtolower($name);
    }

    // Override delete
    public function delete()
    {
        if ($this->isProtected("admin")) {
            throw new \Exception('This role cannot be deleted.');
        }
        parent::delete();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
