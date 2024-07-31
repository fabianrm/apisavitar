<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['ruc', 'name', 'address', 'phone', 'email', 'status'];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
