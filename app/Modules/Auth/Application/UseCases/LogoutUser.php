<?php

namespace App\Modules\Auth\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use Illuminate\Support\Facades\Auth;

class LogoutUser implements UseCaseInterface
{
    /**
     * Ejecuta el caso de uso de cierre de sesión.
     * Invalida la sesión actual y regenera el token CSRF.
     *
     * @param array $params - No se requieren parámetros.
     * @return array{success: bool, message: string} - Resultado indicando éxito con mensaje.
     */
    public function execute(array $params = []): mixed
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return [
            'success' => true,
            'message' => 'Sesión cerrada exitosamente.',
        ];
    }
}
