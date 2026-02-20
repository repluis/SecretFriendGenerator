<?php

namespace App\Modules\SecretSanta\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SecretSanta\Application\UseCases\Game\GetGameConfig;
use App\Modules\SecretSanta\Application\UseCases\Game\UpdateGameConfig;
use App\Modules\SecretSanta\Application\UseCases\Game\ResetGame;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameConfigApiController extends Controller
{
    public function show(GetGameConfig $useCase): JsonResponse
    {
        $config = $useCase->execute();

        return response()->json([
            'success' => true,
            'message' => 'Game configuration retrieved successfully',
            'data' => $config,
        ]);
    }

    public function update(Request $request, UpdateGameConfig $useCase): JsonResponse
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

        $config = $useCase->execute(['startgame' => $request->startgame]);
        $statusText = $request->startgame == 1 ? 'iniciado' : 'detenido';

        return response()->json([
            'success' => true,
            'message' => "El juego ha sido {$statusText}",
            'data' => $config,
        ]);
    }

    public function resetGame(ResetGame $useCase): JsonResponse
    {
        try {
            $result = $useCase->execute();

            return response()->json([
                'success' => true,
                'message' => 'Juego reiniciado exitosamente',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reiniciar el juego: ' . $e->getMessage(),
            ], 500);
        }
    }
}
