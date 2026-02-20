<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class CreateMonthlyCharges implements UseCaseInterface
{
    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $type = $params['type'] ?? 'navidad';
        $baseAmount = $params['base_amount'] ?? 1.00;
        $chargeDate = $params['charge_date']; // e.g. '2026-02-15'

        $userIds = $this->userRepo->getAllIds();
        $created = 0;

        foreach ($userIds as $userId) {
            if ($this->chargeRepo->chargeExistsForMonth($userId, $type, $chargeDate)) {
                continue;
            }

            $this->chargeRepo->create($userId, $type, $baseAmount, $chargeDate);
            $created++;
        }

        return $created;
    }
}
