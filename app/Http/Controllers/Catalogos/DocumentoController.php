<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreDocumentoRequest;
use App\Http\Requests\Catalogos\UpdateDocumentoRequest;
use App\Http\Resources\Catalogos\DocumentoResource;
use App\Models\Convocatoria;
use App\Models\DocumentoCatalogo;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentoController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Catalogo/Documentos/';
        $this->model = new DocumentoCatalogo();
        $this->routeName = 'catalogo.documentos.';
        $this->permissionPrefix = 'documentos.';

        // Middleware de permisos
        $this->middleware("permission:{$this->permissionPrefix}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->permissionPrefix}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->permissionPrefix}edit")->only(['update', 'edit']);
        $this->middleware("permission:{$this->permissionPrefix}delete")->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $activeTab = $request->query('tab', 'requeridos');

        // Datos para la pestaña de Documentos Requeridos
        if ($activeTab === 'requeridos') {
            $query = $this->model->query()
                ->when($filters->search, function ($q) use ($filters) {
                    $q->where('nombre', 'LIKE', "%{$filters->search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$filters->search}%");
                });

            $documentos = $query->orderBy($filters->order ?? 'id', $filters->direction ?? 'desc')
                ->paginate($filters->rows)
                ->withQueryString();

            $documentosData = [
                'data' => DocumentoResource::collection($documentos->items())->resolve(),
                'links' => $documentos->linkCollection()->toArray(),
                'from' => $documentos->firstItem(),
                'to' => $documentos->lastItem(),
                'total' => $documentos->total(),
                'per_page' => $documentos->perPage(),
                'current_page' => $documentos->currentPage(),
                'last_page' => $documentos->lastPage(),
            ];
        } else {
            $documentosData = [
                'data' => [],
                'links' => [],
                'from' => 0,
                'to' => 0,
                'total' => 0,
            ];
        }

        // Datos para la pestaña de Documentos de Docentes
        if ($activeTab === 'docentes') {
            $solicitudesQuery = \App\Models\Solicitud::query()
                ->with(['user.institucion.estado', 'user.priorityArea', 'convocatoria'])
                ->withCount('documentos')
                ->when($filters->search, function ($q) use ($filters) {
                    $q->whereHas('user', function ($query) use ($filters) {
                        $query->where('name', 'LIKE', "%{$filters->search}%")
                              ->orWhere('email', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhereHas('user.institucion', function ($query) use ($filters) {
                        $query->where('nombre', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhereHas('convocatoria', function ($query) use ($filters) {
                        $query->where('nombre', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhere('id', 'LIKE', "%{$filters->search}%");
                });

            $solicitudes = $solicitudesQuery->orderBy($filters->order ?? 'created_at', $filters->direction ?? 'desc')
                ->paginate($filters->rows)
                ->withQueryString();

            $solicitudesData = [
                'data' => \App\Http\Resources\SolicitudResource::collection($solicitudes->items())->resolve(),
                'meta' => [
                    'links' => $solicitudes->linkCollection()->toArray(),
                    'from' => $solicitudes->firstItem(),
                    'to' => $solicitudes->lastItem(),
                    'total' => $solicitudes->total(),
                    'per_page' => $solicitudes->perPage(),
                    'current_page' => $solicitudes->currentPage(),
                    'last_page' => $solicitudes->lastPage(),
                ]
            ];
        } else {
            $solicitudesData = [
                'data' => [],
                'meta' => [
                    'links' => [],
                    'from' => 0,
                    'to' => 0,
                    'total' => 0,
                ]
            ];
        }

        return Inertia::render("{$this->source}Index", [
            'documentos'  => $documentosData,
            'solicitudes' => $solicitudesData,
            'title'       => 'Documentos',
            'routeName'   => $this->routeName,
            'filters'     => $filters,
            'activeTab'   => $activeTab,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Agregar Documento',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida del archivo
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documentos_catalogo', $fileName, 'public');
            
            $data['archivo_path'] = $filePath;
            $data['archivo_nombre'] = $file->getClientOriginalName();
            $data['archivo_tipo'] = $file->getClientMimeType();
            $data['archivo_size'] = $file->getSize();
        }

        $documento = DocumentoCatalogo::create($data);
        
        // Vincular automáticamente a convocatorias activas si el documento está activo
        if ($documento->activo) {
            $convocatoriasActivas = Convocatoria::where('estado', 'activa')->pluck('id');
            if ($convocatoriasActivas->isNotEmpty()) {
                $syncData = [];
                foreach ($convocatoriasActivas as $convId) {
                    $syncData[$convId] = ['es_obligatorio' => true];
                }
                $documento->convocatorias()->syncWithoutDetaching($syncData);
            }
        }
        
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento creado con éxito!');
    }

    /**
     * Display the specified resource.
     * Shows details of a teacher's documents (Solicitud)
     */
    public function show($id): Response
    {
        $solicitud = \App\Models\Solicitud::with([
            'user.institucion.estado', 
            'user.priorityArea', 
            'user.subArea', 
            'convocatoria', 
            'documentos'
        ])->findOrFail($id);

        return Inertia::render("{$this->source}Show", [
            'solicitud' => (new \App\Http\Resources\SolicitudResource($solicitud))->resolve(),
            'title' => 'Detalles de Documentos del Profesor',
            'routeName' => $this->routeName
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentoCatalogo $documento): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'      => 'Editar Documento',
            'routeName'  => $this->routeName,
            'documento'  => new DocumentoResource($documento),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentoRequest $request, DocumentoCatalogo $documento): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar nuevo archivo
        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($documento->archivo_path) {
                Storage::disk('public')->delete($documento->archivo_path);
            }
            
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documentos_catalogo', $fileName, 'public');
            
            $data['archivo_path'] = $filePath;
            $data['archivo_nombre'] = $file->getClientOriginalName();
            $data['archivo_tipo'] = $file->getClientMimeType();
            $data['archivo_size'] = $file->getSize();
        }

        $documento->update($data);
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento actualizado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $documento = DocumentoCatalogo::findOrFail($id);
        
        // Verificar si es documento fundamental (no se puede eliminar)
        if ($documento->es_fundamental) {
            return redirect()->route("{$this->routeName}index")
                ->with('error', 'No se puede eliminar un documento fundamental. Solo puedes actualizarlo.');
        }
        
        // Eliminar archivo si existe
        if ($documento->archivo_path) {
            Storage::disk('public')->delete($documento->archivo_path);
        }
        
        $documento->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento eliminado con éxito');
    }

    /**
     * Download the documento file.
     */
    public function download($id)
    {
        $documento = DocumentoCatalogo::findOrFail($id);
        
        if (!$documento->archivo_path || !Storage::disk('public')->exists($documento->archivo_path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return Storage::disk('public')->download(
            $documento->archivo_path,
            $documento->archivo_nombre
        );
    }

    /**
     * Toggle activo status.
     */
    public function toggleActive($id): RedirectResponse
    {
        $documento = DocumentoCatalogo::findOrFail($id);
        $nuevoEstado = !$documento->activo;
        $documento->update(['activo' => $nuevoEstado]);
        
        // Si se está activando el documento, vincularlo a todas las convocatorias activas
        if ($nuevoEstado) {
            $convocatoriasActivas = Convocatoria::where('estado', 'activa')->pluck('id');
            if ($convocatoriasActivas->isNotEmpty()) {
                $syncData = [];
                foreach ($convocatoriasActivas as $convId) {
                    $syncData[$convId] = ['es_obligatorio' => true];
                }
                $documento->convocatorias()->syncWithoutDetaching($syncData);
            }
        }
        
        return back()->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Download a teacher's document (from Solicitud).
     */
    public function downloadDocente(\App\Models\Documento $documento)
    {
        if (!Storage::disk('public')->exists($documento->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($documento->file_path, $documento->name);
    }

    /**
     * Stream a teacher's document for viewing (from Solicitud).
     */
    public function streamDocente(\App\Models\Documento $documento)
    {
        if (!Storage::disk('public')->exists($documento->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->file(Storage::disk('public')->path($documento->file_path));
    }
}
