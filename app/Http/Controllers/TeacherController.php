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
        // Fetch active announcements with calendar for date info
        $announcements = \App\Models\Announcement::where('status', 'activa')
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

        return Inertia::render('Teacher/Announcements/Apply', [
            'announcement' => (new \App\Http\Resources\Catalog\AnnouncementResource($announcement))->resolve(),
            'catalog_documents' => \App\Http\Resources\Catalog\DocumentResource::collection($documents)->resolve(),
        ]);
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'file_types' => 'required|array', // Maps file index/key to document type name
        ]);

        // Transaction
         \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $application = \App\Models\Application::create([
                'user_id' => auth()->id(),
                'announcement_id' => $request->announcement_id,
                'status' => 'pending',
            ]);

            foreach ($request->file('files') as $key => $file) {
                // Determine doc name/type based on frontend key mapping
                $typeName = $request->file_types[$key] ?? 'Documento';
                
                $path = $file->store('documents/' . $application->id, 'public');

                \App\Models\Document::create([
                    'application_id' => $application->id,
                    'name' => $typeName,
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        });

        return redirect()->route('teacher.dashboard')->with('success', 'Solicitud enviada correctamente.');
    }
}
