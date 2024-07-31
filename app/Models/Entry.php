<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'document_number', 'supplier_id', 'document_id', 'entry_type_id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }

    public function entryDetails()
    {
        return $this->hasMany(EntryDetail::class);
    }
}
