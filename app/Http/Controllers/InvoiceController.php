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
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $invoice->update($request->all());
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
        $totalInvoices = $invoiceService->generateCurrentMonthInvoices();

        return response()->json(
            [
                'totalInvoices' => $totalInvoices,
                'message' => $totalInvoices . ' facturas generadas'
            ]
        );
    }

    /**
     * Generar facturas de un contrato
     */
    function generateInvoicesByService($id) {
        $invoiceService = app(InvoiceService::class);
        $totalInvoices = $invoiceService->generateInvoicesForService($id);

        return response()->json(
            [
                'totalInvoices' => $totalInvoices,
                'message' => $totalInvoices . ' facturas generadas'
            ]
        );

    }





    /***
     * Retornar facturas con Nombre de cliente
     */

    public function getInvoicesWithDetails()
    {
        $invoices = Invoice::with(['service.customers', 'service.plans'])->get();
        return new InvoiceCollection($invoices);
    }


    //Search
    public function searchInvoices2(Request $request)
    {
        $query = Invoice::query();
        $perPage = $request->input('perPage', 10);

        // Filtrar por estado de la factura
        if ($request->has('status')) {
            $query->where('status', $request->input('status'))
                ->orderBy('start_date', 'desc');
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [
                $request->input('start_date'),
                $request->input('start_date')
            ]);
        }

        // Filtrar por nombre de cliente
        if ($request->has('customer_name')) {
            $customerName = $request->input('customer_name');
            $query->whereHas('service.customers', function ($query) use ($customerName) {
                $query->where('name', 'like', "%{$customerName}%");
            });
        }

        //return response()->json($invoices);

        // Usar la paginación de Laravel
        $invoices = $query->with(['service.customers', 'service.plans'])->paginate($perPage);
        return new InvoiceCollection($invoices);
        //       
    }


    //Funcion para listar invoices, con parametros de busqueda
    public function searchInvoices(Request $request)
    {
        $query = Invoice::query();
        $perPage = $request->input('perPage', 10);

        //Join with the Service and Customer tables to allow filtering by customer name
        $query->join('services', 'invoices.service_id', '=', 'services.id')
            ->join('customers', 'services.customer_id', '=', 'customers.id')
            ->select('invoices.*');

        // Filtrar por estado si se proporciona
        if ($request->has('status') && $request->input('status') !== null) {
            $query->where('invoices.status', $request->input('status'));
        }

        // Filtrar por rango de fechas si se proporciona
        if ($request->has('start_date') && $request->has('end_date') && $request->input('start_date') !== null && $request->input('end_date') !== null) {
            $query->whereBetween('invoices.start_date', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        // Filtrar por nombre del cliente si se proporciona
        if ($request->has('customer_name') && $request->input('customer_name') !== null) {
            $query->where('customers.name', 'like', '%' . $request->input('customer_name') . '%');
        }

        // Ordenar por nombre del cliente
        $query->orderBy('customers.name')
            ->orderBy('invoices.start_date', 'desc');

        // Usar la paginación de Laravel
        $invoices = $query->with(['service.customers', 'service.plans'])->paginate($perPage);

        // return response()->json($invoices);
        return new InvoiceCollection($invoices);
    }


    //Exportar facturas
    public function exportInvoices(Request $request)
    {
        $filters = $request->only(['status', 'start_date', 'end_date', 'customer_name']);
        return Excel::download(new InvoicesExport($filters), 'invoices.xlsx');
    }


    //Reporte de facturas
    public function getPaidInvoicesReport(Request $request)
    {
        // Validar los parámetros de fecha
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Obtener las fechas del request
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Obtener las facturas pagadas entre las fechas especificadas
        $invoices = Invoice::with(['service.customers'])
            ->where('status', 'pagada')
            ->whereBetween('paid_dated', [$startDate, $endDate])
            ->get();

        // Calcular la suma de la columna amount
        $totalAmount = $invoices->sum('amount');

        // Transformar las facturas usando el recurso
        $invoiceResource = InvoiceResource::collection($invoices);

        return response()->json([
            'data' => $invoiceResource,
            'totalAmount' => $totalAmount,
        ]);
    }

    // Recibo en PDF
    public function generateReceiptPDF($id)
    {
        $invoice = Invoice::findOrFail($id);

        $data = [
            'receipt' => $invoice->receipt,
            'service_id' => $invoice->service->service_code,
            'plan_name' => $invoice->service->plans->name,
            'customer_name' => $invoice->service->customers->name,
            'price' => $invoice->price,
            'discount' => $invoice->discount,
            'total' => $invoice->amount,
            'start_date' => Carbon::parse($invoice->start_date)->format('d-m-Y'),
            'end_date' => Carbon::parse($invoice->end_date)->format('d-m-Y'),
            'paid_dated' => Carbon::parse($invoice->paid_dated)->format('d-m-Y'),
            'note' => $invoice->note,
        ];

        $pdf = PDF::loadView('invoice.receipt', $data)->setPaper([0, 0, 155, 654], 'portrait');
        //$pdf = PDF::loadView('invoice.receipt', compact('invoice'))->setPaper([0, 0, 226, 654], 'portrait'); // 80mm x 140mm
        return $pdf->download('recibo_nro_' . $invoice->receipt . '.pdf');
    }
}
