<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class ResetPassword implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $user = $this->userRepo->findById($params['id']);

        return $this->userRepo->updatePassword($user->id, $user->identification);
    }
}
