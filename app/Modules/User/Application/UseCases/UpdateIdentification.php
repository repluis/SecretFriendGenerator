<?php

namespace App\Modules\User\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use InvalidArgumentException;

class UpdateIdentification implements UseCaseInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $id = $params['id'];
        $identification = trim($params['identification']);

        if ($this->userRepo->existsByIdentification($identification, $id)) {
            throw new InvalidArgumentException('La identificación ya está en uso por otro usuario.');
        }

        return $this->userRepo->update($id, ['identification' => $identification]);
    }
}
