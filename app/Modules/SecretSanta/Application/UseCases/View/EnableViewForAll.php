<?php

namespace App\Modules\SecretSanta\Application\UseCases\View;

use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class EnableViewForAll implements UseCaseInterface
{
    public function __construct(
        private UrlRepositoryInterface $urlRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $updated = $this->urlRepo->resetAllViewed();
        $total = $this->urlRepo->count();

        return [
            'updated' => $updated,
            'total' => $total,
        ];
    }
}
