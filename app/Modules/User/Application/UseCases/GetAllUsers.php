<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class GetAllUsers implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $onlyActive = $params['only_active'] ?? false;

        if ($onlyActive) {
            return $this->userRepo->findAllActive();
        }

        return $this->userRepo->findAll();
    }
}
