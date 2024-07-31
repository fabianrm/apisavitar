<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'address', 'phone', 'status'];

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }
}
