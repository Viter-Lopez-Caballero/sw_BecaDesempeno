<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CurpService;
use Illuminate\Http\Request;

class CurpController extends Controller
{
    private CurpService $curpService;

    public function __construct(CurpService $curpService)
    {
        $this->curpService = $curpService;
    }

    /**
     * Busca datos por CURP
     */
    public function buscar(Request $request)
    {
        \Log::info('🔍 CurpController->buscar() llamado', [
            'curp' => $request->curp,
            'ip' => $request->ip(),
            'method' => $request->method()
        ]);

        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        $datos = $this->curpService->buscarPorCurp($request->curp);

        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'CURP no encontrado o formato inválido'
            ], 404);
        }

        // Verificar si el CURP ya está registrado
        $usuarioExistente = User::where('curp', $request->curp)->first();
        if ($usuarioExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Este CURP ya está registrado en el sistema'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $datos
        ]);
    }

    /**
     * Verifica el código de 6 dígitos
     */
    public function verificarCodigo(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email_verification_code', $request->code)
            ->where('email_verification_code_expires_at', '>', now())
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'code' => 'El código de verificación es inválido o ha expirado.'
            ]);
        }

        // Marcar el email como verificado
        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ]);

        // Iniciar sesión automáticamente
        auth()->login($user);

        // Redirigir según el rol
        $role = $user->getPrimaryRole();
        
        $redirectRoute = match ($role) {
            'Super Admin' => 'superadmin.inicio',
            'Admin' => 'admin.inicio',
            'Evaluador' => 'evaluador.inicio',
            'Docente' => 'docente.inicio',
            default => 'inicio',
        };

        return redirect()->route($redirectRoute)
            ->with('success', '¡Cuenta verificada exitosamente! Bienvenido al sistema.');
    }

    /**
     * Reenviar código de verificación
     */
    public function reenviarCodigo(Request $request)
    {
        // Obtener email de la sesión o del request
        $email = $request->email ?? session('email');
        
        if (!$email) {
            return back()->withErrors([
                'email' => 'No se pudo determinar el correo electrónico.'
            ]);
        }

        $user = User::where('email', $email)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No se encontró una cuenta sin verificar con este correo.'
            ]);
        }

        // Generar nuevo código
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => now()->addHours(24),
        ]);

        // Reenviar correo
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerificationCode($user, $verificationCode));

        return back()
            ->with('email', $user->email)
            ->with('success', 'Código de verificación reenviado exitosamente. Revisa tu bandeja de entrada.');
    }
}
