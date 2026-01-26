<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $role = $request->user()->getPrimaryRole();
            
            return match ($role) {
                'Super Admin' => redirect()->route('superadmin.inicio'),
                'Admin' => redirect()->route('admin.inicio'),
                'Evaluador' => redirect()->route('evaluador.inicio'),
                'Docente' => redirect()->route('docente.inicio'),
                default => redirect()->route('inicio.dashboard'),
            };
        }

        return $next($request);
    }
}
