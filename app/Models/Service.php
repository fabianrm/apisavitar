<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_code',
        'enterprise_id',
        'customer_id',
        'plan_id',
        'router_id',
        'box_id',
        'port_number',
        'equipment_id',
        'city_id',
        'address_installation',
        'reference',
        'registration_date',
        'installation_date',
        'latitude',
        'longitude',
        'billing_date',
        'due_date',
        'end_date',
        'user_pppoe',
        'pass_pppoe',
        'iptv',
        'user_iptv',
        'pass_iptv',
        'installation_payment',
        'installation_amount',
        'prepayment',
        'status',
        'created_by',
        'updated_by',
    ];


    /**
     * Get all of the invoices for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'service_id');
    }



    /**
     * Get the customer that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customers()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }


    /**
     * Get the plan that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plans()
    {
        return $this->belongsTo(Plan::class, "plan_id");
    }


    public function boxes()
    {
        return $this->belongsTo(Box::class, "box_id");
    }

    /**
     * Get the plan that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function routers()
    {
        return $this->belongsTo(Router::class, "router_id");
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


    /**
     * Get the equip that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipments()
    {
        return $this->belongsTo(Equipment::class, "equipment_id");
    }

    public function suspensions()
    {
        return $this->hasMany(Suspension::class, 'service_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, "created_by");
    }



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
