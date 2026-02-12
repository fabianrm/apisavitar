<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxRoutePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_route_id',
        'path',
    ];

    public function boxRoute()
    {
        return $this->belongsTo(BoxRoute::class);
    }
}
