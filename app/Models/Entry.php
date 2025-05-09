<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = ['enterprise_id', 'date', 'document_number', 'supplier_id', 'document_id', 'entry_type_id', 'total', 'status'];

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

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    /**
     * Capturar usuario
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->enterprise_id)) {
                $model->enterprise_id = CurrentEnterprise::get();
            }

            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    /**
     * Scopes para filtro por tienda de usuario
     */
    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    // Si necesitas consultas sin el filtro global
    public static function withoutStoreScope()
    {
        return static::withoutGlobalScope(EnterpriseScope::class);
    }
}
