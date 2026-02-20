<?php

namespace App\Modules\SecretSanta\Application\UseCases\View;

use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class ResetUrlView implements UseCaseInterface
{
    public function __construct(
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $urlId = $params['urlId'];

        $urlRecord = $this->urlRepo->findById($urlId);

        if (!$urlRecord) {
            return null;
        }

        $this->urlRepo->updateViewed($urlId, false);

        return [
            'url_id' => $urlId,
            'viewed' => false,
        ];
    }
}
