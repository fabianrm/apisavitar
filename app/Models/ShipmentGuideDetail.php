<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentGuideDetail extends Model
{
    use HasFactory;

    protected $fillable = ['shipment_guide_id', 'output_id'];

    public function shipmentGuide()
    {
        return $this->belongsTo(ShipmentGuide::class);
    }

    public function output()
    {
        return $this->belongsTo(Output::class);
    }
}
