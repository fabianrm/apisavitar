<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
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
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    /**
     * Scopes para filtro por tienda de usuario
     */
    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    // Si necesitas consultas sin el filtro global
    public static function withoutStoreScope()
    {
        return static::withoutGlobalScope(EnterpriseScope::class);
    }
}
