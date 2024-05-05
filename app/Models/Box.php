<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'city_id',
        'address',
        'reference',
        'latitude',
        'longitude',
        'total_ports',
        'available_ports',
        'status',
    ];

   /**
    * Get all of the services for the Box
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function services()
   {
       return $this->hasMany(Service::class);
   }

    /**
     * Get the city that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cities()
    {
        return $this->belongsTo(city::class);
    }



}
