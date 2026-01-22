<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\StoreRoleRequest;
use App\Http\Requests\Security\UpdateRoleRequest;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Models\Module;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    use Filterable;
    protected string $routeName;
    protected string $source;
    protected Model $model;

    public function __construct()
    {
        $this->routeName = "roles.";
        $this->source    = "Core/Security/Role/";
        $this->model     = new Role();
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

        $roles = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Gestión de Roles',
            'roles'         => RoleResource::collection($roles),
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
            'title'         => 'Agregar Roles',
            'routeName'     => $this->routeName,
            'modules'       => Module::orderBy('key')->get(['id', 'name', 'description', 'key']),
            'permissions'   => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        $role->syncPermissions($request->permissions);
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol creado con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Roles',
            'role'          => new RoleResource($role->load('permissions')),
            'modules'       => Module::orderBy('key')->get(['id', 'name', 'description', 'key']),
            'permissions'   => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
            'routeName'     => $this->routeName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role->syncPermissions($request->permissions);

        Cache::forget("role.{$role->id}");
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol modificado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(role $role)
    {
        Cache::forget("role.{$role->id}");
        $role->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Rol eliminado con éxito!');
    }
}
