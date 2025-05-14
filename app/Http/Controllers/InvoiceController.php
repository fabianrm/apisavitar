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
use App\Exports\InvoicesResumen;
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
            $invoices = Invoice::orderBy('end_date', 'desc')->paginate($perPage);
        } else {
            $invoices = Invoice::where($queryItems)
                ->orderBy('end_date', 'desc')
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
    public function store(StoreInvoiceRequest $request) {}

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

    /* Paid Invoice */

    public function paidInvoice(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($id);

        $invoice->status = 'pagada';
        $invoice->tipo_pago = $request->input('tipo_pago');
        $invoice->amount = $request->input('amount');
        $invoice->discount = $request->input('discount');
        $invoice->paid_dated = Carbon::now();
        $invoice->note = $request->input('note');
        $invoice->save();

        return response()->json([
            'status' => true,
            'message' => 'Factura pagada con éxito',
            'invoice' => $invoice,
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function cancelInvoice(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($id);

        $invoice->status = 'anulada';
        $invoice->note = $request->input('note');
        $invoice->save();

        return response()->json([
            'status' => true,
            'message' => 'Factura anulada con éxito',
            'invoice' => $invoice,
        ], 200);
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
    public function generateInvoicesMonth(Request $request)
    {
        $serviceId = $request->input('service_id');

        $invoiceService = app(InvoiceService::class);
        $totalInvoices = $invoiceService->generateCurrentMonthInvoices($serviceId);

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
    function generateInvoicesByService($id)
    {
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

        //Filtrar por ciudad si se proporciona
        if ($request->has('city_id') && $request->input('city_id') !== null) {
            $query->where('services.city_id', '=',  $request->input('city_id'));
        }

        // Ordenar por nombre del cliente
        $query->orderBy('invoices.updated_at', 'desc')
            ->orderBy('invoices.start_date', 'desc');

        // Usar la paginación de Laravel
        $invoices = $query->with(['service.customers', 'service.plans'])->paginate($perPage);

        // return response()->json($invoices);
        return new InvoiceCollection($invoices);
    }


    //Funcion para envio de recordatorios por n8n
    //Recuerda desde una semana antes, pasando un día.
    public function recordatory(Request $request)
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(7); // Change from 5 to 7 days

        $invoices = Invoice::query()
            ->join('services', 'invoices.service_id', '=', 'services.id')
            ->join('customers', 'services.customer_id', '=', 'customers.id')
            ->select(
                'invoices.id',
                'invoices.price',
                'invoices.start_date',
                'customers.name as customer_name',
                'customers.whatsapp as customer_phone',
                'invoices.reminder_sent_at',
                'invoices.reminder_count'
            )
            ->where('services.status', 'activo')
            ->where('invoices.status', 'pendiente')
            ->where('invoices.start_date', '>=', $today)
            ->where('invoices.start_date', '<=', $limitDate)
            ->where(function ($query) use ($today) {
                $query->whereNull('invoices.reminder_sent_at')
                    ->orWhere('invoices.reminder_sent_at', '<=', $today->copy()->subDay());
            })
            ->orderBy('customers.name')
            ->get();
        return response()->json($invoices);
    }

    //Recordar vencidos
    public function recordatoryOverdue(Request $request)
    {
        $today = Carbon::today();

        $invoices = Invoice::query()
            ->join('services', 'invoices.service_id', '=', 'services.id')
            ->join('customers', 'services.customer_id', '=', 'customers.id')
            ->select(
                'invoices.id',
                'invoices.price',
                'invoices.start_date as due_date', // Mostramos start_date como fecha de vencimiento
                'invoices.due_date as cutoff_date', // Mostramos la fecha límite de pago
                'customers.name as customer_name',
                'customers.whatsapp as customer_phone',
                'invoices.reminder_sent_at',
                'invoices.reminder_count'
            )
            ->where('services.status', 'activo')
            ->where('invoices.status', 'vencida')
            ->where('invoices.start_date', '<=', $today) // Ya pasó la fecha de vencimiento
            ->where('invoices.due_date', '>=', $today) // Aún no se corta el servicio
            ->where(function ($query) use ($today) {
                $query->whereNull('invoices.overdue_reminder_sent_at')
                    ->orWhereDate('invoices.overdue_reminder_sent_at', '<=', $today->copy()->subDay());
            })
            ->orderBy('customers.name')
            ->get();
        return response()->json($invoices);
    }


    // Marcar la respuesta de recordatorio x vencer
    public function markReminderSent($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->reminder_sent_at = now();
        $invoice->reminder_count = ($invoice->reminder_count ?? 0) + 1;
        $invoice->save();
        return response()->json(['success' => true]);
    }


    //Marcar recordatorio de vencidas
    public function sendReminderOverdue($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $today = Carbon::today();

        // Solo la fecha, sin hora
        $lastReminderDate = $invoice->overdue_reminder_sent_at
            ? Carbon::parse($invoice->overdue_reminder_sent_at)->toDateString()
            : null;

        $todayDate = $today->toDateString();

        // Verifica si está dentro del rango permitido: después del vencimiento (start_date), antes del corte (due_date)
        // Y además que no se haya enviado recordatorio hoy
        if (
            $invoice->start_date < $today &&
            $invoice->due_date >= $today &&
            ($lastReminderDate === null || $lastReminderDate < $todayDate)
        ) {
            $invoice->update([
                'overdue_reminder_sent_at' => Carbon::now(),
                'reminder_count' => $invoice->reminder_count + 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recordatorio enviado correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se envió recordatorio. Fecha fuera de rango o ya enviado hoy.'
        ]);
    }



    //Consulta Vencidas sin corte
    public function getExpiredActiveServices()
    {
        $today = Carbon::today();

        $services = Invoice::query()
            ->join('services', 'invoices.service_id', '=', 'services.id')
            ->join('customers', 'services.customer_id', '=', 'customers.id')
            ->join('cities', 'services.city_id', '=', 'cities.id')
            ->select(
                'invoices.id as invoice_id',
                'invoices.start_date',
                'invoices.due_date',
                'invoices.status as invoice_status',
                'services.id as service_id',
                'services.status as service_status',
                'cities.name as ciudad',
                'services.address_installation',
                'services.reference',
                'customers.name as customer_name',
                'customers.whatsapp as customer_phone',
            )
            ->where('invoices.status', 'vencida') // Factura vencida
            ->where('services.status', 'activo')  // Pero el servicio sigue activo
            ->where('invoices.due_date', '<', $today) // Ya pasó la fecha de corte
            ->orderBy('invoices.start_date', 'desc')
            ->get();

        return response()->json($services);
    }



    //Exportar facturas
    public function exportInvoices(Request $request)
    {
        $filters = $request->only(['status', 'start_date', 'end_date', 'customer_name', 'city_id']);
        return Excel::download(new InvoicesExport($filters), 'invoices.xlsx');
    }


    //Exportar facturas resumen
    public function exportInvoicesResumen(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);
        return Excel::download(new InvoicesResumen($filters), 'invoices_resumen.xlsx');
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

        $customer_name = wordwrap($invoice->service->customers->name, 30, "\n", true);

        $data = [
            'receipt' => $invoice->receipt,
            'service_id' => $invoice->service->service_code,
            'plan_name' => $invoice->service->plans->name,
            'customer_name' => $customer_name,
            'price' => $invoice->price,
            'discount' => $invoice->discount,
            'total' => $invoice->amount,
            'start_date' => Carbon::parse($invoice->start_date)->format('d-m-Y'),
            'end_date' => Carbon::parse($invoice->end_date)->format('d-m-Y'),
            'periodic' => strtoupper(Carbon::parse($invoice->start_date)->translatedFormat('F')),
            'paid_dated' => Carbon::parse($invoice->paid_dated)->format('d-m-Y'),
            'note' => $invoice->note,
        ];

        $pdf = PDF::loadView('invoice.receipt', $data)->setPaper([0, 0, 155, 654], 'portrait');
        //$pdf = PDF::loadView('invoice.receipt', compact('invoice'))->setPaper([0, 0, 226, 654], 'portrait'); // 80mm x 140mm
        return $pdf->download('recibo_nro_' . $invoice->receipt . '.pdf');
    }

    public function getMonthlyPaidAmounts()
    {
        $monthlyPaidAmounts = Invoice::where('status', 'pagada')
            ->selectRaw('MONTH(paid_dated) as month, SUM(amount) as total_amount')
            ->groupBy('month')
            ->get();

        // Array de nombres de los meses
        $monthNames = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        // Variables para almacenar los resultados finales
        $months = [];
        $totals = [];

        // Organizar los resultados
        foreach ($monthlyPaidAmounts as $item) {
            $months[] = $monthNames[$item->month];
            $totals[] = floatval($item->total_amount);
        }

        // Formato de salida
        $result = [
            'data' => [
                [
                    'month' => $months,
                    'total_amount' => $totals,
                ]
            ]
        ];

        return response()->json($result);
    }
}
