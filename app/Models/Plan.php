<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'download',
        'upload',
        'price',
        'guaranteed_speed',
        'priority',
        'burst_limit',
        'burst_threshold',
        'burst_time',
        'status'
    ];

    /**
     * Get all of the Services for the Plan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Services()
    {
        return $this->hasMany(Service::class);
    }


}
