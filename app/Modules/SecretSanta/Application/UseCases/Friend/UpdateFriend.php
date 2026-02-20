<?php

namespace App\Modules\SecretSanta\Application\UseCases\Friend;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Domain\Services\SecretSantaAssigner;
use App\Modules\Shared\Domain\UseCaseInterface;

class UpdateFriend implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private SecretSantaAssigner $assigner,
    ) {}

    public function execute(array $params = []): mixed
    {
        $playerId = $params['playerId'];

        $player = $this->playerRepo->findById($playerId);

        if (!$player) {
            return null;
        }

        // Buscar amigo por nombre o por ID
        $friendId = $params['friendId'] ?? null;
        if (!$friendId && isset($params['friendName'])) {
            $friend = $this->playerRepo->findActiveByName($params['friendName']);
            $friendId = $friend?->id;
        }

        $urlRecord = $this->urlRepo->findByPlayerId($playerId);

        if ($urlRecord) {
            $this->urlRepo->updateFriends($urlRecord->id, $friendId);
            $urlRecord = $this->urlRepo->findById($urlRecord->id);
        } else {
            $urlHash = $this->assigner->generateUniqueUrlHash();
            while ($this->urlRepo->urlExists($urlHash)) {
                $urlHash = $this->assigner->generateUniqueUrlHash();
            }
            $urlRecord = $this->urlRepo->create($urlHash, $playerId, $friendId);
        }

        return [
            'player' => $player->toArray(),
            'url' => $urlRecord->toArray(),
        ];
    }
}
