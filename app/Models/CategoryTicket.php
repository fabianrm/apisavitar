<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTicket extends Model
{
    use HasFactory;
    
    protected $table = 'categories_tickets';

    protected $fillable = ['name', 'description', 'status'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
