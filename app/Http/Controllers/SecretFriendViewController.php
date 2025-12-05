<?php

namespace App\Http\Controllers;

use App\Models\GameConfiguration;
use App\Models\Url;
use Illuminate\Http\Request;

class SecretFriendViewController extends Controller
{
    /**
     * Mostrar la vista del amigo secreto para un jugador
     *
     * @param string $url
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show(string $url)
    {
        // Buscar la URL en la tabla urls con la relación del jugador
        $urlRecord = Url::with('player')->where('url', $url)->first();

        if (!$urlRecord) {
            abort(404, 'URL no encontrada');
        }

        // Verificar si el juego está habilitado
        $gameConfig = GameConfiguration::getCurrent();
        
        if ($gameConfig->startgame == 0) {
            return view('game-not-started');
        }

        // Verificar si ya se vio esta URL
        if ($urlRecord->viewed) {
            return view('already-viewed');
        }

        // Verificar si hay un jugador asignado (amigo asignado)
        // El amigo asignado es el jugador relacionado a través de player_id
        if (!$urlRecord->player_id || !$urlRecord->player) {
            return view('no-friend-assigned');
        }

        // Marcar como visto
        $urlRecord->update(['viewed' => true]);

        // Obtener el nombre del amigo desde la relación player
        $friendName = $urlRecord->player->nombre;

        return view('secret-friend', [
            'friendName' => $friendName,
        ]);
    }
}
