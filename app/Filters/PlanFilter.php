<?php
namespace App\Filters;

use Iluminate\Http\Request;
use App\Filters\ApiFilter;

class PlanFilter extends ApiFilter
{

    protected $safeParams = [
        'id' => ['eq'],
        'name' => ['eq'],
        'price' => ['eq', 'gt','gte', 'lt', 'lte'],
        'status' => ['eq'],
    ];
    protected $columnMap = [
        
    ];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}
