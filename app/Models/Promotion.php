<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Promotion extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'enterprise_id',
        'plan_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'price',
        'duration_months',
        'status',
        'created_by'
    ];

    // Una promoción puede aplicarse a muchos servicios
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
