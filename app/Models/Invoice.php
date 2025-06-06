<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'enterprise_id',
        'service_id',
        'price',
        'igv',
        'discount',
        'amount',
        'letter_amount',
        'due_date',
        'start_date',
        'end_date',
        'paid_dated',
        'receipt',
        'tipo_pago',
        'note',
        'status',
        'reminder_sent_at',
        'overdue_reminder_sent_at',
        'created_by',
        'updated_by',
    ];


    /**
     * Get the service that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Relación con enterprise
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    //creado Por 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //Actualizado Por 
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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
