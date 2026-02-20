<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

class ToggleTransactionStatus implements UseCaseInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $transactionId = $params['transaction_id'];

        return $this->transactionRepo->toggleActive($transactionId);
    }
}
