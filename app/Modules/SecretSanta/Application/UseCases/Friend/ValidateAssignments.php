<?php

namespace App\Modules\SecretSanta\Application\UseCases\Friend;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class ValidateAssignments implements UseCaseInterface
{
    public function __construct(
        private UrlRepositoryInterface $urlRepo,
        private PlayerRepositoryInterface $playerRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $urls = $this->urlRepo->findAll();

        $invalidUrls = [];
        $validUrls = [];

        foreach ($urls as $urlRecord) {
            $playerName = null;
            $friendsName = null;

            if ($urlRecord->playerId) {
                $player = $this->playerRepo->findById($urlRecord->playerId);
                $playerName = $player ? $player->nombre : null;
            }

            if ($urlRecord->friends) {
                $friend = $this->playerRepo->findById($urlRecord->friends);
                $friendsName = $friend ? $friend->nombre : null;
            }

            $entry = [
                'id' => $urlRecord->id,
                'url' => $urlRecord->url,
                'player_id' => $urlRecord->playerId,
                'player_name' => $playerName,
                'friends' => $urlRecord->friends,
                'friends_name' => $friendsName,
            ];

            if ($urlRecord->isSelfAssigned()) {
                $invalidUrls[] = $entry;
            } else {
                $validUrls[] = $entry;
            }
        }

        return [
            'is_valid' => count($invalidUrls) === 0,
            'invalid_count' => count($invalidUrls),
            'valid_count' => count($validUrls),
            'invalid_urls' => $invalidUrls,
            'valid_urls' => $validUrls,
        ];
    }
}
