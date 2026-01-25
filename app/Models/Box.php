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
        'type',
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
        return $this->belongsTo(City::class, 'city_id');
    }

    public function calculateAvailablePorts()
    {
        $usedPorts = Service::where('box_id', $this->id)->where('status', 1)->count();
        $this->available_ports = $this->total_ports - $usedPorts;
        $this->save();
    }

    // Retorna los puertos disponibles
    public function availablePorts()
    {
        // Genera todos los puertos posibles (del 1 hasta total_ports)
        $todosLosPuertos = range(1, $this->total_ports);
        // Obtiene los puertos ocupados en esta caja
        $puertosOcupados = $this->services()->pluck('port_number')->toArray();

        Log::info('Todos los puertos: '.implode(', ', $todosLosPuertos));
        Log::info('Puertos ocupados: '.implode(', ', $puertosOcupados));
        Log::info('Puertos disponibles: '.implode(', ', array_diff($todosLosPuertos, $puertosOcupados)));

        // Elimina los que ya están ocupados
        $puertosDisponibles = array_diff($todosLosPuertos, $puertosOcupados);

        // Devuelve los disponibles en el mismo formato que el procedimiento almacenado
        return collect($puertosDisponibles)->map(function ($port) {
            return [
                'id' => $this->id,
                'port_number' => $port,
            ];
        })->values(); // reindexa
    }

    /**
     * Get contract code and status for all services in the box.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getServicesInfo()
    {
        return $this->services->map(function ($service) {
            return [
                'service_id' => $service->id,
                'service_code' => $service->service_code,
                'customer_name' => $service->customers ? $service->customers->name : null,
                'plan_name' => $service->plans->name,
                'port_number' => $service->port_number,
                'status' => $service->status,
            ];
        });
    }

    /**
     * Setear id de empresa
     */
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
