<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class UpdateUrlPlayer implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $urlId = $params['urlId'];
        $playerId = $params['player_id'] ?? null;

        $url = $this->urlRepo->findById($urlId);

        if (!$url) {
            return null;
        }

        if ($playerId) {
            $existingUrl = $this->urlRepo->findByFriendsExcluding($playerId, $url->id);

            if ($existingUrl) {
                return ['error' => 'Este jugador ya ha seleccionado otra URL'];
            }

            $this->urlRepo->updateFriends($url->id, $playerId);
        } else {
            $this->urlRepo->updateFriends($url->id, null);
        }

        $allUrls = $this->urlRepo->findAll();
        $allPlayers = $this->playerRepo->findAllActive();

        $selectedPlayerIds = [];

        foreach ($allUrls as $urlItem) {
            if ($urlItem->friends !== null) {
                $selectedPlayerIds[$urlItem->id] = $urlItem->friends;
            }
        }

        $urlsData = $allUrls->map(function ($urlItem) use ($allPlayers, $selectedPlayerIds) {
            $player = $urlItem->playerId ? $this->playerRepo->findById($urlItem->playerId) : null;
            $friendPlayer = $urlItem->friends ? $this->playerRepo->findById($urlItem->friends) : null;

            $otherSelectedIds = [];
            foreach ($selectedPlayerIds as $uId => $fId) {
                if ($uId !== $urlItem->id) {
                    $otherSelectedIds[] = $fId;
                }
            }

            $availablePlayers = $allPlayers->filter(function ($p) use ($urlItem, $otherSelectedIds) {
                return !in_array($p->id, $otherSelectedIds) || $urlItem->friends == $p->id;
            })->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                ];
            })->values();

            return [
                'id' => $urlItem->id,
                'url' => $urlItem->url,
                'player_id' => $urlItem->playerId,
                'player_name' => $player ? $player->nombre : null,
                'friends' => $urlItem->friends,
                'friends_name' => $friendPlayer ? $friendPlayer->nombre : null,
                'available_players' => $availablePlayers,
            ];
        })->toArray();

        return ['urls' => $urlsData];
    }
}
