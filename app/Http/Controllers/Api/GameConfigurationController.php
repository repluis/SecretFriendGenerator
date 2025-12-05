<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameConfiguration;
use App\Models\Player;
use App\Models\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameConfigurationController extends Controller
{
    /**
     * Obtener la configuración actual del juego
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $config = GameConfiguration::getCurrent();

        return response()->json([
            'success' => true,
            'message' => 'Game configuration retrieved successfully',
            'data' => [
                'id' => $config->id,
                'startgame' => $config->startgame,
                'created_at' => $config->created_at,
                'updated_at' => $config->updated_at,
            ],
        ], 200);
    }

    /**
     * Actualizar el estado del juego
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'startgame' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $config = GameConfiguration::getCurrent();
        $config->update([
            'startgame' => $request->startgame,
        ]);

        $statusText = $request->startgame == 1 ? 'iniciado' : 'detenido';

        return response()->json([
            'success' => true,
            'message' => "El juego ha sido {$statusText}",
            'data' => [
                'id' => $config->id,
                'startgame' => $config->startgame,
                'created_at' => $config->created_at,
                'updated_at' => $config->updated_at,
            ],
        ], 200);
    }

    /**
     * Reiniciar el juego: borra todos los datos de players y urls, y pone el estado en 0
     *
     * @return JsonResponse
     */
    public function resetGame(): JsonResponse
    {
        try {
            // Contar registros antes de borrar
            $playersCount = Player::count();
            $urlsCount = Url::count();
            
            // Borrar todos los datos de las tablas
            Player::truncate();
            Url::truncate();
            
            // Poner el estado del juego en 0
            $config = GameConfiguration::getCurrent();
            $config->update([
                'startgame' => 0,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Juego reiniciado exitosamente',
                'data' => [
                    'players_deleted' => $playersCount,
                    'urls_deleted' => $urlsCount,
                    'game_status' => 0,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reiniciar el juego: ' . $e->getMessage(),
            ], 500);
        }
    }
}
