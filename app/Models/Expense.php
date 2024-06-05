<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'reason_id',
        'amount',
        'date',
        'voutcher',
        'note',
        'date_paid',
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
}
