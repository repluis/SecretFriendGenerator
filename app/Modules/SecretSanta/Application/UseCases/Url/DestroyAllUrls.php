<?php

namespace App\Modules\SecretSanta\Application\UseCases\Url;

use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class DestroyAllUrls implements UseCaseInterface
{
    public function __construct(
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $count = $this->urlRepo->count();

        $this->urlRepo->truncate();

        return $count;
    }
}
