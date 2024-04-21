<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'document_number',
        'name',
        'address',
        'phone_number',
        'email',
        'status',
    ];

    /**
     * Get all of the services for the Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }



}
