<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'route', 'parent_id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }


    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }
}
