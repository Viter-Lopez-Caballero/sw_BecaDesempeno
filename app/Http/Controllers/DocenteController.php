<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DocenteController extends Controller
{
    public function inicio(Request $request)
    {
        $query = \App\Models\Solicitud::where('user_id', auth()->id())
            ->with(['convocatoria']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('convocatoria', function($qConv) use ($search) {
                      $qConv->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        $solicitudes = $query->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Docente/Dashboard', [
            'solicitudes' => \App\Http\Resources\SolicitudResource::collection($solicitudes),
            'filters' => $request->only('search'),
        ]);
    }

    public function show($id)
    {
        $solicitud = \App\Models\Solicitud::where('user_id', auth()->id())
            ->with(['convocatoria', 'documentos', 'user.institucion', 'user.priorityArea', 'user.subArea'])
            ->findOrFail($id);

        return Inertia::render('Docente/Solicitudes/Show', [
            'solicitud' => (new \App\Http\Resources\SolicitudResource($solicitud))->resolve(),
        ]);
    }

    public function download($id)
    {
        $documento = \App\Models\Documento::findOrFail($id);
        
        // Check ownership via Solicitud
        $solicitud = \App\Models\Solicitud::where('id', $documento->solicitud_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($documento->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($documento->file_path, $documento->name);
    }

    public function stream($id)
    {
        $documento = \App\Models\Documento::findOrFail($id);
        
        $solicitud = \App\Models\Solicitud::where('id', $documento->solicitud_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($documento->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        // Return inline response using the disk's full path
        return response()->file(\Illuminate\Support\Facades\Storage::disk('public')->path($documento->file_path));
    }

    public function convocatorias()
    {
        // Fetch active convocatorias
        // Assuming 'estado' = 'activa' denotes active.
        $convocatorias = \App\Models\Convocatoria::where('estado', 'activa') // Match state string/enum from other files
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Docente/Convocatorias/Index', [
            'convocatorias' => \App\Http\Resources\Catalogos\ConvocatoriaResource::collection($convocatorias),
            'has_active_solicitud' => \App\Models\Solicitud::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->exists(),
        ]);
    }

    public function solicitar($id)
    {
        $convocatoria = \App\Models\Convocatoria::findOrFail($id);

        // Check if user already has an active application (pending or approved)
        // Adjust logic if they can apply to multiple IF they are different years? 
        // User said "una convocatoria a la vez".
        $existing = \App\Models\Solicitud::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
             return redirect()->route('docente.inicio')->with('error', 'Ya tienes una solicitud en proceso. No puedes aplicar a otra.');
        }

        return Inertia::render('Docente/Convocatorias/Solicitar', [
            'convocatoria' => (new \App\Http\Resources\Catalogos\ConvocatoriaResource($convocatoria))->resolve(),
        ]);
    }

    public function storeSolicitud(Request $request)
    {
        $request->validate([
            'convocatoria_id' => 'required|exists:convocatorias,id',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'file_types' => 'required|array', // Maps file index/key to document type name
        ]);

        // Transaction
         \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $solicitud = \App\Models\Solicitud::create([
                'user_id' => auth()->id(),
                'convocatoria_id' => $request->convocatoria_id,
                'status' => 'pending',
            ]);

            foreach ($request->file('files') as $key => $file) {
                // Determine doc name/type based on frontend key mapping
                $typeName = $request->file_types[$key] ?? 'Documento';
                
                $path = $file->store('documentos/' . $solicitud->id, 'public');

                \App\Models\Documento::create([
                    'solicitud_id' => $solicitud->id,
                    'name' => $typeName,
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        });

        return redirect()->route('docente.inicio')->with('success', 'Solicitud enviada correctamente.');
    }
}
