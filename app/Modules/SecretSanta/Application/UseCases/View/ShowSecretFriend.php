<?php

namespace App\Modules\SecretSanta\Application\UseCases\View;

use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class ShowSecretFriend implements UseCaseInterface
{
    public function __construct(
        private UrlRepositoryInterface $urlRepo,
        private PlayerRepositoryInterface $playerRepo,
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $url = $params['url'];

        $urlRecord = $this->urlRepo->findByUrl($url);

        if (!$urlRecord) {
            return ['error' => 'not_found'];
        }

        $gameConfig = $this->gameConfigRepo->getCurrent();

        if (!$gameConfig->isStarted()) {
            return ['error' => 'game_not_started'];
        }

        if ($urlRecord->viewed) {
            return ['error' => 'already_viewed'];
        }

        if (!$urlRecord->friends) {
            return ['error' => 'no_friend_assigned'];
        }

        $this->urlRepo->updateViewed($urlRecord->id, true);

        $friend = $this->playerRepo->findById($urlRecord->friends);

        return ['friendName' => $friend->nombre];
    }
}
