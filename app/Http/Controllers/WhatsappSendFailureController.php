<?php

namespace App\Http\Controllers;

use App\Http\Resources\WhatsappSendFailureCollection;
use App\Http\Resources\WhatsappSendFailureResource;
use App\Models\Invoice;
use App\Models\WhatsappSendFailure;
use App\Scopes\EnterpriseScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappSendFailureController extends Controller
{
    /**
     * Listado paginado de envíos fallidos de la empresa del usuario logueado
     * (ya lo filtra el EnterpriseScope del modelo), para la pantalla de
     * Configuración > WhatsApp > Fallos de envío.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 200) : 10;

        $query = WhatsappSendFailure::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $failures = $query->with('resolvedBy')->orderBy('created_at', 'desc')->paginate($perPage);

        return new WhatsappSendFailureCollection($failures);
    }

    /**
     * Marca un fallo como resuelto (el admin ya corrigió el número del
     * cliente, o confirmó que no aplica) para sacarlo de la lista pendiente.
     */
    public function resolve(WhatsappSendFailure $whatsappSendFailure)
    {
        $whatsappSendFailure->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        return new WhatsappSendFailureResource($whatsappSendFailure);
    }

    /**
     * Registra un envío fallido -- llamado por n8n cuando Evolution API
     * devuelve error al intentar mandar un recordatorio, en vez de la hoja
     * de Google Sheets que se usaba antes. Deriva enterprise_id del propio
     * invoice_id (no confía en un enterprise_id que mande el cliente),
     * porque este endpoint lo llama el mismo token compartido usado para
     * las demás empresas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer'],
            'customer_name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'type' => ['required', 'in:due,overdue'],
            'error_message' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::withoutGlobalScope(EnterpriseScope::class)->find($request->input('invoice_id'));

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found'], 422);
        }

        $failure = WhatsappSendFailure::create([
            'enterprise_id' => $invoice->enterprise_id,
            'invoice_id' => $invoice->id,
            'customer_name' => $request->input('customer_name'),
            'phone' => $request->input('phone'),
            'type' => $request->input('type'),
            'error_message' => $request->input('error_message'),
        ]);

        return response()->json(['success' => true, 'id' => $failure->id], 201);
    }
}
