<?php

namespace App\Modules\Auth\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUser
{
    /**
     * Verifica que el usuario esté autenticado antes de permitir el acceso a la ruta.
     * Si no está autenticado, redirige a la página de login.
     *
     * @param Request $request - La petición HTTP entrante.
     * @param Closure $next - El siguiente middleware o controlador en la cadena.
     * @return Response - La respuesta del siguiente middleware o una redirección al login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
