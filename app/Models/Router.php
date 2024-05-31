<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
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

}
