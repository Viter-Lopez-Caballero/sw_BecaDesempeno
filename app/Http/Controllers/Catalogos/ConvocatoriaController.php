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
use Inertia\Inertia;
use Inertia\Response;

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
        Convocatoria::create($request->validated());
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
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Convocatoria',
            'routeName'     => $this->routeName,
            'convocatoria'  => new ConvocatoriaResource($convocatoria->load('calendario')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConvocatoriaRequest $request, Convocatoria $convocatoria): RedirectResponse
    {
        $convocatoria->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Convocatoria $convocatoria): RedirectResponse
    {
        $convocatoria->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Convocatoria eliminada con éxito');
    }
}
