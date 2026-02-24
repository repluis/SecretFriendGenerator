<?php

namespace App\Modules\User\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Application\UseCases\ChangePassword;
use App\Modules\User\Application\UseCases\UpdateIdentification;
use App\Modules\User\Application\UseCases\UpdateUser;
use App\Modules\User\Presentation\Requests\UpdateProfileIdentificationRequest;
use App\Modules\User\Presentation\Requests\UpdateProfileNameRequest;
use App\Modules\User\Presentation\Requests\UpdateProfilePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('modules.users.profile', [
            'user'         => Auth::user(),
            'navbarActive' => '',
        ]);
    }

    public function updateName(UpdateProfileNameRequest $request, UpdateUser $updateUser): RedirectResponse
    {
        $updateUser->execute([
            'id'   => Auth::id(),
            'name' => $request->input('name'),
        ]);

        return back()->with('name_success', 'Nombre actualizado correctamente.');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request, ChangePassword $changePassword): RedirectResponse
    {
        try {
            $changePassword->execute([
                'id'               => Auth::id(),
                'current_password' => $request->input('current_password'),
                'new_password'     => $request->input('new_password'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withErrors(['current_password' => $e->getMessage()])
                ->withInput();
        }

        return back()->with('password_success', 'Contraseña actualizada correctamente.');
    }

    public function updateIdentification(UpdateProfileIdentificationRequest $request, UpdateIdentification $updateIdentification): RedirectResponse
    {
        try {
            $updateIdentification->execute([
                'id'             => Auth::id(),
                'identification' => $request->input('identification'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withErrors(['identification' => $e->getMessage()])
                ->withInput();
        }

        return back()->with('identification_success', 'Identificación actualizada correctamente.');
    }
}
