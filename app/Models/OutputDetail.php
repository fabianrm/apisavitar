<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputDetail extends Model
{
    use HasFactory;

    protected $fillable = ['output_id', 'material_id', 'quantity', 'subtotal'];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
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
