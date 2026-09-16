<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Router extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'enterprise_id',
        'ip',
        'vlan',
        'usuario',
        'password',
        'port',
        'api_connection',
        'status',
    ];

    /**
     * Get all of the services for the Router
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

    public function connectivityCacheKey(): string
    {
        return "mk_status_{$this->id}";
    }

    public function isMonitored(): bool
    {
        return str_starts_with($this->ip, '10.100.100.');
    }

    /**
     * Último estado conocido reportado por mk:monitor-connectivity (cada 5 min).
     * No es un chequeo en vivo.
     */
    public function connectivityStatus(): array
    {
        if (! $this->isMonitored()) {
            return ['status' => 'no_monitoreado', 'checked_at' => null];
        }

        $cached = Cache::get($this->connectivityCacheKey());

        if (! $cached) {
            return ['status' => 'desconocido', 'checked_at' => null];
        }

        return [
            'status' => $cached['up'] ? 'online' : 'offline',
            'checked_at' => $cached['checked_at'],
        ];
    }

    public function metrics()
    {
        return $this->hasMany(RouterMetric::class);
    }

    //Capturar y setear la empresa del usuario logueado
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
