<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreConvocatoriaRequest;
use App\Http\Requests\Catalogos\UpdateConvocatoriaRequest;
use App\Http\Resources\Catalogos\ConvocatoriaResource;
use App\Models\Convocatoria;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConvocatoriaController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Convocatorias/';
        $this->model = new Convocatoria();
        $this->routeName = 'convocatorias.';
        $this->permissionPrefix = 'convocatorias.';

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
            ->with('calendario')
            ->buscarGlobal($filters->search);

        // Ordenamiento dinámico
        $convocatorias = $query->orderBy($filters->order, $filters->direction ?? 'asc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'convocatorias' => ConvocatoriaResource::collection($convocatorias),
            'title'         => 'Convocatorias',
            'routeName'     => $this->routeName,
            'filters'       => $filters
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
    public function store(StoreConvocatoriaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida del archivo
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('convocatorias', $fileName, 'public');
            
            $data['archivo_path'] = $filePath;
            $data['archivo_nombre'] = $file->getClientOriginalName();
            $data['archivo_tipo'] = $file->getClientMimeType();
            $data['archivo_size'] = $file->getSize();
        }

        $convocatoria = Convocatoria::create($data);
        
        // Vincular automáticamente todos los documentos activos del catálogo
        $documentosActivos = \App\Models\DocumentoCatalogo::where('activo', true)->pluck('id');
        if ($documentosActivos->isNotEmpty()) {
            $syncData = [];
            foreach ($documentosActivos as $docId) {
                $syncData[$docId] = ['es_obligatorio' => true];
            }
            $convocatoria->documentosCatalogo()->sync($syncData);
        }
        
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Convocatoria $convocatoria)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Convocatoria $convocatoria): Response
    {
        // Cargar documentos del catálogo y documentos vinculados
        $documentosCatalogo = \App\Models\DocumentoCatalogo::activos()->ordenado()->get();
        $documentosVinculados = $convocatoria->documentosCatalogo->pluck('id')->toArray();
        
        return Inertia::render("{$this->source}Edit", [
            'title'                  => 'Editar Convocatoria',
            'routeName'              => $this->routeName,
            'convocatoria'           => new ConvocatoriaResource($convocatoria->load('calendario')),
            'documentosCatalogo'     => \App\Http\Resources\Catalogos\DocumentoResource::collection($documentosCatalogo),
            'documentosVinculados'   => $documentosVinculados,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConvocatoriaRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar nuevo archivo
        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($convocatoria->archivo_path) {
                Storage::disk('public')->delete($convocatoria->archivo_path);
            }
            
            $file = $request->file('archivo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('convocatorias', $fileName, 'public');
            
            $data['archivo_path'] = $filePath;
            $data['archivo_nombre'] = $file->getClientOriginalName();
            $data['archivo_tipo'] = $file->getClientMimeType();
            $data['archivo_size'] = $file->getSize();
        }

        $convocatoria->update($data);
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Convocatoria $convocatoria): RedirectResponse
    {
        // Eliminar archivo si existe
        if ($convocatoria->archivo_path) {
            Storage::disk('public')->delete($convocatoria->archivo_path);
        }
        
        $convocatoria->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria eliminada con éxito');
    }

    /**
     * Download the convocatoria file.
     */
    public function download(Convocatoria $convocatoria)
    {
        if (!$convocatoria->archivo_path || !Storage::disk('public')->exists($convocatoria->archivo_path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return Storage::disk('public')->download(
            $convocatoria->archivo_path,
            $convocatoria->archivo_nombre
        );
    }

    /**
     * Update the documents linked to the convocatoria.
     */
    public function updateDocumentos(Request $request, Convocatoria $convocatoria): RedirectResponse
    {
        $request->validate([
            'documentos' => 'nullable|array',
            'documentos.*' => 'exists:documentos_catalogo,id',
        ]);

        // Sincronizar documentos vinculados
        $documentos = $request->input('documentos', []);
        $syncData = [];
        
        foreach ($documentos as $docId) {
            $syncData[$docId] = ['es_obligatorio' => true]; // Por defecto obligatorio
        }
        
        $convocatoria->documentosCatalogo()->sync($syncData);
        
        return redirect()->route("{$this->routeName}edit", $convocatoria->id)
            ->with('success', 'Documentos actualizados correctamente');
    }
}

