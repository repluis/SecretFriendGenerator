<?php

namespace App\Modules\SecretSanta\Application\UseCases\Player;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class GetPlayerById implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $id = $params['id'];

        $player = $this->playerRepo->findById($id);

        return $player?->toArray();
    }
}
