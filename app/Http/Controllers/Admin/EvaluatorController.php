<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;

class EvaluatorController extends Controller
{
    /**
     * Display a listing of evaluators only.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rows = $request->input('rows', 10);
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Get Evaluador role
        $evaluadorRole = Role::where('name', 'Evaluador')->first();

        if (!$evaluadorRole) {
            return Inertia::render('Admin/Security/Evaluators/Index', [
                'users' => [],
                'filters' => $request->all(['search', 'rows', 'sort_field', 'sort_direction']),
                'registrationLink' => route('register.evaluator'),
            ]);
        }

        // Query users with Evaluador role
        $usuarios = User::role('Evaluador')
            ->with(['roles', 'institution'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('curp', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/Security/Evaluators/Index', [
            'users' => UserResource::collection($usuarios),
            'filters' => $request->all(['search', 'rows', 'sort_field', 'sort_direction']),
            'registrationLink' => route('register.evaluator'),
        ]);
    }

    /**
     * Remove the specified evaluator.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verify user is an evaluator
        if (!$user->hasRole('Evaluador')) {
            return back()->withErrors(['error' => 'El usuario no es un evaluador.']);
        }

        $user->delete();

        return redirect()->route('admin.evaluators.index') // Updated route name
            ->with('success', 'Evaluador eliminado correctamente.');
    }
}
