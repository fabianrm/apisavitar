<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceCollection;
use Illuminate\Http\Request;
use App\Filters\InvoiceFilter;
use App\Services\InvoiceService;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new InvoiceFilter();
        $queryItems = $filter->transform($request);
        $perPage = $request->input('perPage', 10); // También puedes usar $request->get('perPage', 10)

        if (count($queryItems) == 0) {
            $invoices = Invoice::orderBy('start_date', 'desc')->paginate($perPage);
        } else {
            $invoices = Invoice::where($queryItems)
                ->orderBy('start_date', 'desc')
                ->paginate($perPage);
        }
        return new InvoiceCollection($invoices->appends($request->query()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            throw new ModelNotFoundException('Invoice not found');
        }
        return new InvoiceResource($invoice);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /***
     * Generar facturas
     */
    public function generateInvoices()
    {
        $invoiceService = app(InvoiceService::class);
        $invoiceService->generateMonthlyInvoices();
        return response()->json(['message' => 'Monthly invoices generated successfully.']);
    }


    /***
     * Generar facturas del mes actual
     */
    public function generateInvoicesMonth()
    {
        $invoiceService = app(InvoiceService::class);
        $invoiceService->generateCurrentMonthInvoices();
        return response()->json(['message' => 'Facturas del mes generadas correctamente.']);
    }


    /***
     * Retornar facturas con Nombre de cliente
     */

    public function getInvoicesWithDetails()
    {
        $invoices = Invoice::with(['service.customers', 'service.plans'])->get();

        return new InvoiceCollection($invoices);

        // $result = $invoices->map(function ($invoice) {
        //     return [
        //         'invoice_id' => $invoice->id,
        //         'contract_id' => $invoice->service->service_code,
        //         'amount' => $invoice->amount,
        //         'due_date' => $invoice->due_date,
        //         'status' => $invoice->status,
        //         'discount' => $invoice->discount,
        //         'start_date' => $invoice->start_date,
        //         'end_date' => $invoice->end_date,
        //         'customer_name' => $invoice->service->customers->name,
        //         'plan_name' => $invoice->service->plans->name,
        //     ];
        // });

        // return response()->json($result);
    }
}
