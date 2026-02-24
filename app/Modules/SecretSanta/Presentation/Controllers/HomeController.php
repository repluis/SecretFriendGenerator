<?php

namespace App\Modules\SecretSanta\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fundraising\Application\UseCases\GetChargesByType;
use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\Transaction\Application\UseCases\GetUserBalances;
use App\Modules\User\Application\UseCases\GetAllUsers;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    /**
     * Muestra la landing pública con la tabla de pagos (sin navbar).
     *
     * @param GetChargesByType $getCharges - Obtiene los cobros por tipo de evento.
     * @param GetUserBalances $getUserBalances - Obtiene los balances de todos los usuarios.
     * @return View - Vista pública con tabla de pagos (modules.dashboard.index).
     */
    public function index(GetChargesByType $getCharges, GetUserBalances $getUserBalances): View
    {
        $fundraisingData = $getCharges->execute(['type' => 'navidad']);
        $balanceData = $getUserBalances->execute();

        return view('modules.dashboard.index', [
            'users' => $fundraisingData['users'],
            'userTransactionBalances' => $balanceData['balances'],
        ]);
    }

    /**
     * Muestra el dashboard administrativo completo (protegido por auth).
     *
     * @param GetChargesByType $getCharges - Obtiene los cobros por tipo de evento.
     * @param GetAllUsers $getAllUsers - Obtiene todos los usuarios del sistema.
     * @param GetUserBalances $getUserBalances - Obtiene los balances de todos los usuarios.
     * @return View - Vista del dashboard administrativo (modules.dashboard.home).
     */
    public function home(GetChargesByType $getCharges, GetAllUsers $getAllUsers, GetUserBalances $getUserBalances): View
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

        return view('modules.dashboard.home', compact(
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

    /**
     * Muestra la vista del juego de amigo secreto (pública).
     *
     * @return View - Vista del juego con URLs, jugadores y estado (modules.secret-santa.index).
     */
    public function game(): View
    {
        $urls = $this->urlRepo->findAllWithRelations();

        $allPlayers = $this->playerRepo->getActiveModelsOrderedByName();

        $gameConfig = $this->gameConfigRepo->getCurrent();
        $gameStarted = $gameConfig->isStarted();

        return view('modules.secret-santa.index', compact('urls', 'allPlayers', 'gameStarted'));
    }

    /**
     * Muestra la vista de configuración del juego (protegido por auth).
     *
     * @return View - Vista de configuración con jugadores y URLs (modules.secret-santa.configuracion).
     */
    public function configuracion(): View
    {
        $players = $this->playerRepo->getActiveModelsOrderedByName();

        $urls = $this->urlRepo->getAllModelsWithFriendPlayer();

        return view('modules.secret-santa.configuracion', compact('players', 'urls'));
    }
}
