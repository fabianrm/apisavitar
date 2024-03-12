<?php
namespace App\Filters;

use Iluminate\Http\Request;
use App\Filters\ApiFilter;

class CustomerFilter extends ApiFilter
{

    protected $safeParams = [
        'type' =>['eq'],
        'documentNumber' =>['eq'],
        'name' =>['eq'],
        'address' =>['eq'],
        'city' =>['eq'],
        'email' =>['eq'],
        'status'=>['eq'],
    ];
    protected $columnMap = [
        'documentNumber'=> 'document_number',
    ];
    protected $operatorMap = [
        'eq'=> '=',
        'lt'=> '<',
        'lte'=> '<=',
        'gt'=> '>',
        'gte'=> '>=',
    ];

   

}
