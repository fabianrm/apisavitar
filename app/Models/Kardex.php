<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Kardex extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'date',
        'has',
        'operation',
        'quantity',
        'stock',
        'comment'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }


    /**
     * Capturar usuario
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
