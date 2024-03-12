<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'city',
        'address',
        'reference',
        'total_ports',
        'available_ports',
        'status',
    ];

}
