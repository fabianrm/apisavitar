<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentGuideDetail extends Model
{
    use HasFactory;

    protected $fillable = ['enterprise_id', 'shipment_guide_id', 'output_id'];

    public function shipmentGuide()
    {
        return $this->belongsTo(ShipmentGuide::class);
    }

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    //Capturar y setear la empresa del usuario logueado
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->enterprise_id)) {
                $model->enterprise_id = CurrentEnterprise::get();
            }
        });
    }


    /**
     * Scopes para filtro por tienda de usuario
     */
    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    // Si necesitas consultas sin el filtro global
    public static function withoutStoreScope()
    {
        return static::withoutGlobalScope(EnterpriseScope::class);
    }
}
