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

    public function index(GetChargesByType $getCharges, GetAllUsers $getAllUsers, GetUserBalances $getUserBalances): View
    {
        $totalPlayers = $this->playerRepo->countActive();
        $totalUrls = $this->urlRepo->count();

        $gameConfig = $this->gameConfigRepo->getCurrent();
        $gameStarted = $gameConfig->isStarted();

        $fundraisingData = $getCharges->execute(['type' => 'navidad']);
        $fundraisingUsers = $fundraisingData['summary']['total_users'];

        // Balances from transactions module
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

    public function game(): View
    {
        $urls = $this->urlRepo->findAllWithRelations();

        $allPlayers = $this->playerRepo->getActiveModelsOrderedByName();

        $gameConfig = $this->gameConfigRepo->getCurrent();
        $gameStarted = $gameConfig->isStarted();

        return view('modules.secret-santa.index', compact('urls', 'allPlayers', 'gameStarted'));
    }

    public function configuracion(): View
    {
        $players = $this->playerRepo->getActiveModelsOrderedByName();

        $urls = $this->urlRepo->getAllModelsWithFriendPlayer();

        return view('modules.secret-santa.configuracion', compact('players', 'urls'));
    }
}
