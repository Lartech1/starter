<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->user()->role->slug === 'legal_officer') {
            // Legal officers see all documents
            $query->with(['uploadedBy', 'legalOfficer']);
        } else {
            // Others see only their own
            $query->where('uploaded_by', $request->user()->id);
        }

        return response()->json([
            'documents' => $query->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:cac,firs,property_title,contract,receipt,other',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_url' => Storage::url($path),
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Document uploaded', 'document' => $document], 201);
    }

    public function destroyRequest(Request $request, $id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        if ($document->uploaded_by !== $request->user()->id && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Document deleted']);
    }

    public function verify(Request $request, $id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        if ($request->user()->role->slug !== 'legal_officer' && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $document->update([
            'verified' => $validated['verified'],
            'legal_officer_id' => $request->user()->id,
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json(['message' => 'Document verification updated', 'document' => $document]);
    }
}
