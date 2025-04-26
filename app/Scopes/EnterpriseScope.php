<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EnterpriseScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        //Multiple Store
        if (auth()->check()) {
            $user = auth()->user();
            $storeIds = $user->stores()->pluck('enterprise_id')->toArray(); // Todas las tiendas del usuario

            if (!empty($storeIds)) {
                $table = $model->getTable();
                $builder->whereIn($table . '.enterprise_id', $storeIds);
            }
        }
    }
}
