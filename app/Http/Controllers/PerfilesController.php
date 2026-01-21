<?php

namespace App\Http\Controllers;


use App\Http\Requests\StorePerfilesRequest;
use App\Http\Requests\UpdatePerfilesRequest;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Modulo;

class PerfilesController extends Controller
{
    protected string $routeName;
    protected string $source;
    protected string $module = 'perfiles';
    protected Role $model;

    public function __construct()
    {
        $this->routeName    = "perfiles.";
        $this->source       = "Seguridad/Perfiles/";
        $this->model        = new Role();
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['update', 'edit']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy', 'edit']);
    }

    public function index(Request $request): Response
    {
        $records = $this->model;
        $records = $records->when($request->search, function ($query, $search){
            if($search != ''){
                $query->where('name',       'LIKE', "%$search%");
                $query->orWhere('description', 'LIKE', "%$search%");
            }
        })->paginate(5)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'titulo'            => 'Gestion de Roles',
            'records'           =>  $records,
            'routeName'         =>  $this->routeName,
            'loadingResults'    =>  false,
            'search'            =>  $request->search ?? '',
            'status'            => (bool) $request->status,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'titulo'        =>  'Agregar Roles',
            'routeName'     =>  $this->routeName,
            'modulos'       =>  Modulo::orderBy('key')->get(['id', 'nombre', 'descripcion', 'key']),
            'permisos'      =>  Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
        ]);
    }

    public function store(StorePerfilesRequest $request)
    {
        $perfil = Role::create($request->validated());
        $permisos = Permission::whereIn('id', $request->permisos)->get();
        $perfil->syncPermissions($permisos);
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol creado con exito!');
    }

    public function show()
    {
        abort(404);
    }

    public function edit(Role $perfiles)
    {
        return Inertia::render("{$this->source}Edit", [
            'titulo'            =>  'Editar Roles',
            'perfil'            =>  $perfiles->load('permissions:id,name,description,module_key'),
            'modules'           =>  Modulo::orderBy('key')->get(['id','nombre','descripcion','key']),
            'permisos'          =>  Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
            'routeName' => $this->routeName,
        ]);
    }

    public function update(UpdatePerfilesRequest $request, Role $perfiles)
    {
        $perfiles->update($request->validated());
        $permisos = Permission::whereIn('id', $request->permisos)->get();
        $perfiles->syncPermissions($permisos);
        Cache::forget("profile.{$perfiles->id}");
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol modificado con exito!');
    }

    public function destroy(role $perfiles)
    {
        Cache::forget("profiles.{$perfiles->id}");
        $perfiles->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol eliminado con exito!');
    }
}