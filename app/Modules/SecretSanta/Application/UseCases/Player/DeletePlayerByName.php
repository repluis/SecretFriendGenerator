<?php

namespace App\Modules\SecretSanta\Application\UseCases\Player;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class DeletePlayerByName implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $nombre = $params['nombre'];

        $player = $this->playerRepo->findByName($nombre);

        if (!$player) {
            return null;
        }

        $this->playerRepo->deleteByName($nombre);

        return true;
    }
}
