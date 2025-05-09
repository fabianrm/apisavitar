<?php

namespace App\Models;

use App\Helpers\CurrentEnterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['enterprise_id', 'name', 'status'];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * Setear Id de empresa
     */
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
