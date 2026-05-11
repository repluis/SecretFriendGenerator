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

        // Solo crear cobros si ya llegamos al día 15 del mes
        $created = 0;
        if ($today->day >= 15) {
            $created = $createCharges->execute([
                'type' => 'navidad',
                'base_amount' => 1.00,
                'charge_date' => $chargeDate,
            ]);
        }

        // Sync charges with transaction balances before applying penalties
        $syncCharges->execute(['type' => 'navidad']);

        // Apply penalties for today — ApplyDailyPenalties auto-catches up all
        // missed days since the last time the job ran (or since charge_date).
        $totalPenalized = $applyPenalties->execute([
            'date' => $today->toDateString(),
            'penalty_amount' => 0.05,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'charges_created' => $created,
                'penalties_applied' => $totalPenalized,
                'charge_date' => $chargeDate,
            ],
            'message' => "Cobros creados: {$created}, Moras actualizadas: {$totalPenalized}",
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

    public function resetCurrentMonthCharges(): JsonResponse
    {
        $chargeDate = Carbon::today()->day(15)->toDateString();
        $deleted = DB::table('fundraising_charges')
            ->where('charge_date', $chargeDate)
            ->where('type', 'navidad')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cobros de {$chargeDate} eliminados: {$deleted} registro(s).",
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
