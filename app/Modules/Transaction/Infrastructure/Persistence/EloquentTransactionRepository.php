<?php

namespace App\Modules\Transaction\Infrastructure\Persistence;

use App\Modules\Transaction\Domain\Entities\Transaction;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Models\TransactionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function findAllWithUser(): Collection
    {
        return TransactionModel::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(int $userId, string $type, float $amount, ?string $description): Transaction
    {
        $model = TransactionModel::create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'active' => true,
        ]);

        return $this->toEntity($model);
    }

    public function toggleActive(int $id): Transaction
    {
        $model = TransactionModel::findOrFail($id);
        $model->update(['active' => !$model->active]);

        return $this->toEntity($model->fresh());
    }

    public function getAllUserBalances(): array
    {
        return DB::table('transactions')
            ->where('active', true)
            ->groupBy('user_id')
            ->selectRaw("user_id, SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END) as balance")
            ->pluck('balance', 'user_id')
            ->map(fn($val) => round((float) $val, 2))
            ->toArray();
    }

    public function getTotalBalance(): float
    {
        $total = DB::table('transactions')
            ->where('active', true)
            ->selectRaw("SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END) as total")
            ->value('total');

        return round((float) ($total ?? 0), 2);
    }

    private function toEntity(TransactionModel $model): Transaction
    {
        return Transaction::fromArray($model->toArray());
    }
}
