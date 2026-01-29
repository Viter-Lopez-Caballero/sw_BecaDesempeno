<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\StoreUserRequest;
use App\Http\Requests\Security\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Inertia\Response;
use Inertia\Inertia;
use App\Traits\HasOrderableRelations;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Http\Controllers\SecurityController;

class UserController extends SecurityController
{
    use Filterable;
    use HasOrderableRelations;
    protected string $routeName;
    protected string $permissionPrefix;
    protected string $source;
    protected Model $model;

    public function __construct()
    {
        $this->routeName = "seguridad.users.";
        $this->permissionPrefix = "users.";
        $this->source    = "SuperAdmin/Seguridad/Usuarios/";
        $this->model     = new User();
        
        $this->middleware("permission:{$this->permissionPrefix}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->permissionPrefix}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->permissionPrefix}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->permissionPrefix}delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        
        $query = $this->model->query()
            ->with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('id', 3); 
            })
            ->buscarGlobal($filters->search)
            ->when($filters->withTrashed, fn($q) => $q->withTrashed());

        // Ordenamiento dinámico usando los filtros del trait
        $users = $query->orderBy($filters->order, $filters->direction ?? 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'usuarios'  => UserResource::collection($users),
            'title'     => 'Gestión de Usuarios',
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->where('id', '!=', 3)->get();
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Agregar Usuarios',
            'routeName'     => $this->routeName,
            'roles'         => $roles,
        ]);
    }

    
    public function store(StoreUserRequest $request)
    {
        $user = $this->model::create($request->validated());
        $user->syncRoles($request->roles);

        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->where('id', '!=', 3)->get();
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Usuarios',
            'routeName'     => $this->routeName,
            'user'          => new UserResource($user->load('roles')),
            'roles'         => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!$request->filled('password')) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($request->roles);
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario modificado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario eliminado con éxito');
    }

    protected function getOrderableRelations(): array
    {
        return [
            'roles' => ['roles', 'id', 'name'],
        ];
    }
}