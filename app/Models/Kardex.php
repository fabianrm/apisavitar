<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Kardex extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_detail_id', 'date', 'has', 'operation', 'quantity', 'stock', 'comment'
    ];

    public function entryDetail()
    {
        return $this->belongsTo(EntryDetail::class);
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
