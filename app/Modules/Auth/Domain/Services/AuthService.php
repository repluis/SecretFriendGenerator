<?php

namespace App\Modules\Auth\Domain\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Verifica que la contraseña proporcionada coincida con el hash del usuario.
     *
     * @param User $user - El modelo de usuario con la contraseña hasheada.
     * @param string $password - La contraseña en texto plano a verificar.
     * @return bool - true si la contraseña es correcta, false si no.
     */
    public function verifyCredentials(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    /**
     * Verifica si la cuenta del usuario está activa.
     *
     * @param User $user - El modelo de usuario a verificar.
     * @return bool - true si la cuenta está activa, false si está desactivada.
     */
    public function isAccountActive(User $user): bool
    {
        return (bool) $user->active;
    }
}
