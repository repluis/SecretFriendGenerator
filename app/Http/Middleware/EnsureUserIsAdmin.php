<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Verifica que el usuario autenticado tenga el rol de administrador.
     *
     * @param Request $request - La solicitud HTTP entrante.
     * @param Closure $next - El siguiente middleware en la cadena.
     * @return Response - Respuesta HTTP o redirección.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }

            return redirect()->route('login');
        }

        // Verificar si el usuario tiene el rol de administrador
        if (!Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción. Solo administradores.',
                ], 403);
            }

            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}
