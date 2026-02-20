<?php

namespace App\Modules\Fundraising\Infrastructure\Jobs;

use App\Modules\Fundraising\Application\UseCases\ApplyDailyPenalties;
use App\Modules\Fundraising\Application\UseCases\CreateMonthlyCharges;
use App\Modules\Fundraising\Application\UseCases\SyncChargesWithTransactions;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDailyFundraisingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        CreateMonthlyCharges $createCharges,
        SyncChargesWithTransactions $syncCharges,
        ApplyDailyPenalties $applyPenalties,
    ): void {
        $today = Carbon::today();

        Log::info('ProcessDailyFundraisingJob started', ['date' => $today->toDateString()]);

        // On the 15th of each month, create $1.00 charges for all active players
        if ($today->day === 15) {
            $created = $createCharges->execute([
                'type' => 'navidad',
                'base_amount' => 1.00,
                'charge_date' => $today->toDateString(),
            ]);

            Log::info('Monthly charges created', ['count' => $created]);
        }

        // Sync charge payment status with transaction balances
        // Marks charges as fully paid if user's transactions cover the total owed
        $synced = $syncCharges->execute(['type' => 'navidad']);

        Log::info('Charges synced with transactions', ['count' => $synced]);

        // Every day, apply $0.05 penalty to unpaid charges older than today
        $penalized = $applyPenalties->execute([
            'date' => $today->toDateString(),
            'penalty_amount' => 0.05,
        ]);

        Log::info('Daily penalties applied', ['count' => $penalized]);
    }
}
