<?php

namespace App\Modules\SecretSanta\Application\UseCases\Game;

use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class UpdateGameConfig implements UseCaseInterface
{
    public function __construct(
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $startgame = $params['startgame'];

        $config = $this->gameConfigRepo->update($startgame);

        return $config->toArray();
    }
}
