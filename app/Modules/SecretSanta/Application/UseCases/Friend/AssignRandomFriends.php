<?php

namespace App\Modules\SecretSanta\Application\UseCases\Friend;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Domain\Services\SecretSantaAssigner;
use App\Modules\Shared\Domain\UseCaseInterface;

class AssignRandomFriends implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private SecretSantaAssigner $assigner,
    ) {}

    public function execute(array $params = []): mixed
    {
        $activePlayers = $this->playerRepo->findAllActive();

        if ($activePlayers->count() < 2) {
            return ['error' => 'Se necesitan al menos 2 jugadores'];
        }

        $playerIds = $activePlayers->map(fn($player) => $player->id)->toArray();

        $assignments = $this->assigner->assignFriends($playerIds);

        $assigned = [];

        foreach ($assignments as $playerId => $friendId) {
            $player = $this->playerRepo->findById($playerId);
            $friend = $this->playerRepo->findById($friendId);

            $urlRecord = $this->urlRepo->findByPlayerId($playerId);

            if (!$urlRecord) {
                $urlHash = $this->assigner->generateUniqueUrlHash();
                $urlRecord = $this->urlRepo->create($urlHash, $playerId, $friendId);
            } else {
                $this->urlRepo->updateFriends($urlRecord->id, $friendId);
            }

            $assigned[] = [
                'player_name' => $player->nombre,
                'friend_name' => $friend->nombre,
                'url' => $urlRecord->url,
            ];
        }

        return $assigned;
    }
}
