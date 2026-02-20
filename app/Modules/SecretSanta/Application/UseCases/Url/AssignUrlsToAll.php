<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Domain\Services\SecretSantaAssigner;
use App\Modules\Shared\Domain\UseCaseInterface;

class AssignUrlsToAll implements UseCaseInterface
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

        $assigned = [];

        foreach ($players as $player) {
            $this->urlRepo->deleteByPlayerId($player->id);

            $uniqueUrl = $this->assigner->generateUniqueUrlHash();

            while ($this->urlRepo->urlExists($uniqueUrl)) {
                $uniqueUrl = $this->assigner->generateUniqueUrlHash();
            }

            $urlRecord = $this->urlRepo->create($uniqueUrl, $player->id, null, false);

            $assigned[] = [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $uniqueUrl,
                'full_url' => url('/secret-friend/' . $uniqueUrl),
                'friends' => null,
            ];
        }

        return $assigned;
    }
}
