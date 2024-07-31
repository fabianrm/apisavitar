<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Capturar usuario
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
