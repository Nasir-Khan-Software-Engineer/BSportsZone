<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Accountinfo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public function getRememberTokenName()
    {
        return null; // disables remember me
    }
    
    protected $fillable = [
        'POSID',
        'name',
        'email',
        'password',
        'name',
        'email',
        'phone',
        'picture',
        'status',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        public function role(): BelongsTo
    {
        return $this->BelongsTo(Role::class,'role_id');
    }

    public function accessRights()
    {
        return $this->role ? $this->role->accessRights : collect();
    }

    public function accountInfo()
    {
        return $this->hasOne(AccountInfo::class, 'POSID', 'POSID');
    }
}
