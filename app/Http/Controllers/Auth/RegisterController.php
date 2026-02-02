<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institucion;
use App\Models\PriorityArea;
use App\Mail\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro
     */
    public function create()
    {
        return Inertia::render('Auth/Register', [
            'instituciones' => Institucion::ordenado('nombre')->get(['id', 'nombre']),
            'priorityAreas' => PriorityArea::with('subAreas')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Maneja el registro del usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'curp' => 'required|string|size:18|unique:users,curp',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'institucion_id' => 'required|exists:instituciones,id',
            'priority_area_id' => 'required|exists:priority_areas,id',
            'sub_area_id' => 'required|exists:sub_areas,id',
        ]);

        // Generar código de verificación de 6 dígitos
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Crear el usuario sin iniciar sesión
        $user = User::create([
            'curp' => strtoupper($request->curp),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'institucion_id' => $request->institucion_id,
            'priority_area_id' => $request->priority_area_id,
            'sub_area_id' => $request->sub_area_id,
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => now()->addHours(24),
        ]);

        // Asignar rol de Docente
        $user->assignRole('Docente');

        // Enviar correo de verificación
        Mail::to($user->email)->send(new VerificationCode($user, $verificationCode));

        // Redirigir a verificación sin iniciar sesión
        return redirect()->route('verification.notice')
            ->with('email', $user->email)
            ->with('success', 'Registro exitoso. Por favor verifica tu correo electrónico.');
    }
}
