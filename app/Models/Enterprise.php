<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'name',
        'city_id',
        'address',
        'phone',
        'logo'
    ];

    public function cities()
    {
        return $this->belongsTo(City::class, "city_id");
    }

}
