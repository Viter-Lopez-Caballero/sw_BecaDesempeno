<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreInstitutionRequest;
use App\Http\Requests\Catalogos\UpdateInstitutionRequest;
use App\Http\Resources\Catalogos\InstitutionResource;
use App\Models\Estado;
use App\Models\Institucion;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstitutionController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Catalogos/Institutions/';
        $this->model = new Institucion();
        $this->routeName = 'catalogo.institutions.';
        $this->permissionPrefix = 'instituciones.';

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
            ->with('estado')
            ->buscarGlobal($filters->search);

        // Ordenamiento dinámico
        $instituciones = $query->orderBy($filters->order, $filters->direction ?? 'asc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'instituciones' => InstitutionResource::collection($instituciones),
            'title'         => 'Catálogo de Instituciones',
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
            'title'     => 'Agregar Institución',
            'routeName' => $this->routeName,
            'estados'   => Estado::ordenado('nombre', 'asc')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        Institucion::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institucion $institucion)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institucion $institution): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'       => 'Editar Institución',
            'routeName'   => $this->routeName,
            'institucion' => new InstitutionResource($institution->load('estado')),
            'estados'     => Estado::ordenado('nombre', 'asc')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstitutionRequest $request, Institucion $institution): RedirectResponse
    {
        $institution->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institucion $institution): RedirectResponse
    {
        $institution->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución eliminada con éxito');
    }
}
