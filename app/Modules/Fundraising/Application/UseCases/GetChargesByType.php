<?php

namespace App\Modules\Fundraising\Application\UseCases;

use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Shared\Domain\UseCaseInterface;

class GetChargesByType implements UseCaseInterface
{
    public function __construct(
        private FundraisingChargeRepositoryInterface $chargeRepo,
    ) {}

    public function execute(array $params = []): mixed
    {
        $type = $params['type'] ?? 'navidad';

        $charges = $this->chargeRepo->findByTypeWithUser($type);

        $userSummary = $charges->groupBy('user_id')->map(function ($userCharges) {
            $user = $userCharges->first()->user;
            $totalOwed = $userCharges->sum(fn($c) => (float) $c->base_amount + (float) $c->penalty_amount);
            $totalPaid = $userCharges->sum(fn($c) => (float) $c->paid_amount);
            $totalPenalty = $userCharges->sum(fn($c) => (float) $c->penalty_amount);
            $unpaidCount = $userCharges->where('is_fully_paid', false)->count();

            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_owed' => round($totalOwed, 2),
                'total_paid' => round($totalPaid, 2),
                'total_penalty' => round($totalPenalty, 2),
                'balance' => round($totalOwed - $totalPaid, 2),
                'unpaid_charges' => $unpaidCount,
                'charges' => $userCharges->map(fn($c) => [
                    'id' => $c->id,
                    'base_amount' => (float) $c->base_amount,
                    'penalty_amount' => (float) $c->penalty_amount,
                    'paid_amount' => (float) $c->paid_amount,
                    'charge_date' => $c->charge_date->format('Y-m-d'),
                    'is_fully_paid' => $c->is_fully_paid,
                ])->values()->toArray(),
            ];
        })->sortByDesc('balance')->values();

        $totalCollected = $userSummary->sum('total_paid');
        $totalOwed = $userSummary->sum('total_owed');
        $totalPending = $totalOwed - $totalCollected;
        $totalPenalties = $userSummary->sum('total_penalty');
        $usersWithDebt = $userSummary->where('balance', '>', 0)->count();
        $totalUsers = $userSummary->count();

        return [
            'summary' => [
                'total_collected' => round($totalCollected, 2),
                'total_owed' => round($totalOwed, 2),
                'total_pending' => round($totalPending, 2),
                'total_penalties' => round($totalPenalties, 2),
                'users_with_debt' => $usersWithDebt,
                'total_users' => $totalUsers,
                'progress' => $totalOwed > 0 ? round(($totalCollected / $totalOwed) * 100) : 0,
            ],
            'users' => $userSummary->toArray(),
        ];
    }
}
