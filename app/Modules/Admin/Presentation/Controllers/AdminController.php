<?php

namespace App\Modules\Admin\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Infrastructure\Persistence\Models\Role;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administración con lista de usuarios.
     *
     * @return View - Vista del panel de administración.
     */
    public function index(): View
    {
        // Cargar usuarios con sus roles
        $users = User::with('roles')
            ->orderBy('name')
            ->get();

        // Cargar todos los roles disponibles
        $roles = Role::orderBy('name')->get();

        return view('modules.admin.index', [
            'users' => $users,
            'roles' => $roles,
            'navbarActive' => 'admin',
        ]);
    }
}
