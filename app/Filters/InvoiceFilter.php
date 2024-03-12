<?php
namespace App\Filters;

use App\Filters\ApiFilter;

class InvoiceFilter extends ApiFilter
{

    protected $safeParams = [
        'serviceId' => ['eq'],
        'amount' => ['eq', 'gt','gte', 'lt', 'lte'],
        'billedDated' => ['eq'],
        'paidDated' => ['eq', 'gt', 'gte', 'lt', 'lte'],
        'status' => ['eq', 'ne'],
    ];
    protected $columnMap = [
        'serviceId' => 'service_id',
        'billedDate' => 'billed_dated',
        'paidDate' => 'paid_dated'
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
