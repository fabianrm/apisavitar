<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'status'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }


    public function cities()
    {
        return $this->belongsTo(City::class, "city_id");
    }



    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withPivot('role_id');
    }
}
