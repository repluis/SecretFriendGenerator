<?php

namespace App\Modules\Auth\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\UseCases\LoginUser;
use App\Modules\Auth\Application\UseCases\LogoutUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login. Si ya está autenticado, redirige al home.
     *
     * @return View|RedirectResponse - Vista de login o redirección al home.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('modules.auth.login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     *
     * @param Request $request - Contiene 'identification', 'password', y opcionalmente 'remember'.
     * @param LoginUser $loginUser - Caso de uso que valida credenciales y crea la sesión.
     * @return RedirectResponse - Redirige al home si el login es exitoso, o de vuelta con errores.
     */
    public function login(Request $request, LoginUser $loginUser): RedirectResponse
    {
        $request->validate([
            'identification' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $loginUser->execute([
            'identification' => $request->input('identification'),
            'password' => $request->input('password'),
            'remember' => $request->boolean('remember'),
        ]);

        if (!$result['success']) {
            return back()
                ->withInput($request->only('identification', 'remember'))
                ->withErrors(['login' => $result['message']]);
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Cierra la sesión del usuario y redirige al login.
     *
     * @param LogoutUser $logoutUser - Caso de uso que invalida la sesión y regenera el CSRF token.
     * @return RedirectResponse - Redirige a la página de login.
     */
    public function logout(LogoutUser $logoutUser): RedirectResponse
    {
        $logoutUser->execute();

        return redirect()->route('login');
    }
}
