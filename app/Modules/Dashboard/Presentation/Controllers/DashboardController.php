<?php

namespace App\Modules\Dashboard\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\GetChargesByType;
use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Transaction\Application\UseCases\GetUserBalances;
use App\Modules\User\Application\UseCases\GetAllUsers;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    /**
     * Muestra el dashboard administrativo completo (protegido por auth).
     * Incluye estadísticas, tabla de usuarios, y accesos rápidos a módulos.
     *
     * @param GetChargesByType $getCharges - Obtiene los cobros por tipo de evento.
     * @param GetAllUsers $getAllUsers - Obtiene todos los usuarios del sistema.
     * @param GetUserBalances $getUserBalances - Obtiene los balances de todos los usuarios.
     * @return View - Vista del dashboard administrativo (modules.dashboard.index).
     */
    public function index(GetChargesByType $getCharges, GetAllUsers $getAllUsers, GetUserBalances $getUserBalances): View
    {
        $totalPlayers = $this->playerRepo->countActive();
        $totalUrls = $this->urlRepo->count();

        $gameConfig = $this->gameConfigRepo->getCurrent();
        $gameStarted = $gameConfig->isStarted();

        $fundraisingData = $getCharges->execute(['type' => 'navidad']);
        $fundraisingUsers = $fundraisingData['summary']['total_users'];

        $balanceData = $getUserBalances->execute();
        $userBalances = $balanceData['balances'];
        $fundraisingCollected = $balanceData['total'];
        $fundraisingPending = max(0, $fundraisingData['summary']['total_owed'] - $balanceData['total']);

        $users = $getAllUsers->execute();
        $totalUsers = $users->count();

        return view('modules.dashboard.index', compact(
            'totalPlayers',
            'totalUrls',
            'gameStarted',
            'fundraisingCollected',
            'fundraisingPending',
            'fundraisingUsers',
            'users',
            'totalUsers',
            'userBalances',
        ));
    }
}
