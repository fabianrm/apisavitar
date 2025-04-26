<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_code',
        'description',
        'reason_id',
        'amount',
        'date',
        'voutcher',
        'note',
        'date_paid',
        'status',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function reasons()
    {
        return $this->belongsTo(Reason::class, "reason_id");
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            $expense->created_by = Auth::id();
            $expense->updated_by = Auth::id();
        });

        static::updating(function ($expense) {
            $expense->updated_by = Auth::id();
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
