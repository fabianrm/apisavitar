<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\TicketAttachment;
use App\Models\TicketHistory;
use App\Services\UtilService;
use Illuminate\Http\Request;
use Carbon\Carbon;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $ticket = Ticket::with(['categoryTicket', 'customer','admin', 'technician'])->get();
        // return new TicketCollection($ticket);

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener el primer rol del usuario autenticado
        $role = $user->roles()->first();

        // Inicializar la consulta base con las relaciones necesarias
        $query = Ticket::with(['categoryTicket', 'customer', 'admin', 'technician']);

        // Filtrar según el rol
        if ($role && $role->name === 'Técnico') {
            // Si el usuario es técnico, filtrar por los tickets asignados a él
            $query->where('technician_id', $user->id);
        }

        // Ejecutar la consulta
        $tickets = $query->get();

        // Devolver los tickets usando el recurso TicketCollection
        return new TicketCollection($tickets);
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
    public function store(StoreTicketRequest $request)
    {

        $clientService = app(UtilService::class);

        // Genera un código único para el cliente
        $uniqueCode = $clientService->generateUniqueCodeTicket('TK');


        $ticket = Ticket::create([
            'code' => $uniqueCode,
            'category_ticket_id' => $request->category_ticket_id,
            'destination_id' => $request->destination_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'registrado',
            'customer_id' => $request->customer_id,
            'technician_id' => null,  // Aún no asignado
            'admin_id' => auth()->id(),
        ]);

        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'status' => 'registrado',
            'comment' => 'Registrado',
            'changed_by' => auth()->id(),
        ]);

        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::with(['categoryTicket', 'customer', 'admin', 'technician', 'history', 'history.user'])->findOrFail($id);
        return new TicketResource($ticket);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }

    public function updateStatus(Request $request, $ticketId)
    {
        // Validar los datos de entrada
        $request->validate([
            'status' => 'required|in:pendiente,atencion,espera_pase,validacion,solucionado,cerrado',
            'changed_by' => 'required|exists:users,id',
        ]);

        // Buscar el ticket
        $ticket = Ticket::findOrFail($ticketId);

        // Actualizar el estado del ticket
        $ticket->status = $request->status;

        // Si el estado es 'En atención', se asigna un técnico
        if ($request->status === 'atencion') {
            $note = 'Se inició la atención';
        }

        // Si el estado es 'Solucionado', se guarda la fecha de resolución
        if ($request->status === 'solucionado') {
            $note =  $request->comment;
            $ticket->resolved_at = now();
        }

        // Si el estado es 'En validación', se guarda la fecha de cierre
        if ($request->status === 'validacion') {
            $note = 'Esperando confirmación de solución';
        }

        // Si el estado es 'En validación', se guarda la fecha de cierre
        if ($request->status === 'cerrado') {
            $ticket->closed_at = now();
            $note = 'Caso cerrado';
        }
        // Guardar los cambios del ticket
        $ticket->save();

        // Registrar el historial del cambio de estado
        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'comment' => $note,
            'status' => $request->status,
            'changed_by' => $request->changed_by,  // Id del técnico o admin que hizo el cambio
        ]);

        return response()->json([
            'message' => 'El estado del ticket ha sido actualizado',
            'ticket' => $ticket
        ], 200);
    }

    
    // Adjuntar documentos/imágenes al ticket
    public function addAttachment(Request $request, Ticket $ticket)
    {
        $filePath = $request->file('attachment')->store('attachments');

        TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'Archivo adjuntado con éxito']);
    }

    // Ver el historial del ticket
    public function history(Ticket $ticket)
    {
        return response()->json($ticket->history);
    }


    //Asignar Ticket
    public function assignTechnician(Request $request, $ticketId)
    {
        // Validar los datos de entrada
        $request->validate([
            'technician_id' => 'required|exists:users,id', // Validar que el técnico exista
            'admin_id' => 'required|exists:users,id', // Validar que el administrador exista
        ]);

        // Buscar el ticket
        $ticket = Ticket::findOrFail($ticketId);

        // Verificar si ya fue asignado a este mismo técnico
        if ($ticket->technician_id == $request->technician_id) {
            return response()->json([
                'message' => 'Este técnico ya ha sido asignado a este ticket.'
            ], 400);
        }

        // Si ya hay un técnico asignado, se permite reasignar
        $previousTechnician = $ticket->technician_id;

        // Si el ticket no tiene un técnico asignado, lo colocamos en 'Pending'
        if (!$ticket->technician_id) {
            $ticket->status = 'pendiente'; // Asignación inicial al estado 'Pending'
        }

        // Asignar el nuevo técnico al ticket (sin cambiar el estado)
        $ticket->technician_id = $request->technician_id;
        $ticket->assigned_at = Carbon::now(); // Registrar la fecha de asignación
        $ticket->expiration = $request->expiration; // Registrar la fecha de asignación
       
        $ticket->save();

        // Registrar el historial del cambio de técnico
        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'status' => $ticket->status, // El estado permanece igual (Pending)
            'changed_by' => $request->admin_id, // ID del administrador que hizo la asignación
            'comment' => $previousTechnician
                ? 'Reasignado desde técnico ID ' . $previousTechnician
                : 'Asignado a técnico', // Comentario sobre la asignación/reasignación
        ]);

        return response()->json([
            'message' => $previousTechnician
                ? 'Ticket reasiagnado al nuevo técnico correctamente'
                : 'Ticket asignado al técnico correctamente',
            'ticket' => $ticket
        ], 200);
    }
}
