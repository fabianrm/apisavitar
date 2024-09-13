<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
