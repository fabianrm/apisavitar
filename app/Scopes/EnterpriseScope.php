<?php

namespace App\Scopes;

use App\Helpers\CurrentEnterprise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;

class EnterpriseScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {

        $enterpriseId = CurrentEnterprise::get();

        if ($enterpriseId) {
            $builder->where($model->getTable() . '.enterprise_id', $enterpriseId);
        }

        //Multiple Store
        if (auth()->check()) {
            $user = auth()->user();
            $storeIds = $user->enterprises()->select('role_user.enterprise_id')->pluck('enterprise_id')->toArray(); // Todas las tiendas del usuario

            if (!empty($storeIds)) {
                $table = $model->getTable();
                $builder->whereIn($table . '.enterprise_id', $storeIds);
            }
        }
    }
}
