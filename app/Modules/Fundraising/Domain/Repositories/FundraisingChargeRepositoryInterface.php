<?php

namespace App\Modules\Fundraising\Domain\Repositories;

use App\Modules\Fundraising\Domain\Entities\FundraisingCharge;
use Illuminate\Support\Collection;

interface FundraisingChargeRepositoryInterface
{
    public function findById(int $id): ?FundraisingCharge;

    public function findByType(string $type): Collection;

    public function findUnpaidByType(string $type): Collection;

    public function findByUserAndType(int $userId, string $type): Collection;

    public function findUnpaidOlderThan(string $date): Collection;

    public function chargeExistsForMonth(int $userId, string $type, string $chargeDate): bool;

    public function create(int $userId, string $type, float $baseAmount, string $chargeDate): FundraisingCharge;

    public function addPenalty(int $chargeId, float $penaltyAmount, string $date): void;

    public function setPenalty(int $chargeId, float $penaltyAmount): void;

    public function setPayment(int $chargeId, float $paidAmount, bool $isFullyPaid): void;

    public function markAsPaid(int $chargeId, float $paidAmount): void;

    public function markAsFullyPaid(int $chargeId): void;

    public function getUserSummaryByType(string $type): Collection;

    public function findByTypeWithUser(string $type): Collection;
}
