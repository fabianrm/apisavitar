<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputDetail extends Model
{
    use HasFactory;

    protected $fillable = ['output_id', 'material_id', 'quantity', 'subtotal'];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
