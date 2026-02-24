<?php

namespace App\Modules\Fundraising\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\ApplyDailyPenalties;
use App\Modules\Fundraising\Application\UseCases\CreateMonthlyCharges;
use App\Modules\Fundraising\Application\UseCases\SyncChargesWithTransactions;
use App\Modules\Fundraising\Application\UseCases\UpdateChargePenalty;
use App\Modules\Fundraising\Presentation\Requests\UpdatePenaltyRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FundraisingApiController extends Controller
{
    public function runManual(
        CreateMonthlyCharges $createCharges,
        SyncChargesWithTransactions $syncCharges,
        ApplyDailyPenalties $applyPenalties,
    ): JsonResponse {
        $today = Carbon::today();
        $chargeDate = $today->copy()->day(15)->toDateString(); // 15 del mes actual

        // Crear cobros con fecha del 15
        $created = $createCharges->execute([
            'type' => 'navidad',
            'base_amount' => 1.00,
            'charge_date' => $chargeDate,
        ]);

        // Sync charges with transaction balances before applying penalties
        $syncCharges->execute(['type' => 'navidad']);

        // Calcular dias de multa: desde el dia 16 hasta hoy
        $daysSince = max(0, $today->day - 15);
        $totalPenalized = 0;

        for ($i = 0; $i < $daysSince; $i++) {
            $penaltyDate = $today->copy()->day(16 + $i)->toDateString();
            $totalPenalized += $applyPenalties->execute([
                'date' => $penaltyDate,
                'penalty_amount' => 0.05,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'charges_created' => $created,
                'penalty_days' => $daysSince,
                'penalties_applied' => $totalPenalized,
                'charge_date' => $chargeDate,
            ],
            'message' => "Cobros creados: {$created}, Dias de multa: {$daysSince}",
        ]);
    }

    public function updatePenalty(
        int $chargeId,
        UpdatePenaltyRequest $request,
        UpdateChargePenalty $updatePenalty,
    ): JsonResponse {
        $validated = $request->validated();

        $charge = $updatePenalty->execute([
            'charge_id'      => $chargeId,
            'penalty_amount' => $validated['penalty_amount'],
            'type'           => $validated['type'] ?? 'navidad',
        ]);

        return response()->json([
            'success' => true,
            'data'    => $charge?->toArray(),
            'message' => 'Mora actualizada correctamente',
        ]);
    }

    public function resetData(): JsonResponse
    {
        // Delete in dependency order (FK constraints)
        DB::table('fundraising_charges')->delete();
        DB::table('transactions')->delete();
        DB::table('users')->delete();

        return response()->json([
            'success' => true,
            'message' => 'Datos eliminados: usuarios, transacciones y cobros.',
        ]);
    }
}
