<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;


    /**
     * Get the customer that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }


    /**
     * Get the plan that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Plan()
    {
        return $this->belongsTo(Plan::class);
    }


    /**
     * Get all of the invoices for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Invoices()
    {
        return $this->hasMany(Invoice::class);
    }



}
