<?php
namespace App\Filters;

use App\Filters\ApiFilter;

class BoxFilter extends ApiFilter
{

    protected $safeParams = [
        'id' => ['eq'],
        'city' => ['eq'],
        'totalPorts' => ['eq', 'gt', 'gte', 'lt', 'lte'],
        'availablePorts' => ['eq', 'gt', 'gte', 'lt', 'lte'],
    ];
    protected $columnMap = [
        'totalPorts' => 'total_ports',
        'availablePorts' => 'available_ports'
    ];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}