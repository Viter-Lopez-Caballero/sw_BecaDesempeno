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

class UserController extends Controller
{
    use Filterable;
    use HasOrderableRelations;
    protected string $routeName;
    protected string $source;
    protected Model $model;

    public function __construct()
    {
        $this->routeName = "users.";
        $this->source    = "Core/Security/User/";
        $this->model     = new User();
        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('id', 3); // academic
            })
            ->when($filters->search, function ($query, $search) {
                $query->where('users.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $search . '%')
                    ->orWhereRelation('roles', 'name', 'LIKE', '%' . $search . '%');
            })
            ->when($filters->withTrashed, function ($query) {
                $query->withTrashed();
            });

        $this->applyOrdering($query, $filters->order, $filters->direction);
        $users = $query->paginate($filters->rows)->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'     => 'Gestión de Usuarios',
            'users'     => UserResource::collection($users),
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->where('id', '!=', 3)->get();
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Agregar Usuarios',
            'routeName'     => $this->routeName,
            'roles'         => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
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
