<?php
namespace App\Filters;

use App\Filters\ApiFilter;

class ServiceFilter extends ApiFilter
{

    protected $safeParams = [
        'customerId' => ['eq'],
        'routerId' => ['eq'],
        'planId' => ['eq'],
        'boxId' => ['eq'],
        'registrationDate' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'billingDate' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'dueDate' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'isActive' => ['eq', 'ne'],
        'status' => ['eq', 'ne'],

    ];
    protected $columnMap = [
        'customerId' => 'customer_id',
        'routerId' => 'router_id',
        'planId' => 'plan_id',
        'boxId' => 'box_id',
        'registrationDate' => 'registration_date',
        'billingDate' => 'billing_date',
        'dueDate' => 'due_date',
        'isActive' => 'is_active',
        'status' => 'status'
    ];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!='
    ];



}
