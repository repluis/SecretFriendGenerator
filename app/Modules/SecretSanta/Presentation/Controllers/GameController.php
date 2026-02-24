<?php

namespace App\Modules\SecretSanta\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepo,
        private UrlRepositoryInterface $urlRepo,
        private GameConfigurationRepositoryInterface $gameConfigRepo,
    ) {}

    /**
     * Muestra la vista del juego de amigo secreto (pública, sin auth).
     * Incluye las URLs generadas, jugadores activos y estado del juego.
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
     * Permite gestionar jugadores y asignaciones de URLs.
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
