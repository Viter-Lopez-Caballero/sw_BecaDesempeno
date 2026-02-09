<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreCalendarioRequest;
use App\Http\Requests\Catalogos\UpdateCalendarioRequest;
use App\Http\Resources\Catalogos\CalendarioResource;
use App\Models\Calendario;
use App\Models\Convocatoria;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarioController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Catalogo/Calendario/';
        $this->model = new Calendario();
        $this->routeName = 'catalogo.calendario.';
        $this->permissionPrefix = 'calendario.';

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
            ->with('convocatoria')
            ->buscarGlobal($filters->search);

        // Ordenamiento dinámico
        $calendarios = $query->orderBy($filters->order, $filters->direction ?? 'asc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'calendarios' => CalendarioResource::collection($calendarios),
            'title'       => 'Calendario de Convocatorias',
            'routeName'   => $this->routeName,
            'filters'     => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'          => 'Agregar Calendario',
            'routeName'      => $this->routeName,
            'convocatorias'  => Convocatoria::ordenado('id', 'desc')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarioRequest $request): RedirectResponse
    {
        Calendario::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Calendario creado con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendario $calendario)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendario $calendario): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Calendario',
            'routeName'     => $this->routeName,
            'calendario'    => new CalendarioResource($calendario->load('convocatoria')),
            'convocatorias' => Convocatoria::ordenado('id', 'desc')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarioRequest $request, Calendario $calendario): RedirectResponse
    {
        $calendario->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Calendario actualizado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendario $calendario): RedirectResponse
    {
        $calendario->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Calendario eliminado con éxito');
    }
}
