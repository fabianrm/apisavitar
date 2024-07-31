<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Output extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'date', 'destination_id', 'employee_id', 'total', 'comment', 'status'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function outputDetails()
    {
        return $this->hasMany(OutputDetail::class);
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
