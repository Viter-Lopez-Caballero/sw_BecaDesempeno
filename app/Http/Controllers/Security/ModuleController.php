<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\StoreModuleRequest;
use App\Http\Requests\Security\UpdateModuleRequest;
use App\Models\Module;
use App\Http\Resources\ModuleResource;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class ModuleController extends Controller
{
    use Filterable;
    private Model $model;
    private string $source;
    private string $routeName;

    public function __construct()
    {
        $this->source = 'Core/Security/Module/';
        $this->model = new Module();
        $this->routeName = 'modules.';

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['update', 'edit']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->query()->when($filters->search, function ($query, $search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%');
        });

        $modules = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'modules'   => ModuleResource::collection($modules),
            'title'     => 'Gestión de Módulos',
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'          => 'Agregar Módulos',
            'routeName'      => $this->routeName
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModuleRequest $request)
    {
        Module::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo creado con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        return Inertia::render("{$this->source}Edit", [
            'title'     => 'Editar Módulos',
            'routeName' => $this->routeName,
            'module'    => $module
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $module->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo modificado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo eliminado con éxito');
    }
}
