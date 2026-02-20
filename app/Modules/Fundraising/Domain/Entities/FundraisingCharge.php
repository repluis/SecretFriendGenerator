<?php

namespace App\Modules\Fundraising\Domain\Entities;

class FundraisingCharge
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $type,
        public readonly float $baseAmount,
        public readonly float $penaltyAmount,
        public readonly float $paidAmount,
        public readonly string $chargeDate,
        public readonly bool $isFullyPaid,
        public readonly ?string $penaltyLastAppliedDate = null,
        public readonly ?string $paidAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'],
            type: $data['type'],
            baseAmount: (float) ($data['base_amount'] ?? 1.00),
            penaltyAmount: (float) ($data['penalty_amount'] ?? 0.00),
            paidAmount: (float) ($data['paid_amount'] ?? 0.00),
            chargeDate: $data['charge_date'],
            isFullyPaid: (bool) ($data['is_fully_paid'] ?? false),
            penaltyLastAppliedDate: $data['penalty_last_applied_date'] ?? null,
            paidAt: $data['paid_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'type' => $this->type,
            'base_amount' => $this->baseAmount,
            'penalty_amount' => $this->penaltyAmount,
            'paid_amount' => $this->paidAmount,
            'charge_date' => $this->chargeDate,
            'penalty_last_applied_date' => $this->penaltyLastAppliedDate,
            'is_fully_paid' => $this->isFullyPaid,
            'paid_at' => $this->paidAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function totalOwed(): float
    {
        return $this->baseAmount + $this->penaltyAmount;
    }

    public function remainingBalance(): float
    {
        return $this->totalOwed() - $this->paidAmount;
    }
}
