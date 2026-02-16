<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function inicio(Request $request)
    {
        $query = \App\Models\Application::where('user_id', auth()->id())
            ->with(['announcement']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('announcement', function($qConv) use ($search) {
                      $qConv->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Teacher/Dashboard', [
            'applications' => \App\Http\Resources\ApplicationResource::collection($applications),
            'filters' => $request->only('search'),
        ]);
    }

    public function show($id)
    {
        $application = \App\Models\Application::where('user_id', auth()->id())
            ->with(['announcement', 'documents', 'user.institution.state', 'user.priorityArea', 'user.subArea'])
            ->findOrFail($id);

        return Inertia::render('Teacher/Applications/Show', [
            'application' => (new \App\Http\Resources\ApplicationResource($application))->resolve(),
        ]);
    }

    public function download($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        
        // Check ownership via Application
        $application = \App\Models\Application::where('id', $document->application_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function stream($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        
        $application = \App\Models\Application::where('id', $document->application_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        // Return inline response using the disk's full path
        return response()->file(\Illuminate\Support\Facades\Storage::disk('public')->path($document->file_path));
    }

    public function convocatorias()
    {
        // Get IDs of announcements the user has ALREADY applied to (any status)
        $appliedAnnouncementIds = \App\Models\Application::where('user_id', auth()->id())
            ->pluck('announcement_id');

        // Fetch active announcements excluding those already applied to
        $announcements = \App\Models\Announcement::where('status', 'activa')
            ->whereNotIn('id', $appliedAnnouncementIds)
            ->with('calendar')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Teacher/Announcements/Index', [
            'announcements' => \App\Http\Resources\Catalog\AnnouncementResource::collection($announcements),
            'has_active_application' => \App\Models\Application::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->exists(),
        ]);
    }

    public function solicitar($id)
    {
        $announcement = \App\Models\Announcement::with(['catalogDocuments', 'calendar'])->findOrFail($id);

        // Check if user already has an active application (pending or approved)
        $existing = \App\Models\Application::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
             return redirect()->route('teacher.dashboard')->with('error', 'Ya tienes una solicitud en proceso. No puedes aplicar a otra.');
        }

        // Get documents for this announcement (from catalog)
        $documents = $announcement->catalogDocuments()
            ->active()
            ->ordered()
            ->get();

        // Get previous application documents for auto-fill
        // We look for the latest *submitted* application (pending/approved/rejected doesn't matter, as long as it has files)
        $previousApp = \App\Models\Application::where('user_id', auth()->id())
            ->whereNotNull('status') // ensuring it was submitted
            ->latest()
            ->with('documents')
            ->first();

        $previousDocuments = $previousApp ? $previousApp->documents : [];

        return Inertia::render('Teacher/Announcements/Apply', [
            'announcement' => (new \App\Http\Resources\Catalog\AnnouncementResource($announcement))->resolve(),
            'catalog_documents' => \App\Http\Resources\Catalog\DocumentResource::collection($documents)->resolve(),
            'previous_documents' => $previousDocuments,
        ]);
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'position_type' => 'required|string|max:255',
            // Files are validated manually to allow mix of upload vs reuse
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf|max:10240', // 10MB max
            'file_types' => 'required|array', // Maps key to document name
            'reused_documents' => 'nullable|array', // Array of doc_ids to reuse
        ]);

        // Custom validation for required documents
        $announcement = \App\Models\Announcement::with('catalogDocuments')->findOrFail($request->announcement_id);
        $requiredDocs = $announcement->catalogDocuments()->where('is_mandatory', true)->pluck('name')->toArray();
        
        $providedDocs = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                if (isset($request->file_types[$key])) {
                    $providedDocs[] = $request->file_types[$key];
                }
            }
        }
        
        // Add reused docs to provided list
        if ($request->reused_documents) {
            foreach ($request->reused_documents as $name => $docId) {
                 $providedDocs[] = $name;
            }
        }

        foreach ($requiredDocs as $req) {
            if (!in_array($req, $providedDocs)) {
                return back()->withErrors(['files' => "El documento '{$req}' es obligatorio."]);
            }
        }

        // Transaction
         \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $application = \App\Models\Application::create([
                'user_id' => auth()->id(),
                'announcement_id' => $request->announcement_id,
                'status' => 'pending',
                'position_type' => $request->position_type,
            ]);

            // Handle New Uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $typeName = $request->file_types[$key] ?? 'Documento';
                    
                    $path = $file->store('documents/' . $application->id, 'public');

                    \App\Models\Document::create([
                        'application_id' => $application->id,
                        'name' => $typeName,
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            // Handle Reused Documents
            if ($request->reused_documents) {
                foreach ($request->reused_documents as $name => $originalDocId) {
                    // Start by checking if we already uploaded a new version of this doc (override)
                    // If the user uploaded a file with this name, it's already handled above.
                    // We need to ensure we don't duplicate. 
                    // However, the frontend should ideally not send both for the same key.
                    // But let's check DB to be safe.
                    $exists = \App\Models\Document::where('application_id', $application->id)
                        ->where('name', $name)
                        ->exists();
                    
                    if (!$exists) {
                        $originalDoc = \App\Models\Document::find($originalDocId);
                        if ($originalDoc) {
                             // We copy the record pointing to the SAME file path
                             // This saves storage space. 
                             // NOTE: If one deletes the file, it might break the other. 
                             // Better approach: Copy field. 
                             // Even better: Soft deletes protect us. 
                             // Let's copy the file to be safe and independent.
                             
                             $newPath = 'documents/' . $application->id . '/' . basename($originalDoc->file_path);
                             \Illuminate\Support\Facades\Storage::disk('public')->copy($originalDoc->file_path, $newPath);

                             \App\Models\Document::create([
                                'application_id' => $application->id,
                                'name' => $name,
                                'file_path' => $newPath,
                                'file_type' => $originalDoc->file_type,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('teacher.dashboard')->with('success', 'Solicitud enviada correctamente.');
    }
}
