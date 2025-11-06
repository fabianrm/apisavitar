<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerHistoricalSummaryExport implements FromView
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function view(): View
    {
        return view('customers.historical_summary', [
            'summary' => $this->customer->getHistoricalSummary()
        ]);
    }
}
