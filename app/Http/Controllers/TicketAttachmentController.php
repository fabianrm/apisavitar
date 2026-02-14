<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketAttachmentRequest;
use App\Http\Requests\UpdateTicketAttachmentRequest;
use App\Models\TicketAttachment;

class TicketAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreTicketAttachmentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $attachment = TicketAttachment::findOrFail($id);

            if (! Storage::disk('public')->exists($attachment->file_path)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $file = Storage::disk('public')->get($attachment->file_path);
            $mimeType = Storage::disk('public')->mimeType($attachment->file_path);

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="'.basename($attachment->file_path).'"')
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            \Log::error('Error serving attachment: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketAttachment $ticketAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketAttachmentRequest $request, TicketAttachment $ticketAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketAttachment $ticketAttachment)
    {
        //
    }
}
