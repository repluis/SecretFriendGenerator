<?php

namespace App\Modules\SecretSanta\Application\UseCases\Game;

use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class ResetGame implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $playersDeleted = $this->playerRepo->count();
        $urlsDeleted = $this->urlRepo->count();

        $this->playerRepo->deleteAll();
        $this->urlRepo->truncate();

        $config = $this->gameConfigRepo->update(0);

        return [
            'players_deleted' => $playersDeleted,
            'urls_deleted' => $urlsDeleted,
            'game_status' => $config->toArray(),
        ];
    }
}
