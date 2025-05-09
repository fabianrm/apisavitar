<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Box extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'enterprise_id',
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
     * Relación con enterprise
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
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


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->enterprise_id)) {
                $model->enterprise_id = CurrentEnterprise::get();
            }
        });
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
