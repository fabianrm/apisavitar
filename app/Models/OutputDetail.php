<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputDetail extends Model
{
    use HasFactory;

    protected $fillable = ['output_id', 'entry_detail_id', 'quantity', 'subtotal'];

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function entryDetail()
    {
        return $this->belongsTo(EntryDetail::class);
    }
}
