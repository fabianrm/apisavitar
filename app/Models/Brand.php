<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];


    /**
     * Get all of the equipments for the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }


    public function materials()
    {
        return $this->hasMany(Material::class);
    }


    /**
     * Relación con enterprise
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
