<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
        'dni',
        'name',
        'email',
        'password',
        'address',
        'phone',
        'position',
        'status',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }


    public function employees()
    {
        return $this->hasMany(Employee::class);
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user','user_id', 'role_id');
    }


    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // public function hasPermission($permission)
    // {
    //     return $this->roles()->whereHas('permissions', function ($q) use ($permission) {
    //         $q->where('name', $permission);
    //     })->exists();
    // }

    public function permissions()
    {
        return $this->roles->flatMap->permissions->unique();
    }



}
