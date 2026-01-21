<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StorePermisosRequest;
use App\Http\Requests\UpdatePermisosRequest;
use Spatie\Permission\Models\Permission;
use App\Models\Modulo;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    protected string $routeName;
    protected string $source;
    protected string $module = 'permissions';
    protected Permission $model;

    public function __construct()
    {
        $this->routeName    = "permissions.";
        $this->source      = "Seguridad/Permisos/";
        $this->model        = new Permission();
        
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $records = $this->model;
        $records = $records->when($request->search, function($query, $search) {
            if($search != ''){
                $query->where('name',       'LIKE', "%$search%");
                $query->orWhere('description', 'LIKE', "%$search%");
                $query->orWhere('module_key',   'LIKE', "%$search");
            }
        })->paginate(10)->withQueryString();
        return Inertia::render("{$this->source}Index", [
            'titulo'            => 'Gestion de Permisos',
            'records'           => $records,
            'routeName'         => $this->routeName,
            'loadingResults'    => false,
            'search'            => $request->search ?? '',
            'status'            => (bool) $request->status,
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'titulo'    =>  'Agregar Permisos',
            'routeName' =>  $this->routeName,
            'modulos'   =>  Modulo::orderBy('id')->get(),
        ]);
    }

    public function store(StorePermisosRequest $request): RedirectResponse
    {
        $this->model::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Permiso creado con exito!');
    }

    public function edit(Permission $permission): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'titulo'        =>  'Editar Permisos',
            'routeName'     =>  $this->routeName,
            'modulos'       =>  Modulo::orderBy('id')->get(),
            'record'        => $permission,
        ]);
    }

    public function update(UpdatePermisosRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        Cache::forget('permissions');
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Permiso modificado con exito!');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Permiso eliminado con exito!');
    }
}
