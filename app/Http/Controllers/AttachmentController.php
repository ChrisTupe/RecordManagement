<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    // Store attachment
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'transaction_id' => 'nullable|exists:transactions,id',
        ]);

        if ($request->file('file')) {
            $path = $request->file('file')->store('attachments', 'public');
            $fileName = $request->file('file')->getClientOriginalName();

            $document->attachments()->create([
                'file_name' => $fileName,
                'file_path' => $path,
                'transaction_id' => $validated['transaction_id'] ?? null,
            ]);
        }

        return redirect()->route('documents.show', $document)->with('success', 'Attachment uploaded successfully.');
    }

    // Delete attachment
    public function destroy(Document $document, Attachment $attachment)
    {
        // Delete file from storage
        if (\Storage::disk('public')->exists($attachment->file_path)) {
            \Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();
        return redirect()->route('documents.show', $document)->with('success', 'Attachment deleted successfully.');
    }

    // Download attachment
    public function download(Document $document, Attachment $attachment)
    {
        return \Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    // View attachment (inline, especially for PDFs)
    public function view(Document $document, Attachment $attachment)
    {
        $filePath = \Storage::disk('public')->path($attachment->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"'
        ]);
    }
}
