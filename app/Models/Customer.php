<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SuspensionResource;
use App\Http\Resources\TicketResource;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'enterprise_id',
        'type',
        'document_number',
        'name',
        'city_id',
        'address',
        'reference',
        'latitude',
        'longitude',
        'phone_number',
        'whatsapp',
        'email',
        'status',
        'created_by',
        'updated_by',
    ];


    /**
     * Capturar usuario
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->enterprise_id)) {
                $model->enterprise_id = CurrentEnterprise::get();
            }

            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }



    /**
     * Get all of the services for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function cities()
    {
        return $this->belongsTo(City::class, "city_id");
    }

    /**
     * Get all of the tickets for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relación con enterprise
     */
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

    public function getHistoricalSummary()
    {
        $this->load([
            'services.invoices',
            'services.suspensions',
            'tickets.history'
        ]);

        $services = $this->services->map(function ($service) {
            return [
                'service' => new ServiceResource($service),
                'invoices' => InvoiceResource::collection($service->invoices),
                'suspensions' => SuspensionResource::collection($service->suspensions),
            ];
        });

        return [
            'customer' => new CustomerResource($this),
            'services' => $services,
            'tickets' => TicketResource::collection($this->tickets),
        ];
    }
}
