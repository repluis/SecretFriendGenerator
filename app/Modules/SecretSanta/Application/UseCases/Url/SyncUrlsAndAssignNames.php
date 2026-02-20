<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Domain\Services\SecretSantaAssigner;
use App\Modules\Shared\Domain\UseCaseInterface;

class SyncUrlsAndAssignNames implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private SecretSantaAssigner $assigner,
    ) {}

    public function execute(array $params = []): mixed
    {
        $this->urlRepo->truncate();

        $players = $this->playerRepo->findAllActive();
        $shuffledPlayers = $players->shuffle();

        if ($shuffledPlayers->count() < 2) {
            return ['error' => 'Se necesitan al menos 2 jugadores activos'];
        }

        $assigned = [];

        foreach ($shuffledPlayers as $player) {
            $uniqueUrl = $this->assigner->generateUniqueUrlHash();

            while ($this->urlRepo->urlExists($uniqueUrl)) {
                $uniqueUrl = $this->assigner->generateUniqueUrlHash();
            }

            $urlRecord = $this->urlRepo->create($uniqueUrl, $player->id, null, false);

            $assigned[] = [
                'url_id' => $urlRecord->id,
                'url' => $urlRecord->url,
                'player_id' => $player->id,
                'player_name' => $player->nombre,
            ];
        }

        return [
            'total_players' => $shuffledPlayers->count(),
            'total_urls' => count($assigned),
            'assigned' => $assigned,
        ];
    }
}
