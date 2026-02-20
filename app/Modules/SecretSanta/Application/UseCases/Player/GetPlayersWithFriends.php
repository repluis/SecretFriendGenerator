<?php

namespace App\Modules\SecretSanta\Application\UseCases\Player;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class GetPlayersWithFriends implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $players = $this->playerRepo->findAllActive();

        return $players->map(function ($player) {
            $friendData = null;
            $urlData = null;

            $urlRecord = $this->urlRepo->findByPlayerId($player->id);

            if ($urlRecord) {
                $urlData = [
                    'url' => $urlRecord->url,
                    'full_url' => url('/secret-friend/' . $urlRecord->url),
                ];

                if ($urlRecord->friends) {
                    $friend = $this->playerRepo->findById($urlRecord->friends);

                    if ($friend) {
                        $friendData = [
                            'id' => $friend->id,
                            'nombre' => $friend->nombre,
                        ];

                        $friendUrl = $this->urlRepo->findByPlayerId($friend->id);
                        if ($friendUrl) {
                            $friendData['url'] = $friendUrl->url;
                            $friendData['full_url'] = url('/secret-friend/' . $friendUrl->url);
                        }
                    } else {
                        $friendData = [
                            'id' => $urlRecord->friends,
                            'not_found' => true,
                        ];
                    }
                }
            }

            return [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $urlData,
                'friend' => $friendData,
                'estado' => $player->estado,
                'created_at' => $player->createdAt,
                'updated_at' => $player->updatedAt,
            ];
        })->toArray();
    }
}
