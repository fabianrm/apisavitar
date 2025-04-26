<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'category_ticket_id',
        'destination_id',
        'subject',
        'description',
        'expiration',
        'priority',
        'customer_id',
        'technician_id',
        'admin_id',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'status'
    ];

    protected $dates = ['expiration', 'assigned_at', 'resolved_at', 'closed_at'];

    public function categoryTicket()
    {
        return $this->belongsTo(CategoryTicket::class);
    }

    public function project()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
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
