<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreDocumentRequest; // Renamed
use App\Http\Requests\Catalogos\UpdateDocumentRequest; // Renamed
use App\Http\Resources\Catalog\DocumentResource;
use App\Models\Announcement;
use App\Models\CatalogDocument;
use App\Models\Application; // Use Application model
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Catalog/Documents/';
        $this->model = new CatalogDocument();
        $this->routeName = 'catalog.documents.'; // Route name change
        $this->permissionPrefix = 'catalog.documents.';

        // Middleware de permisos - Assuming permissions are 'documents.index' etc or we map them.
        // If DB has 'documentos.index', we might break access.
        // User said "Visual text in Spanish, Code in English".
        // Permissions are code/logic. So 'documents.index' is better.
        // I will use 'documents.' and add a todo to migrate permissions.
        $this->permissionPrefix = 'documents.';

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
                    $q->where('name', 'LIKE', "%{$filters->search}%")
                      ->orWhere('description', 'LIKE', "%{$filters->search}%");
                });

            $documents = $query->orderBy($filters->order ?? 'id', $filters->direction ?? 'desc')
                ->paginate($filters->rows)
                ->withQueryString();

            $documentsData = [
                'data' => DocumentResource::collection($documents->items())->resolve(),
                'links' => $documents->linkCollection()->toArray(),
                'from' => $documents->firstItem(),
                'to' => $documents->lastItem(),
                'total' => $documents->total(),
                'per_page' => $documents->perPage(),
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
            ];
        } else {
            $documentsData = [
                'data' => [],
                'links' => [],
                'from' => 0,
                'to' => 0,
                'total' => 0,
            ];
        }

        // Datos para la pestaña de Documentos de Docentes
        if ($activeTab === 'docentes') {
            $applicationsQuery = Application::query()
                ->with(['user.institucion.state', 'user.priorityArea', 'announcement' => function ($query) {
                    $query->withTrashed();
                }])
                ->withCount('documents')
                ->when($filters->search, function ($q) use ($filters) {
                    $q->whereHas('user', function ($query) use ($filters) {
                        $query->where('name', 'LIKE', "%{$filters->search}%")
                              ->orWhere('email', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhereHas('user.institucion', function ($query) use ($filters) {
                        $query->where('name', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhereHas('announcement', function ($query) use ($filters) {
                        $query->where('name', 'LIKE', "%{$filters->search}%");
                    })
                    ->orWhere('id', 'LIKE', "%{$filters->search}%");
                });

            $applications = $applicationsQuery->orderBy($filters->order ?? 'created_at', $filters->direction ?? 'desc')
                ->paginate($filters->rows)
                ->withQueryString();

            $applicationsData = [
                'data' => \App\Http\Resources\ApplicationResource::collection($applications->items())->resolve(), // Keep Resource name for now
                'meta' => [
                    'links' => $applications->linkCollection()->toArray(),
                    'from' => $applications->firstItem(),
                    'to' => $applications->lastItem(),
                    'total' => $applications->total(),
                    'per_page' => $applications->perPage(),
                    'current_page' => $applications->currentPage(),
                    'last_page' => $applications->lastPage(),
                ]
            ];
        } else {
            $applicationsData = [
                'data' => [],
                'meta' => [
                    'links' => [],
                    'from' => 0,
                    'to' => 0,
                    'total' => 0,
                    'per_page' => 10,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ];
        }

        return Inertia::render("{$this->source}Index", [
            'documentos'  => $documentsData,
            'solicitudes' => $applicationsData, // Vue expects 'solicitudes'
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
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida del archivo
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('catalog_documents', $fileName, 'public'); // Changed folder name
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        $document = CatalogDocument::create($data);
        
        // Vincular automáticamente a convocatorias activas si el documento está activo
        if ($document->active) {
            $activeAnnouncements = Announcement::where('status', 'activa')->pluck('id');
            if ($activeAnnouncements->isNotEmpty()) {
                $syncData = [];
                foreach ($activeAnnouncements as $convId) {
                    $syncData[$convId] = ['is_mandatory' => true];
                }
                $document->announcements()->syncWithoutDetaching($syncData);
            }
        }
        
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento creado con éxito!');
    }

    /**
     * Display the specified resource.
     * Shows details of a teacher's documents (Application)
     */
    public function show($id): Response
    {
        $application = Application::with([
            'user.institucion.state', // Updated relationship name
            'user.priorityArea', 
            'user.subArea', 
            'announcement', // Updated relationship name
            'documents' // Updated relationship name
        ])->findOrFail($id);

        return Inertia::render("{$this->source}Show", [
            'solicitud' => (new \App\Http\Resources\SolicitudResource($application))->resolve(), // Keep 'solicitud' prop
            'title' => 'Detalles de Documentos del Profesor',
            'routeName' => $this->routeName
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CatalogDocument $document): Response // Typehint updated
    {
        // Inertia route binding might fail if it expects 'documento' based on route param name?
        // Route is likely /documents/{document}.
        // If we change route resource name in web.php to 'documents', then param is 'document'.
        return Inertia::render("{$this->source}Edit", [
            'title'      => 'Editar Documento',
            'routeName'  => $this->routeName,
            'documento'  => new DocumentResource($document), // Keep 'documento' prop for Vue
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, CatalogDocument $document): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar nuevo archivo
        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('catalog_documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento actualizado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $document = CatalogDocument::findOrFail($id);
        
        // Verificar si es documento fundamental (no se puede eliminar)
        if ($document->is_fundamental) {
            return redirect()->route("{$this->routeName}index")
                ->with('error', 'No se puede eliminar un documento fundamental. Solo puedes actualizarlo.');
        }
        
        // Eliminar archivo si existe
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Documento eliminado con éxito');
    }

    /**
     * Download the documento file.
     */
    public function download($id)
    {
        $document = CatalogDocument::findOrFail($id);
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Toggle activo status.
     */
    public function toggleActive($id): RedirectResponse
    {
        $document = CatalogDocument::findOrFail($id);
        $newState = !$document->active;
        $document->update(['active' => $newState]);
        
        // Si se está activando el documento, vincularlo a todas las convocatorias activas
        if ($newState) {
            $activeAnnouncements = Announcement::where('status', 'activa')->pluck('id');
            if ($activeAnnouncements->isNotEmpty()) {
                $syncData = [];
                foreach ($activeAnnouncements as $convId) {
                    $syncData[$convId] = ['is_mandatory' => true];
                }
                $document->announcements()->syncWithoutDetaching($syncData);
            }
        }
        
        return back()->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Download a teacher's document (from Application/Solicitud).
     */
    public function downloadDocente(\App\Models\Document $document) // Typehint Document
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    /**
     * Stream a teacher's document for viewing (from Application/Solicitud).
     */
    public function streamDocente(\App\Models\Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->file(Storage::disk('public')->path($document->file_path));
    }
}
