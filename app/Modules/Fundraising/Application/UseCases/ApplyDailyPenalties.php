<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class ApplyDailyPenalties implements UseCaseInterface
{
    private const PENALTY_AMOUNT = 0.05;

    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $today = $params['date'] ?? now()->toDateString();
        $penaltyAmount = $params['penalty_amount'] ?? self::PENALTY_AMOUNT;

        // Get all unpaid charges where the charge_date is before today
        // (penalty starts the day after the charge was created)
        $unpaidCharges = $this->chargeRepo->findUnpaidOlderThan($today);

        $penalized = 0;

        foreach ($unpaidCharges as $charge) {
            $this->chargeRepo->addPenalty($charge->id, $penaltyAmount, $today);
            $penalized++;
        }

        return $penalized;
    }
}
