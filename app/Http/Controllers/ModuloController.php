<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Http\Requests\StoreModuloRequest;
use App\Http\Requests\UpdateModuloRequest;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class ModuloController extends Controller
{
    private Modulo $model;
    private string $source;
    private string $routeName;
    private string $module = 'modulo';

    
    public function __construct()
    {
        // $this->middleware('auth'); // Handled by routes usually
        $this->source = 'Seguridad/Modulos/';
        $this->model = new Modulo();
        $this->routeName = 'modulo.';

        // Permissions middleware
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['update', 'edit']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
        $this->middleware("permission:{$this->module}.recover")->only(['recover']);
    }

    public function index(Request $request): Response
    {
        $request['status'] = $request->status === null ? true : $request->status;
        $records = $request->status == '0' ? $this->model->onlyTrashed() : $this->model;
        $records = $records->when($request->search, function ($query, $search) {
            if ($search != '') {
                $query->where('nombre', 'LIKE', '%' . $search . '%')
                    ->orWhere('descripcion', 'LIKE', '%' . $search . '%');
            }
        });

        return Inertia::render("{$this->source}Index", [
            'modulos'           =>  $records->paginate(5),
            'titulo'            =>  'Gestion de Modulos',
            'routeName'         =>  $this->routeName,
            'loadingResults'    => false,
            'search'            => $request->search ?? '',
            'status'            => (bool) $request->status,
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'titulo'            => 'Agregar Modulos',
            'routeName'         => $this->routeName
        ]);
    }

    public function store(StoreModuloRequest $request)
    {
        Modulo::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Modulo creado con exito! ');
    }

    public function show(Modulo $modulo)
    {
        abort(404);
    }

    public function edit(Modulo $modulo)
    {
        return Inertia::render("{$this->source}Edit", [
            'titulo'            =>  'Editar Modulos',
            'routeName'         =>  $this->routeName,
            'modulo'            =>  $modulo
        ]);
    }

    public function update(UpdateModuloRequest $request, Modulo $modulo): RedirectResponse
    {
        $modulo->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('succes', 'Modulo modificado con exito!');
    }

    public function destroy(Modulo $modulo)
    {
        $modulo->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Modulo eliminado con exito!');
    }

    public function recover($id){
        $module = Modulo::withoutTrashed()->find($id);
        if ($module){
            $module->restore();
            return redirect()->route("{$this->routeName}index")->with('success', 'Modulo recuperado con exito!');
        }
        return redirect()->route("{$this->routeName}index")->with('error', 'Error, no se puede recuperar el modulo');
    }
}
