<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;
use Carbon\Carbon;

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

        // Ordered oldest-first. Apply $0.05 per USER per day (not per charge).
        // Calculates ALL missed days since last penalty was applied, catching up
        // automatically if the daily job didn't run for several days.
        $unpaidCharges = $this->chargeRepo->findUnpaidOlderThan($today);

        $penalized = 0;
        $processedUsers = [];

        foreach ($unpaidCharges as $charge) {
            if (isset($processedUsers[$charge->userId])) {
                continue; // Only oldest charge per user accumulates the penalty
            }

            // Count days missed: from last applied date (or charge_date) to today
            $from = $charge->penaltyLastAppliedDate ?? $charge->chargeDate;
            $missedDays = Carbon::parse($from)->diffInDays(Carbon::parse($today));

            if ($missedDays > 0) {
                $this->chargeRepo->addPenalty(
                    $charge->id,
                    round($penaltyAmount * $missedDays, 2),
                    $today
                );
                $processedUsers[$charge->userId] = true;
                $penalized++;
            }
        }

        return $penalized;
    }
}
