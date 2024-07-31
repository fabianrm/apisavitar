<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_detail_id', 'date', 'operation', 'quantity', 'price', 'total', 'comment'
    ];

    public function entryDetail()
    {
        return $this->belongsTo(EntryDetail::class);
    }
}
