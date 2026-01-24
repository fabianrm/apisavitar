<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_box_id',
        'end_box_id',
        'color',
        'points',
        'distance',
        'notes',
        'status',
    ];

    protected $casts = [
        'points' => 'array',
        'distance' => 'float',
    ];

    public function startBox()
    {
        return $this->belongsTo(Box::class, 'start_box_id');
    }

    public function endBox()
    {
        return $this->belongsTo(Box::class, 'end_box_id');
    }
}
