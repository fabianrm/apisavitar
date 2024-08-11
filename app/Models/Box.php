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
        'note',
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
        return $this->belongsTo(City::class, "city_id");
    }

    public function calculateAvailablePorts()
    {
        $usedPorts = Service::where('box_id', $this->id)->where('status', 1)->count();
        $this->available_ports = $this->total_ports - $usedPorts;
        $this->save();
    }



}
