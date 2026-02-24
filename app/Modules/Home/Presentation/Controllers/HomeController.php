<?php

namespace App\Modules\Home\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\GetChargesByType;
use App\Modules\Transaction\Application\UseCases\GetUserBalances;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Muestra la landing pública con la tabla de pagos (sin navbar, sin auth).
     *
     * @param GetChargesByType $getCharges - Obtiene los cobros por tipo de evento.
     * @param GetUserBalances $getUserBalances - Obtiene los balances de todos los usuarios.
     * @return View - Vista pública con tabla de pagos (modules.home.index).
     */
    public function index(GetChargesByType $getCharges, GetUserBalances $getUserBalances): View
    {
        $fundraisingData = $getCharges->execute(['type' => 'navidad']);
        $balanceData = $getUserBalances->execute();

        return view('modules.home.index', [
            'users' => $fundraisingData['users'],
            'userTransactionBalances' => $balanceData['balances'],
        ]);
    }
}
