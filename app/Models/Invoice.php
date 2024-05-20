<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'amount',
        'igv',
        'discount',
        'letter_amount',
        'due_date',
        'start_date',
        'end_date',
        'paid_dated',
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
