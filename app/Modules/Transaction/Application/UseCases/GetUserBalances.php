<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Shared\Domain\UseCaseInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

class GetUserBalances implements UseCaseInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        return [
            'balances' => $this->transactionRepo->getAllUserBalances(),
            'total' => $this->transactionRepo->getTotalBalance(),
        ];
    }
}
