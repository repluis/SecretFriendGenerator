<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Domain\Services\SecretSantaAssigner;
use App\Modules\Shared\Domain\UseCaseInterface;

class GenerateUrls implements UseCaseInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private SecretSantaAssigner $assigner,
    ) {}

    public function execute(array $params = []): mixed
    {
        $players = $this->playerRepo->findAll();

        if ($players->isEmpty()) {
            return ['error' => 'No hay jugadores registrados'];
        }

        $generated = [];

        foreach ($players as $player) {
            $existingUrl = $this->urlRepo->findByPlayerId($player->id);

            if ($existingUrl) {
                $generated[] = [
                    'id' => $player->id,
                    'nombre' => $player->nombre,
                    'url' => $existingUrl->url,
                    'full_url' => url('/secret-friend/' . $existingUrl->url),
                    'friends' => $existingUrl->friends,
                ];
                continue;
            }

            $uniqueUrl = $this->assigner->generateUniqueUrlHash();

            while ($this->urlRepo->urlExists($uniqueUrl)) {
                $uniqueUrl = $this->assigner->generateUniqueUrlHash();
            }

            $urlRecord = $this->urlRepo->create($uniqueUrl, $player->id, null, false);

            $generated[] = [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $uniqueUrl,
                'full_url' => url('/secret-friend/' . $uniqueUrl),
                'friends' => null,
            ];
        }

        return $generated;
    }
}
