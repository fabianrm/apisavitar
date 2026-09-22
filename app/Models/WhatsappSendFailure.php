<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSendFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'invoice_id',
        'customer_name',
        'phone',
        'type',
        'error_message',
        'status',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
