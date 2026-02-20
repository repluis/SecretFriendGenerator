<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class GetUserCharges implements UseCaseInterface
{
    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $userId = $params['user_id'];
        $type   = $params['type'] ?? 'navidad';

        $user    = $this->userRepo->findById($userId);
        $charges = $this->chargeRepo->findByUserAndType($userId, $type);

        return [
            'user'    => $user,
            'charges' => $charges,
            'type'    => $type,
        ];
    }
}
