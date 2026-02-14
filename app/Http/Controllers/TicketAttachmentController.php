<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    /**
     * Serve ticket attachment file with proper Content-Type headers
     */
    public function show($id)
    {
        try {
            $attachment = TicketAttachment::findOrFail($id);

            // Check if file exists in storage
            if (! Storage::disk('public')->exists($attachment->file_path)) {
                return response()->json([
                    'error' => 'File not found',
                    'path' => $attachment->file_path,
                ], 404);
            }

            // Get file content and mime type
            $file = Storage::disk('public')->get($attachment->file_path);
            $mimeType = Storage::disk('public')->mimeType($attachment->file_path);

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="'.basename($attachment->file_path).'"')
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Error serving ticket attachment: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
