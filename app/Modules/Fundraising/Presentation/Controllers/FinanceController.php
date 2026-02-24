<?php

namespace App\Modules\Fundraising\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\GetChargesByType;
use App\Modules\Fundraising\Application\UseCases\GetUserCharges;
use App\Modules\Transaction\Application\UseCases\GetAllTransactions;
use App\Modules\Transaction\Application\UseCases\GetUserBalances;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function recaudaciones(
        Request $request,
        GetChargesByType $getCharges,
        GetAllTransactions $getAllTransactions,
        GetUserBalances $getUserBalances,
    ): View {
        $type = $request->query('type', 'navidad');

        $data = $getCharges->execute(['type' => $type]);
        $transactions = $getAllTransactions->execute();
        $balanceData = $getUserBalances->execute();

        // Recalculate summary using transactions as source of truth for payments
        $summary = $data['summary'];
        $totalCollected = $balanceData['total'];
        $totalOwed = $summary['total_owed'];
        $totalPending = max(0, $totalOwed - $totalCollected);

        // Count users with actual debt (owed - tx balance > 0)
        $usersWithDebt = 0;
        foreach ($data['users'] as $user) {
            $txBal = $balanceData['balances'][$user['user_id']] ?? 0;
            if (($user['total_owed'] - $txBal) > 0) {
                $usersWithDebt++;
            }
        }

        $summary['total_collected'] = round($totalCollected, 2);
        $summary['total_pending'] = round($totalPending, 2);
        $summary['users_with_debt'] = $usersWithDebt;
        $summary['progress'] = $totalOwed > 0 ? round(($totalCollected / $totalOwed) * 100) : 0;

        return view('modules.fundraising.recaudaciones', [
            'summary' => $summary,
            'users' => $data['users'],
            'type' => $type,
            'transactions' => $transactions,
            'totalFromTransactions' => $totalCollected,
            'userTransactionBalances' => $balanceData['balances'],
        ]);
    }

    public function pagos(
        Request $request,
        GetChargesByType $getCharges,
        GetUserBalances $getUserBalances,
    ): View {
        $type = $request->query('type', 'navidad');

        $data = $getCharges->execute(['type' => $type]);
        $balanceData = $getUserBalances->execute();

        $summary = $data['summary'];
        $totalCollected = $balanceData['total'];
        $totalOwed = $summary['total_owed'];
        $summary['total_pending'] = max(0, round($totalOwed - $totalCollected, 2));
        $summary['progress'] = $totalOwed > 0 ? round(($totalCollected / $totalOwed) * 100) : 0;

        return view('modules.fundraising.pagos', [
            'summary' => $summary,
            'users' => $data['users'],
            'type' => $type,
            'totalFromTransactions' => $totalCollected,
            'userTransactionBalances' => $balanceData['balances'],
        ]);
    }

    public function cargosUsuario(
        int $userId,
        Request $request,
        GetUserCharges $getUserCharges,
    ): View {
        $type = $request->query('type', 'navidad');

        $data = $getUserCharges->execute([
            'user_id' => $userId,
            'type'    => $type,
        ]);

        return view('modules.fundraising.cargos-usuario', [
            'user'    => $data['user'],
            'charges' => $data['charges'],
            'type'    => $type,
        ]);
    }
}
