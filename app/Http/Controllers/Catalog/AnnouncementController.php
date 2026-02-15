<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreAnnouncementRequest;
use App\Http\Requests\Catalogos\UpdateAnnouncementRequest;
use App\Http\Resources\Catalog\AnnouncementResource;
use App\Models\Announcement;
use App\Models\CatalogDocument;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Announcements/';
        $this->model = new Announcement();
        $this->routeName = 'announcements.';
        $this->permissionPrefix = 'announcements.';

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

        $query = $this->model->query()
            ->with('calendar')
            ->buscarGlobal($filters->search);

        // Ordenamiento dinámico
        $announcements = $query->orderBy($filters->order, $filters->direction ?? 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        // Check restriction: active or pending
        $hasActiveOrPending = Announcement::whereIn('status', ['activa', 'pendiente'])->exists();

        return Inertia::render($this->source . 'Index', [
            'announcements' => AnnouncementResource::collection($announcements),
            'title' => 'Gestión de Convocatorias',
            'routeName' => $this->routeName,
            'filters' => $filters,
            'canCreate' => !$hasActiveOrPending && auth()->user()->can($this->permissionPrefix . 'create'),
            'restrictionMessage' => $hasActiveOrPending ? 'Ya existe una convocatoria Activa o Pendiente.' : 'No tienes permisos para crear convocatorias.',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Agregar Convocatoria',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Default to 'pendiente' on creation
        $data['status'] = 'pendiente'; // DB Enum is still likely Spanish values 'activa','pendiente' unless we changed them? Migration comment said "Enum values might need casting". I strictly renamed COLUMNS. The ENUM VALUES are data. I did not change data. So 'pendiente' is correct.
        
        // Handle File Upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Usar UUID para evitar colisiones y nombres extraños
            $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('announcements', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '_img_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('announcements/images', $imageName, 'public');
            $data['image_path'] = $imagePath;
        }

        // Enforce single active announcement
        if (isset($data['status']) && $data['status'] === 'activa') {
             Announcement::where('status', 'activa')->update(['status' => 'cerrada']);
        }

        $announcement = Announcement::create($data);
        
        // Crear Calendario
        $announcement->calendar()->create([
            'publication_start' => $request->publication_start,
            'registration_start' => $request->registration_start,
            'registration_end' => $request->registration_end,
            'evaluation_start' => $request->evaluation_start,
            'evaluation_end' => $request->evaluation_end,
            'results_start' => $request->results_start,
            'results_end' => $request->results_end,
        ]);
        
        // Vincular automáticamente todos los documentos activos del catálogo
        $activeDocuments = CatalogDocument::where('active', true)->pluck('id');
        if ($activeDocuments->isNotEmpty()) {
            $syncData = [];
            foreach ($activeDocuments as $docId) {
                $syncData[$docId] = ['is_mandatory' => true];
            }
            $announcement->catalogDocuments()->sync($syncData);
        }
        
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement): Response
    {
        // Cargar documentos del catálogo y documentos vinculados
        $catalogDocuments = CatalogDocument::active()->ordered()->get();
        $linkedDocuments = $announcement->catalogDocuments->pluck('id')->toArray();
        
        return Inertia::render("{$this->source}Edit", [
            'title'                  => 'Editar Convocatoria',
            'routeName'              => $this->routeName,
            'announcement'           => new AnnouncementResource($announcement->load('calendar')),
            'requiredDocuments'      => \App\Http\Resources\Catalog\DocumentResource::collection($catalogDocuments)->resolve(),
            'linkedDocuments'        => $linkedDocuments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle File Update
        if ($request->hasFile('file')) {
            // Eliminar archivo anterior si existe
            if ($announcement->file_path) {
                Storage::disk('public')->delete($announcement->file_path);
            }
            
            $file = $request->file('file');
            $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('announcements', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '_img_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('announcements/images', $imageName, 'public');
            $data['image_path'] = $imagePath;
        }

        if (isset($data['status']) && $data['status'] === 'activa') {
            // Desactivar otras convocatorias activas
            Announcement::where('status', 'activa')
                ->where('id', '!=', $announcement->id)
                ->update(['status' => 'cerrada']);
        }

        $announcement->update($data);

        // Actualizar o Crear Calendario
        $announcement->calendar()->updateOrCreate(
            ['announcement_id' => $announcement->id],
            [
                'publication_start' => $request->publication_start,
                'registration_start' => $request->registration_start,
                'registration_end' => $request->registration_end,
                'evaluation_start' => $request->evaluation_start,
                'evaluation_end' => $request->evaluation_end,
                'results_start' => $request->results_start,
                'results_end' => $request->results_end,
            ]
        );

        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        // Delete image if exists
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        // Eliminar archivo si existe
        if ($announcement->file_path) {
            Storage::disk('public')->delete($announcement->file_path);
        }
        
        $announcement->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria eliminada con éxito');
    }

    /**
     * Download the announcement file.
     */
    public function download(Announcement $announcement)
    {
        if (!$announcement->file_path || !Storage::disk('public')->exists($announcement->file_path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return Storage::disk('public')->download(
            $announcement->file_path,
            $announcement->file_name
        );
    }

    /**
     * Update the documents linked to the announcement.
     */
    public function updateDocumentos(Request $request, Announcement $announcement): RedirectResponse
    {
        $request->validate([
            'documentos' => 'nullable|array',
            'documentos.*' => 'exists:catalog_documents,id',
        ]);

        // Sincronizar documentos vinculados
        $documents = $request->input('documentos', []);
        $syncData = [];
        
        foreach ($documents as $docId) {
            $syncData[$docId] = ['is_mandatory' => true]; // Por defecto obligatorio
        }
        
        $announcement->catalogDocuments()->sync($syncData);
        
        return redirect()->route("{$this->routeName}edit", $announcement->id)
            ->with('success', 'Documentos actualizados correctamente');
    }
}
