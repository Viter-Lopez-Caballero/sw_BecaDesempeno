<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreInstitutionRequest;
use App\Http\Requests\Catalogos\UpdateInstitutionRequest;
use App\Http\Resources\Catalog\InstitutionResource;
use App\Models\State; // Renamed from Estado
use App\Models\Institution; // Renamed from Institucion
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
        $this->source = 'SuperAdmin/Catalog/Institutions/';
        $this->model = new \App\Models\Institution();
        $this->routeName = 'catalog.institutions.';
        $this->permissionPrefix = 'institutions.';

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
            ->with('state')
            ->buscarGlobal($filters->search);

        // Ordenamiento dinámico
        $institutions = $query->orderBy($filters->order, $filters->direction ?? 'asc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'institutions' => InstitutionResource::collection($institutions), // Keep prop
            'title'         => 'Instituciones',
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
            'states'   => State::ordenado('name', 'asc')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        Institution::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'       => 'Editar Institución',
            'routeName'   => $this->routeName,
            'institution' => new InstitutionResource($institution->load('state')),
            'states'     => State::ordenado('name', 'asc')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $institution->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución actualizada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Institución eliminada con éxito');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InstitutionsExport, 'instituciones.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InstitutionsTemplateExport, 'plantilla_instituciones.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\InstitutionsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Instituciones importadas correctamente.');
    }
}
