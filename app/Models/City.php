<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'status',
    ];

    /**
     * Get all of the services for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }


    /**
     * Get all of the boxes for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boxes()
    {
        return $this->hasMany(Box::class);
    }

    /**
     * Get all of the boxes for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get all of the enterprises for the city
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enterprises()
    {
        return $this->hasMany(Enterprise::class);
    }

}
