<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

class SyncChargesWithTransactions implements UseCaseInterface
{
    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
        private TransactionRepositoryInterface $transactionRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $type = $params['type'] ?? 'navidad';

        $userBalances = $this->transactionRepo->getAllUserBalances();

        // Get all charges to know which users to process
        $allCharges = $this->chargeRepo->findByType($type);
        $userIds = $allCharges->map(fn($c) => $c->userId)->unique()->values();

        $synced = 0;

        foreach ($userIds as $userId) {
            $txBalance = (float) ($userBalances[$userId] ?? 0);

            // All charges for this user ordered oldest first (asc by charge_date)
            // Payments cover the oldest debt first
            $userCharges = $this->chargeRepo->findByUserAndType($userId, $type);

            $remaining = $txBalance;

            foreach ($userCharges as $charge) {
                $totalOwed = $charge->baseAmount + $charge->penaltyAmount;

                if ($remaining >= $totalOwed) {
                    // Balance covers this charge completely
                    $this->chargeRepo->setPayment($charge->id, $totalOwed, true);
                    $remaining -= $totalOwed;
                    if (!$charge->isFullyPaid) {
                        $synced++;
                    }
                } elseif ($remaining > 0) {
                    // Balance covers this charge partially — mora keeps running
                    $this->chargeRepo->setPayment($charge->id, $remaining, false);
                    $remaining = 0;
                } else {
                    // No balance left — reset any previously set paid_amount
                    $this->chargeRepo->setPayment($charge->id, 0.00, false);
                }
            }
        }

        return $synced;
    }
}
