<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'prefix', 'status'];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
