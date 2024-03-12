<?php
namespace App\Filters;

class ApiFilter
{

    protected $safeParams = [];
    protected $columnMap = [];
    protected $operatorMap = [];

    public function transform($request)
    {
        $eloquery = [];
        foreach ($this->safeParams as $parm => $operators) {
            $query = $request->query($parm);
            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$parm] ?? $parm;
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloquery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloquery;
    }


}
