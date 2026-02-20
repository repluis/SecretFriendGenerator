<?php

namespace App\Modules\SecretSanta\Application\UseCases\Player;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class DeleteAllPlayers implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        return $this->playerRepo->deleteAll();
    }
}
