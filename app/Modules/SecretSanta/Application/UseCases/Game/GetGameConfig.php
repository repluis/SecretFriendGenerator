<?php

namespace App\Modules\SecretSanta\Application\UseCases\Game;

use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class GetGameConfig implements UseCaseInterface
{
    public function __construct(
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $config = $this->gameConfigRepo->getCurrent();

        return $config->toArray();
    }
}
