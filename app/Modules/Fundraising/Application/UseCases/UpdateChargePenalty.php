<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class UpdateChargePenalty implements UseCaseInterface
{
    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
        private SyncChargesWithTransactions $syncCharges,
    ) {}

    public function execute(array $params = []): mixed
    {
        $chargeId      = $params['charge_id'];
        $penaltyAmount = $params['penalty_amount'];
        $type          = $params['type'] ?? 'navidad';

        $this->chargeRepo->setPenalty($chargeId, $penaltyAmount);

        // Re-sync so paid_amount and is_fully_paid reflect the new penalty
        $this->syncCharges->execute(['type' => $type]);

        return $this->chargeRepo->findById($chargeId);
    }
}
