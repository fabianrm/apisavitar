<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbreviation', 'status'];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
