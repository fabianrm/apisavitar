<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Material extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code', 'name', 'category_id', 'presentation_id',
        'serial', 'model', 'brand_id', 'min', 'type', 'image', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function entryDetails()
    {
        return $this->hasMany(EntryDetail::class);
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
