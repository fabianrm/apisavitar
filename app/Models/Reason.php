<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'type',
        'name',
        'status'
    ];


    /**
     * Get all of the expenses for the reason
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    //Capturar y setear la empresa del usuario logueado
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->enterprise_id)) {
                $model->enterprise_id = CurrentEnterprise::get();
            }
        });
    }
}
