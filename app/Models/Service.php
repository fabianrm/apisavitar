<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'router_id',
        'plan_id',
        'box_id',
        'port_number',
        'registration_date',
        'billing_date',
        'recurrent',
        'due_date',
        'address_instalation',
        'city',
        'latitude',
        'longitude',
        'is_active',
        'status'
    ];


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


    /**
     * Get all of the invoices for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
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




}
