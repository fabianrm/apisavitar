<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = [
        'id',
        'ip',
        'usuario',
        'password',
        'port',
        'api_connection',
        'status',
    ];
    use HasFactory;
}
