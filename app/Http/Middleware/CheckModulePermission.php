<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleName): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Los administradores tienen acceso a todos los módulos
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar si el usuario tiene acceso al módulo
        if (!$user->hasModuleAccess($moduleName)) {
            abort(403, 'No tiene permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
