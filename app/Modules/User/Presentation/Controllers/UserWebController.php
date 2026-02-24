<?php

namespace App\Modules\User\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Application\UseCases\GetAllUsers;
use Illuminate\View\View;

class UserWebController extends Controller
{
    /**
     * Muestra la lista de usuarios del sistema.
     *
     * @param GetAllUsers $getAllUsers - Caso de uso para obtener todos los usuarios.
     * @return View - Vista con la lista de usuarios.
     */
    public function index(GetAllUsers $getAllUsers): View
    {
        // Obtener usuarios directamente con roles para la vista
        $users = \App\Models\User::with('roles')
            ->orderBy('name')
            ->get();

        return view('modules.users.index', [
            'users'        => $users,
            'navbarActive' => 'usuarios',
        ]);
    }
}
