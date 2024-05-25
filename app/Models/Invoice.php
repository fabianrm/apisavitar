<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'note',
        'status'
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

}
