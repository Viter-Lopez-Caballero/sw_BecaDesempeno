<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Modulo;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Mail\UserDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserStore;
use App\Mail\UserUpdate;
use App\Models\Renapo;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    protected string $routeName;
    protected string $source;
    protected string $module = 'usuarios';
    protected User $model;

    public function __construct()
    {
        $this->routeName    = "usuarios.";
        $this->source       = "Seguridad/Usuarios/";
        $this->model        = new User();
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $request->validate(['search' => 'nullable']);

        $usuarios = $this->model::filtro($request->all('search', 'profile'))
            ->with('roles')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

            return Inertia::render("{$this->source}Index", [
                'titulo'        =>  'Gestion de Usuarios',
                'usuarios'      =>  $usuarios,
                'profiles'      =>  Role::get(['id', 'name']),
                'routeName'     =>  $this->routeName,
                'loadingresults'    =>  false,
                'filtro'        =>  $request->all('search', 'profile'),
            ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'titulo'        =>  'Agregar Usuarios',
            'routeName'     =>  $this->routeName,
            'profiles' => Role::with('permissions:id,name,description,module_key')->orderBy('name')->select('id', 'name', 'description')->get(),
            'permisos' => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
            'modulos' => Modulo::orderBy('key')->get(['id', 'nombre', 'descripcion', 'key'])
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $fields = $request->validated();

        $passwordEmail = $fields['password'];

        $fields['password'] = Hash::make($fields['password']);
        $fields['curp'] = strtoupper($fields['curp']);
        $usuario = $this->model::create($fields);
        $roles = Role::whereIn('id', $request->profiles)->get();
        $usuario->syncRoles($roles);

        // Enviamos el correo electrónico al usuario
        Mail::to($usuario->email)->send(new UserStore($usuario, $passwordEmail));

        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario creado con éxito!');
    }

    public function show($request)
    {
        if (Renapo::where('curp', $request)->exists()) {
            $records = Renapo::where('curp', $request)->get();
        }else{
            $request = Http::withToken(env('RENAPO_API_TOKEN'))->post("https://apimarket.mx/api/renapo/grupo/valida-curp", ['curp' => $request])['data'];

            $records = Renapo::create(
                [
                    "curp"      =>  $request['curp'],
                    "nombres"   =>  $request['nombres'],
                    "apellidoPaterno"   =>  $request['apellidoPaterno'],
                    "apellidoMaterno"   =>  $request['apellidoMaterno'],
                ]
            )->where('curp', $request['curp'])->get();
        }
        return response($data = [$records], 200);
    }

    public function edit(User $usuarios): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'titulo'    => 'Editar Usuarios.',
            'routeName' => $this->routeName,
            'pass' => $usuarios,
            'record' => $usuarios->load('roles:id,name', 'permissions:id,name'),
            'profiles' => Role::with('permissions:id,name,description,module_key')->orderBy('name')->select('id', 'name', 'description')->get(),
            'permissions' => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
            'modules' => Modulo::orderBy('key')->get(['id', 'nombre', 'descripcion', 'key'])
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuarios)
    {
        $fields = $request->validated();
        $fields['curp'] = strtoupper($fields['curp']);

        $usuarios->update($fields);
        $roles = Role::whereIn('id', $request->profiles)->get();
        $usuarios->syncRoles($roles);

        Mail::to($usuarios->email)->send(new UserUpdate($usuarios));

        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario modificado con éxito!');
    }

    public function destroy(User $usuarios)
    {
        Mail::to($usuarios->email)->send(new UserDelete($usuarios->name));
        $usuarios->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario eliminado con éxito!');
    }
}