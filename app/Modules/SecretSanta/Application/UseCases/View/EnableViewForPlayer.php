<?php

namespace App\Modules\SecretSanta\Application\UseCases\View;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class EnableViewForPlayer implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $nombre = $params['nombre'];

        $player = $this->playerRepo->findActiveByName($nombre);

        if (!$player) {
            return null;
        }

        $urlRecord = $this->urlRepo->findByPlayerId($player->id);

        if (!$urlRecord) {
            return ['error' => 'No se encontró URL para este jugador'];
        }

        $this->urlRepo->updateViewed($urlRecord->id, false);

        $urlRecord = $this->urlRepo->findById($urlRecord->id);

        return [
            'player' => $player->toArray(),
            'url' => $urlRecord->toArray(),
        ];
    }
}
