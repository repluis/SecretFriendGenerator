<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FundraisingSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Luis Antonio Cedeño',
            'Shirley Cedeño',
            'Katherine Cedeño',
            'Luis Palemon Cedeño',
            'Dolores Posligua',
            'Samara Guerrero',
            'Ahilany Palma',
            'Jean Molina',
            'Jose Palma',
            'Dereck Cedeño',
            'Jeremy Alava',
            'Pedro Guerrero',
        ];

        $chargeDate  = '2026-01-15';
        $paidAt      = '2026-01-15 12:00:00';
        $now         = now()->toDateTimeString();

        foreach ($names as $name) {
            // Generar email único a partir del nombre
            $email = strtolower(
                str_replace([' ', 'ñ', 'é', 'á', 'í', 'ó', 'ú', 'ü'],
                            ['.', 'n', 'e', 'a', 'i', 'o', 'u', 'u'], $name)
            ) . '@familia.com';

            // Crear usuario si no existe
            $existing = DB::table('users')->where('email', $email)->first();
            if ($existing) {
                $userId = $existing->id;
            } else {
                $userId = DB::table('users')->insertGetId([
                    'name'       => $name,
                    'email'      => $email,
                    'password'   => Hash::make('password'),
                    'active'     => true,
                    'created_at' => $chargeDate,
                    'updated_at' => $chargeDate,
                ]);
            }

            // Crear cobro de enero 2026 (ya pagado)
            $chargeExists = DB::table('fundraising_charges')
                ->where('user_id', $userId)
                ->where('type', 'navidad')
                ->where('charge_date', $chargeDate)
                ->exists();

            if (!$chargeExists) {
                DB::table('fundraising_charges')->insert([
                    'user_id'                  => $userId,
                    'type'                     => 'navidad',
                    'base_amount'              => 1.00,
                    'penalty_amount'           => 0.00,
                    'paid_amount'              => 1.00,
                    'charge_date'              => $chargeDate,
                    'penalty_last_applied_date' => null,
                    'is_fully_paid'            => true,
                    'paid_at'                  => $paidAt,
                    'created_at'               => $chargeDate,
                    'updated_at'               => $paidAt,
                ]);
            }

            // Crear transacción de pago
            $txExists = DB::table('transactions')
                ->where('user_id', $userId)
                ->where('type', 'credit')
                ->where('amount', 1.00)
                ->whereDate('created_at', $chargeDate)
                ->exists();

            if (!$txExists) {
                DB::table('transactions')->insert([
                    'user_id'     => $userId,
                    'type'        => 'credit',
                    'amount'      => 1.00,
                    'description' => 'Pago cuota navidad enero 2026',
                    'active'      => true,
                    'created_at'  => $paidAt,
                    'updated_at'  => $paidAt,
                ]);
            }
        }

        $this->command->info('FundraisingSeeder: 12 usuarios, cobros y transacciones de enero creados.');
    }
}
