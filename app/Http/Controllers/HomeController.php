<?php

namespace App\Http\Controllers;

use App\Models\GameConfiguration;
use App\Models\Player;
use App\Models\Url;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with all URLs.
     *
     * @return View
     */
    public function index(): View
    {
        $urls = Url::with(['player', 'friendPlayer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Obtener todos los jugadores activos
        $allPlayers = Player::where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        // Obtener el estado del juego
        $gameConfig = GameConfiguration::getCurrent();
        $gameStarted = $gameConfig->startgame == 1;

        return view('home', compact('urls', 'allPlayers', 'gameStarted'));
    }
}
