<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use InvalidArgumentException;

class ChangePassword implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $id             = $params['id'];
        $currentPassword = $params['current_password'];
        $newPassword    = $params['new_password'];

        if (!$this->userRepo->verifyPassword($id, $currentPassword)) {
            throw new InvalidArgumentException('La contraseña actual es incorrecta.');
        }

        return $this->userRepo->updatePassword($id, $newPassword);
    }
}
