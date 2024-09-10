<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'category_ticket_id',
        'subject',
        'description',
        'customer_id',
        'technician_id',
        'admin_id',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'status'
    ];

    protected $dates = ['assigned_at', 'resolved_at', 'closed_at'];

    public function categoryTicket()
    {
        return $this->belongsTo(CategoryTicket::class);
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
}
