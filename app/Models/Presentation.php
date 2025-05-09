<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    use HasFactory;
    protected $fillable = ['enterprise_id', 'name', 'prefix', 'status'];

    public function materials()
    {
        return $this->hasMany(Material::class);
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
