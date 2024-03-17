<?php
namespace App\Filters;

use App\Filters\ApiFilter;

class RouterFilter extends ApiFilter
{

    protected $safeParams = [
        'id' => ['eq'],
        'ip' => ['eq'],
    ];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}