<?php

namespace App\Modules\User\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Application\UseCases\GetAllUsers;
use Illuminate\View\View;

class UserWebController extends Controller
{
    public function index(GetAllUsers $getAllUsers): View
    {
        $users = $getAllUsers->execute();

        return view('modules.users.index', [
            'users'        => $users,
            'navbarActive' => 'usuarios',
        ]);
    }
}
