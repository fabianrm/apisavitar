<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id', 'date', 'material_id', 'quantity', 'current_stock', 'price', 'subtotal', 'warehouse_id', 'location'
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function outputDetails()
    {
        return $this->hasMany(OutputDetail::class);
    }
}
