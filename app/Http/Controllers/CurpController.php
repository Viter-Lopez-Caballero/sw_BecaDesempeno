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
    public function search(Request $request)
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

        // No validamos si el CURP existe en el sistema
        // La validación de duplicados se hará por correo electrónico durante el registro

        return response()->json([
            'success' => true,
            'data' => $datos
        ]);
    }

    /**
     * Verifica el código de 6 dígitos
     */
    public function verifyCode(Request $request)
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
        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->email_verification_code_expires_at = null;
        $user->save();

        // Iniciar sesión usando el ID para forzar una recarga limpia del usuario
        \Illuminate\Support\Facades\Auth::loginUsingId($user->id, true);
        
        // Regenerar la sesión para seguridad
        request()->session()->regenerate();

        // Redirigir según el rol
        $role = $user->getPrimaryRole();
        
        $redirectRoute = match ($role) {
            'Super Admin' => 'superadmin.dashboard',
            'Admin' => 'admin.dashboard',
            'Evaluador' => 'evaluator.dashboard',
            'Docente' => 'teacher.dashboard',
            default => 'inicio',
        };

        return redirect()->route($redirectRoute)
            ->with('success', '¡Cuenta verificada exitosamente! Bienvenido al sistema.');
    }

    /**
     * Reenviar código de verificación
     */
    public function resendCode(Request $request)
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
        
        \Log::info('🔄 Reenviando código. Código anterior: ' . $user->email_verification_code . ' | Nuevo código: ' . $verificationCode);

        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => now()->addHours(24),
        ]);

        // Reenviar correo
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerificationCode($user, $verificationCode));
        } catch (\Exception $e) {
            \Log::error('❌ Error al reenviar correo de verificación: ' . $e->getMessage());
            // No retornamos error al usuario para evitar bloqueos si el SMTP falla
        }

        return back()
            ->with('email', $user->email)
            ->with('success', 'Código de verificación reenviado exitosamente. Revisa tu bandeja de entrada.');
    }
}
