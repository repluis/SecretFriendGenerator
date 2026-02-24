<?php

namespace App\Modules\Dashboard\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\GetChargesByType;
use App\Modules\SecretSanta\Application\UseCases\Game\GetGameConfig;
use App\Modules\SecretSanta\Application\UseCases\Player\GetAllPlayers;
use App\Modules\SecretSanta\Application\UseCases\Url\GetAllUrls;
use App\Modules\Transaction\Application\UseCases\GetUserBalances;
use App\Modules\User\Application\UseCases\GetAllUsers;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(
        GetAllUsers $getAllUsers,
        GetAllPlayers $getAllPlayers,
        GetAllUrls $getAllUrls,
        GetGameConfig $getGameConfig,
        GetChargesByType $getCharges,
        GetUserBalances $getUserBalances,
    ): View {
        $users = $getAllUsers->execute();
        $totalUsers = $users->count();

        $players = $getAllPlayers->execute();
        $totalPlayers = count($players);

        $urls = $getAllUrls->execute();
        $totalUrls = count($urls);

        $gameConfig = $getGameConfig->execute();
        $gameStarted = $gameConfig['startgame'] === 1;

        $fundraisingData = $getCharges->execute(['type' => 'navidad']);
        $fundraisingPending   = $fundraisingData['summary']['total_pending'];
        $fundraisingCollected = $fundraisingData['summary']['total_collected'];
        $fundraisingUsers     = $fundraisingData['summary']['total_users'];
        $usersWithCharges     = $fundraisingData['users']; // Datos de usuarios con cobros y moras

        $balanceData  = $getUserBalances->execute();
        $userBalances = $balanceData['balances'];

        return view('modules.home.home', [
            'navbarActive'        => 'dashboard',
            'users'               => $users,
            'totalUsers'          => $totalUsers,
            'totalPlayers'        => $totalPlayers,
            'totalUrls'           => $totalUrls,
            'gameStarted'         => $gameStarted,
            'fundraisingPending'  => $fundraisingPending,
            'fundraisingCollected'=> $fundraisingCollected,
            'fundraisingUsers'    => $fundraisingUsers,
            'userBalances'        => $userBalances,
            'usersWithCharges'    => $usersWithCharges, // Agregar datos de moras
        ]);
    }
}
