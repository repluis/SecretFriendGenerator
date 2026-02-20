<?php

namespace App\Modules\Fundraising\Infrastructure\Persistence;

use App\Modules\Fundraising\Domain\Entities\FundraisingCharge;
use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Fundraising\Infrastructure\Persistence\Models\FundraisingChargeModel;
use Illuminate\Support\Collection;

class EloquentFundraisingChargeRepository implements FundraisingChargeRepositoryInterface
{
    public function findById(int $id): ?FundraisingCharge
    {
        $model = FundraisingChargeModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByType(string $type): Collection
    {
        return FundraisingChargeModel::where('type', $type)
            ->orderBy('charge_date', 'desc')
            ->get()
            ->map(fn(FundraisingChargeModel $m) => $this->toEntity($m));
    }

    public function findUnpaidByType(string $type): Collection
    {
        return FundraisingChargeModel::where('type', $type)
            ->where('is_fully_paid', false)
            ->orderBy('charge_date', 'asc')
            ->get()
            ->map(fn(FundraisingChargeModel $m) => $this->toEntity($m));
    }

    public function findByUserAndType(int $userId, string $type): Collection
    {
        return FundraisingChargeModel::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('charge_date', 'asc')
            ->get()
            ->map(fn(FundraisingChargeModel $m) => $this->toEntity($m));
    }

    public function findUnpaidOlderThan(string $date): Collection
    {
        return FundraisingChargeModel::where('is_fully_paid', false)
            ->where('charge_date', '<', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('penalty_last_applied_date')
                  ->orWhere('penalty_last_applied_date', '<', $date);
            })
            ->get()
            ->map(fn(FundraisingChargeModel $m) => $this->toEntity($m));
    }

    public function chargeExistsForMonth(int $userId, string $type, string $chargeDate): bool
    {
        return FundraisingChargeModel::where('user_id', $userId)
            ->where('type', $type)
            ->where('charge_date', $chargeDate)
            ->exists();
    }

    public function create(int $userId, string $type, float $baseAmount, string $chargeDate): FundraisingCharge
    {
        $model = FundraisingChargeModel::create([
            'user_id' => $userId,
            'type' => $type,
            'base_amount' => $baseAmount,
            'penalty_amount' => 0.00,
            'paid_amount' => 0.00,
            'charge_date' => $chargeDate,
            'is_fully_paid' => false,
        ]);

        return $this->toEntity($model);
    }

    public function addPenalty(int $chargeId, float $penaltyAmount, string $date): void
    {
        FundraisingChargeModel::where('id', $chargeId)
            ->increment('penalty_amount', $penaltyAmount, ['penalty_last_applied_date' => $date]);
    }

    public function setPenalty(int $chargeId, float $penaltyAmount): void
    {
        FundraisingChargeModel::where('id', $chargeId)
            ->update(['penalty_amount' => $penaltyAmount]);
    }

    public function setPayment(int $chargeId, float $paidAmount, bool $isFullyPaid): void
    {
        FundraisingChargeModel::where('id', $chargeId)->update([
            'paid_amount' => $paidAmount,
            'is_fully_paid' => $isFullyPaid,
            'paid_at' => $isFullyPaid ? now() : null,
        ]);
    }

    public function markAsPaid(int $chargeId, float $paidAmount): void
    {
        $model = FundraisingChargeModel::findOrFail($chargeId);
        $newPaid = (float) $model->paid_amount + $paidAmount;
        $totalOwed = (float) $model->base_amount + (float) $model->penalty_amount;

        $isFullyPaid = $newPaid >= $totalOwed;

        $model->update([
            'paid_amount' => $newPaid,
            'is_fully_paid' => $isFullyPaid,
            'paid_at' => $isFullyPaid ? now() : null,
        ]);
    }

    public function markAsFullyPaid(int $chargeId): void
    {
        FundraisingChargeModel::where('id', $chargeId)->update([
            'is_fully_paid' => true,
            'paid_at' => now(),
        ]);
    }

    public function getUserSummaryByType(string $type): Collection
    {
        return FundraisingChargeModel::where('type', $type)
            ->with('user')
            ->selectRaw('user_id, SUM(base_amount + penalty_amount) as total_owed, SUM(paid_amount) as total_paid')
            ->groupBy('user_id')
            ->get();
    }

    public function findByTypeWithUser(string $type): Collection
    {
        return FundraisingChargeModel::where('type', $type)
            ->with('user')
            ->orderBy('charge_date', 'desc')
            ->get();
    }

    private function toEntity(FundraisingChargeModel $model): FundraisingCharge
    {
        return FundraisingCharge::fromArray($model->toArray());
    }
}
