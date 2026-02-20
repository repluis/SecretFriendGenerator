<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class GetAllUrls implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $urls = $this->urlRepo->findAll();

        return $urls->map(function ($url) {
            $player = $url->playerId ? $this->playerRepo->findById($url->playerId) : null;

            return [
                'id' => $url->id,
                'player_id' => $url->playerId,
                'nombre' => $player ? $player->nombre : 'N/A',
                'url' => $url->url,
                'full_url' => url('/secret-friend/' . $url->url),
                'viewed' => $url->viewed,
            ];
        })->toArray();
    }
}
