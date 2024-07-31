<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'emission_date', 'transfer_date', 'origin_address',
        'destination_address', 'driver_name', 'vehicle_plate',
        'warehouse_id', 'sender_name', 'receiver_name', 'comment'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shipmentGuideDetails()
    {
        return $this->hasMany(ShipmentGuideDetail::class);
    }
}
