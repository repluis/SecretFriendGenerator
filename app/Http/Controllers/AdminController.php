<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Url;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin page with all players and URLs.
     *
     * @return View
     */
    public function index(): View
    {
        $players = Player::orderBy('created_at', 'desc')->get();
        $urls = Url::with(['player', 'friendPlayer'])->orderBy('created_at', 'desc')->get();

        return view('admin', compact('players', 'urls'));
    }
}
