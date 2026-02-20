<?php

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Entities\Transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function findAllWithUser(): Collection;

    public function create(int $userId, string $type, float $amount, ?string $description): Transaction;

    public function toggleActive(int $id): Transaction;

    public function getAllUserBalances(): array;

    public function getTotalBalance(): float;
}
