<?php

namespace App\Modules\Auth\Application\UseCases;

use App\Modules\Auth\Domain\Repositories\AuthRepositoryInterface;
use App\Modules\Auth\Domain\Services\AuthService;
use App\Modules\Shared\Domain\UseCaseInterface;
use Illuminate\Support\Facades\Auth;

class LoginUser implements UseCaseInterface
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
        private AuthService $authService,
    ) {}

    /**
     * Ejecuta el caso de uso de inicio de sesión.
     *
     * @param array $params - ['identification' => string, 'password' => string, 'remember' => bool].
     * @return array{success: bool, message: string} - Resultado indicando éxito o fallo con mensaje.
     */
    public function execute(array $params = []): mixed
    {
        $identification = $params['identification'] ?? '';
        $password = $params['password'] ?? '';

        if (empty($identification) || empty($password)) {
            return [
                'success' => false,
                'message' => 'La identificación y la contraseña son obligatorias.',
            ];
        }

        $user = $this->authRepository->findByIdentification($identification);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ];
        }

        if (!$this->authService->isAccountActive($user)) {
            return [
                'success' => false,
                'message' => 'Esta cuenta se encuentra desactivada.',
            ];
        }

        if (!$this->authService->verifyCredentials($user, $password)) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ];
        }

        Auth::login($user, $params['remember'] ?? false);
        request()->session()->regenerate();

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
        ];
    }
}
