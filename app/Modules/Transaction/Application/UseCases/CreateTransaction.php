<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

class CreateTransaction implements UseCaseInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $userId = $params['user_id'];
        $type = $params['type'];
        $amount = (float) $params['amount'];
        $description = $params['description'] ?? null;

        return $this->transactionRepo->create($userId, $type, $amount, $description);
    }
}
