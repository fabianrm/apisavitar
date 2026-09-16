<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouterMetric extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'router_id',
        'cpu_load',
        'mem_used',
        'mem_total',
        'disk_used',
        'disk_total',
        'uptime',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
