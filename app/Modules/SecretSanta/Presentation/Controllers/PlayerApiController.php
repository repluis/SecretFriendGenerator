<?php

namespace App\Modules\SecretSanta\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SecretSanta\Application\UseCases\Player\GetAllPlayers;
use App\Modules\SecretSanta\Application\UseCases\Player\GetPlayerById;
use App\Modules\SecretSanta\Application\UseCases\Player\GetPlayersWithFriends;
use App\Modules\SecretSanta\Application\UseCases\Player\CreatePlayer;
use App\Modules\SecretSanta\Application\UseCases\Player\CreateManyPlayers;
use App\Modules\SecretSanta\Application\UseCases\Player\DeleteAllPlayers;
use App\Modules\SecretSanta\Application\UseCases\Player\DeletePlayerByName;
use App\Modules\SecretSanta\Application\UseCases\Url\GenerateUrls;
use App\Modules\SecretSanta\Application\UseCases\Url\AssignUrlsToAll;
use App\Modules\SecretSanta\Application\UseCases\Url\GetAllUrls;
use App\Modules\SecretSanta\Application\UseCases\Url\DestroyAllUrls;
use App\Modules\SecretSanta\Application\UseCases\Url\UpdateUrlPlayer;
use App\Modules\SecretSanta\Application\UseCases\Url\SyncUrlsAndAssignNames;
use App\Modules\SecretSanta\Application\UseCases\Friend\AssignRandomFriends;
use App\Modules\SecretSanta\Application\UseCases\Friend\UpdateFriend;
use App\Modules\SecretSanta\Application\UseCases\Friend\ValidateAssignments;
use App\Modules\SecretSanta\Application\UseCases\View\EnableViewForPlayer;
use App\Modules\SecretSanta\Application\UseCases\View\EnableViewForAll;
use App\Modules\SecretSanta\Application\UseCases\View\ResetUrlView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerApiController extends Controller
{
    // index - GET /api/players
    public function index(GetAllPlayers $useCase): JsonResponse
    {
        $players = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => 'Players retrieved successfully',
            'data' => $players,
        ]);
    }

    // indexWithFriends - GET /api/players/with-friends
    public function indexWithFriends(GetPlayersWithFriends $useCase): JsonResponse
    {
        $data = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => 'Players with friends retrieved successfully',
            'data' => $data,
        ]);
    }

    // show - GET /api/players/{id}
    public function show(string|int $id, GetPlayerById $useCase): JsonResponse
    {
        $player = $useCase->execute(['id' => (int) $id]);
        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Player not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Player retrieved successfully',
            'data' => $player,
        ]);
    }

    // store - POST /api/players
    public function store(Request $request, CreatePlayer $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $player = $useCase->execute(['nombre' => $request->nombre]);
        return response()->json([
            'success' => true,
            'message' => 'Player created successfully',
            'data' => $player,
        ], 201);
    }

    // storeMany - POST /api/players/many
    public function storeMany(Request $request, CreateManyPlayers $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|array|min:1',
            'nombres.*' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $players = $useCase->execute(['nombres' => $request->nombres]);
        return response()->json([
            'success' => true,
            'message' => count($players) . ' players created successfully',
            'data' => $players,
        ], 201);
    }

    // destroyAll - DELETE /api/players/all
    public function destroyAll(DeleteAllPlayers $useCase): JsonResponse
    {
        $count = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => $count . ' players deleted successfully',
        ]);
    }

    // destroyByName - DELETE /api/players/by-name
    public function destroyByName(Request $request, DeletePlayerByName $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $result = $useCase->execute(['nombre' => $request->nombre]);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Player not found'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Player deleted successfully']);
    }

    // updateFriend - PUT /api/players/{id}/friend
    public function updateFriend(Request $request, string|int $id, UpdateFriend $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'friend' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        // The original code stored friend name as string. We now need to find the friend player by name and use their ID.
        // But to maintain API compatibility, accept the friend name and look up the player
        $result = $useCase->execute(['playerId' => (int) $id, 'friendName' => $request->friend]);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'Player not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Friend updated successfully',
            'data' => $result,
        ]);
    }

    // assignRandomFriends - POST /api/players/assign-friends
    public function assignRandomFriends(AssignRandomFriends $useCase): JsonResponse
    {
        $result = $useCase->execute();
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'Amigos asignados aleatoriamente',
            'data' => $result,
        ]);
    }

    // generateUrls - POST /api/players/generate-urls
    public function generateUrls(GenerateUrls $useCase): JsonResponse
    {
        $result = $useCase->execute();
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error'], 'data' => []], 404);
        }
        return response()->json([
            'success' => true,
            'message' => count($result) . ' URLs generadas exitosamente',
            'data' => $result,
        ]);
    }

    // assignUrlsToAll - POST /api/players/assign-urls-to-all
    public function assignUrlsToAll(AssignUrlsToAll $useCase): JsonResponse
    {
        $result = $useCase->execute();
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error'], 'data' => []], 404);
        }
        return response()->json([
            'success' => true,
            'message' => count($result) . ' URLs asignadas exitosamente a todos los jugadores',
            'data' => $result,
        ]);
    }

    // getAllUrls - GET /api/players/urls
    public function getAllUrls(GetAllUrls $useCase): JsonResponse
    {
        $result = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => 'URLs obtenidas exitosamente',
            'data' => $result,
            'total' => count($result),
        ]);
    }

    // destroyAllUrls - DELETE /api/players/urls/all
    public function destroyAllUrls(DestroyAllUrls $useCase): JsonResponse
    {
        $count = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => $count . ' URLs eliminadas exitosamente',
        ]);
    }

    // updateUrlPlayer - PUT /api/players/urls/{urlId}/player
    public function updateUrlPlayer(Request $request, string|int $urlId, UpdateUrlPlayer $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'nullable|integer|exists:players,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $playerId = $request->player_id ? (int) $request->player_id : null;
        $result = $useCase->execute(['urlId' => (int) $urlId, 'player_id' => $playerId]);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'URL not found'], 404);
        }
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'URL player updated successfully',
            'data' => $result,
        ]);
    }

    // syncUrlsAndAssignNames - POST /api/players/sync-urls-and-assign-names
    public function syncUrlsAndAssignNames(SyncUrlsAndAssignNames $useCase): JsonResponse
    {
        $result = $useCase->execute();
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'URLs sincronizadas exitosamente',
            'data' => $result,
        ]);
    }

    // validateAssignments - GET /api/players/validate-assignments
    public function validateAssignments(ValidateAssignments $useCase): JsonResponse
    {
        $result = $useCase->execute();
        $isValid = $result['is_valid'];
        return response()->json([
            'success' => true,
            'message' => $isValid ? 'Todas las asignaciones son válidas' : 'Se encontraron asignaciones inválidas',
            'data' => $result,
        ]);
    }

    // enableViewForPlayer - POST /api/players/enable-view
    public function enableViewForPlayer(Request $request, EnableViewForPlayer $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $result = $useCase->execute(['nombre' => $request->nombre]);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'No se encontró un jugador activo con ese nombre'], 404);
        }
        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Vista habilitada para el jugador',
            'data' => $result,
        ]);
    }

    // enableViewForAll - POST /api/players/enable-view-all
    public function enableViewForAll(EnableViewForAll $useCase): JsonResponse
    {
        $result = $useCase->execute();
        return response()->json([
            'success' => true,
            'message' => 'Vista habilitada para todos los jugadores',
            'data' => $result,
        ]);
    }

    // resetUrlView - POST /api/players/urls/{urlId}/reset-view
    public function resetUrlView(string|int $urlId, ResetUrlView $useCase): JsonResponse
    {
        $result = $useCase->execute(['urlId' => (int) $urlId]);
        if (!$result) {
            return response()->json(['success' => false, 'message' => 'URL no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Vista restablecida para la URL',
            'data' => $result,
        ]);
    }
}
