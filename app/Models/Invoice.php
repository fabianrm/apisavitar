<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'service_id',
        'price',
        'igv',
        'discount',
        'amount',
        'letter_amount',
        'due_date',
        'start_date',
        'end_date',
        'paid_dated',
        'receipt',
        'tipo_pago',
        'note',
        'status',
        'created_by',
        'updated_by',
    ];


    /**
     * Get the service that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
