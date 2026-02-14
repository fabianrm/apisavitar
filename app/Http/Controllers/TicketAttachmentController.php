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

    /**
     * Delete ticket attachment from database and file system
     */
    public function destroy($id)
    {
        try {
            $attachment = TicketAttachment::findOrFail($id);

            // Delete physical file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
                Log::info('File deleted from storage', [
                    'attachment_id' => $id,
                    'file_path' => $attachment->file_path,
                ]);
            } else {
                Log::warning('File not found in storage, proceeding to delete database record', [
                    'attachment_id' => $id,
                    'file_path' => $attachment->file_path,
                ]);
            }

            // Delete database record
            $attachment->delete();

            return response()->json([
                'message' => 'Archivo adjunto eliminado correctamente',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting ticket attachment: '.$e->getMessage(), [
                'attachment_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
