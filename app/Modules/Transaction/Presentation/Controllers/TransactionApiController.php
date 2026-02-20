<?php

namespace App\Modules\Transaction\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\SyncChargesWithTransactions;
use App\Modules\Transaction\Application\UseCases\CreateTransaction;
use App\Modules\Transaction\Application\UseCases\GetAllTransactions;
use App\Modules\Transaction\Application\UseCases\ToggleTransactionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionApiController extends Controller
{
    public function index(GetAllTransactions $getAllTransactions): JsonResponse
    {
        $transactions = $getAllTransactions->execute();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function store(
        Request $request,
        CreateTransaction $createTransaction,
        SyncChargesWithTransactions $syncCharges,
    ): JsonResponse {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = $createTransaction->execute($validated);

        // Sync immediately so fundraising_charges reflect the payment right away
        $syncCharges->execute(['type' => 'navidad']);

        return response()->json([
            'success' => true,
            'data' => $transaction->toArray(),
            'message' => 'Transaccion creada exitosamente',
        ]);
    }

    public function toggleActive(int $id, ToggleTransactionStatus $toggleStatus): JsonResponse
    {
        $transaction = $toggleStatus->execute(['transaction_id' => $id]);

        return response()->json([
            'success' => true,
            'data' => $transaction->toArray(),
            'message' => $transaction->active ? 'Transaccion activada' : 'Transaccion desactivada',
        ]);
    }
}
