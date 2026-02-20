<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class UpdateUser implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        return $this->userRepo->update($params['id'], [
            'name' => $params['name'] ?? null,
            'email' => $params['email'] ?? null,
        ]);
    }
}
