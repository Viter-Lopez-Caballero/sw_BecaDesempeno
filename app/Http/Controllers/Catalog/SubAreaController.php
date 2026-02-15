<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Catalogos\StoreSubAreaRequest;
use App\Http\Requests\Catalogos\UpdateSubAreaRequest;
use App\Http\Resources\Catalog\SubAreaResource;
use App\Models\PriorityArea;
use App\Models\SubArea;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubAreaController extends Controller
{
    use Filterable;

    private Model $model;
    private string $source;
    private string $permissionPrefix;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'SuperAdmin/Catalog/SubAreas/';
        $this->model = new SubArea();
        $this->routeName = 'catalog.sub-areas.';
        $this->permissionPrefix = 'sub_areas.';

        $this->middleware("permission:{$this->permissionPrefix}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->permissionPrefix}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->permissionPrefix}edit")->only(['update', 'edit']);
        $this->middleware("permission:{$this->permissionPrefix}delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->query()->with('priorityArea')->buscarGlobal($filters->search);

        $subAreas = $query->orderBy($filters->order, $filters->direction ?? 'asc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'subAreas'  => SubAreaResource::collection($subAreas),
            'title'     => 'Sub Áreas',
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Agregar Sub Área',
            'routeName'     => $this->routeName,
            'priorityAreas' => PriorityArea::ordenado('name', 'asc')->get(['id', 'name']),
        ]);
    }

    public function store(StoreSubAreaRequest $request): RedirectResponse
    {
        SubArea::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Sub Área creada con éxito!');
    }

    public function show(SubArea $subArea)
    {
        abort(404);
    }

    public function edit(SubArea $subArea): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Sub Área',
            'routeName'     => $this->routeName,
            'subArea'       => new SubAreaResource($subArea->load('priorityArea')),
            'priorityAreas' => PriorityArea::ordenado('name', 'asc')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateSubAreaRequest $request, SubArea $subArea): RedirectResponse
    {
        $subArea->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Sub Área actualizada con éxito!');
    }

    public function destroy($id): RedirectResponse
    {
        $subArea = SubArea::findOrFail($id);
        $subArea->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Sub Área eliminada con éxito');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SubAreasExport, 'sub-areas.xlsx');
    }
}
