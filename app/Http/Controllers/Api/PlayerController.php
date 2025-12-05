<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * Get all players.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $players = Player::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Players retrieved successfully',
            'data' => $players,
        ], 200);
    }

    /**
     * Get all players with their friends.
     *
     * @return JsonResponse
     */
    public function indexWithFriends(): JsonResponse
    {
        $players = Player::where('estado', true)
            ->with('urlRecord')
            ->orderBy('created_at', 'desc')
            ->get();

        $playersWithFriends = $players->map(function ($player) {
            $friendData = null;
            $urlData = null;
            
            // Obtener información de la URL desde la tabla urls
            if ($player->urlRecord) {
                $urlData = [
                    'url' => $player->urlRecord->url,
                    'full_url' => url('/secret-friend/' . $player->urlRecord->url),
                ];
                
                // Obtener información del amigo desde la tabla urls
                if ($player->urlRecord->friend) {
                    $friend = Player::where('nombre', $player->urlRecord->friend)
                        ->where('estado', true)
                        ->first();
                    
                    if ($friend) {
                        $friendData = [
                            'id' => $friend->id,
                            'nombre' => $friend->nombre,
                        ];
                        
                        // Si el amigo tiene URL, incluirla
                        if ($friend->urlRecord) {
                            $friendData['url'] = $friend->urlRecord->url;
                            $friendData['full_url'] = url('/secret-friend/' . $friend->urlRecord->url);
                        }
                    } else {
                        // Si el amigo no existe o está inactivo, solo mostrar el nombre
                        $friendData = [
                            'nombre' => $player->urlRecord->friend,
                            'not_found' => true,
                        ];
                    }
                }
            }

            return [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $urlData,
                'friend' => $friendData,
                'estado' => $player->estado,
                'created_at' => $player->created_at,
                'updated_at' => $player->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Players with friends retrieved successfully',
            'data' => $playersWithFriends,
        ], 200);
    }

    /**
     * Get a single player by ID.
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function show(string|int $id): JsonResponse
    {
        $player = Player::find((int) $id);

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Player retrieved successfully',
            'data' => $player,
        ], 200);
    }

    /**
     * Create a new player.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $player = Player::create([
            'nombre' => $request->nombre,
            'estado' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player created successfully',
            'data' => $player,
        ], 201);
    }

    /**
     * Add URL to a player (crea o actualiza en la tabla urls).
     *
     * @param Request $request
     * @param string|int $id
     * @return JsonResponse
     */
    public function updateUrl(Request $request, string|int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $player = Player::find((int) $id);

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found',
            ], 404);
        }

        // Buscar o crear el registro en la tabla urls
        $urlRecord = Url::where('player_id', $player->id)->first();
        
        if ($urlRecord) {
            $urlRecord->update([
                'url' => $request->url,
            ]);
        } else {
            $urlRecord = Url::create([
                'url' => $request->url,
                'player_id' => $player->id,
                'friend' => null,
                'viewed' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'URL updated successfully',
            'data' => [
                'player' => $player,
                'url' => $urlRecord,
            ],
        ], 200);
    }

    /**
     * Add friend name to a player (actualiza en la tabla urls).
     *
     * @param Request $request
     * @param string|int $id
     * @return JsonResponse
     */
    public function updateFriend(Request $request, string|int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'friend' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $player = Player::find((int) $id);

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found',
            ], 404);
        }

        // Buscar o crear el registro en la tabla urls
        $urlRecord = Url::where('player_id', $player->id)->first();
        
        if ($urlRecord) {
            $urlRecord->update([
                'friend' => $request->friend,
            ]);
        } else {
            // Si no existe URL, crear una nueva
            $uniqueUrl = bin2hex(random_bytes(16));
            while (Url::where('url', $uniqueUrl)->exists()) {
                $uniqueUrl = bin2hex(random_bytes(16));
            }
            
            $urlRecord = Url::create([
                'url' => $uniqueUrl,
                'player_id' => $player->id,
                'friend' => $request->friend,
                'viewed' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Friend updated successfully',
            'data' => [
                'player' => $player,
                'url' => $urlRecord,
            ],
        ], 200);
    }

    /**
     * Create multiple players at once.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeMany(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|array|min:1',
            'nombres.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $players = [];
        foreach ($request->nombres as $nombre) {
            $players[] = Player::create([
                'nombre' => $nombre,
                'estado' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($players) . ' players created successfully',
            'data' => $players,
        ], 201);
    }

    /**
     * Delete all players.
     *
     * @return JsonResponse
     */
    public function destroyAll(): JsonResponse
    {
        $count = Player::count();
        Player::truncate();

        return response()->json([
            'success' => true,
            'message' => $count . ' players deleted successfully',
        ], 200);
    }

    /**
     * Delete a player by name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyByName(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $player = Player::where('nombre', $request->nombre)->first();

        if (!$player) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found',
            ], 404);
        }

        $player->delete();

        return response()->json([
            'success' => true,
            'message' => 'Player deleted successfully',
        ], 200);
    }

    /**
     * Assign random friends to all players.
     * Each player gets a different friend, no duplicates.
     *
     * @return JsonResponse
     */
    public function assignRandomFriends(): JsonResponse
    {
        $players = Player::where('estado', true)->get();

        if ($players->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Se necesitan al menos 2 jugadores para asignar amigos',
            ], 400);
        }

        // Convertir a array para trabajar más fácilmente
        $playersArray = $players->toArray();
        $playerIds = array_column($playersArray, 'id');
        $friendIds = $playerIds;
        
        // Mezclar los amigos aleatoriamente
        shuffle($friendIds);

        // Asegurar que ningún jugador tenga a sí mismo como amigo
        // Si hay auto-asignación, intercambiar con otro jugador
        $maxAttempts = 100;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $hasSelfAssignment = false;
            
            // Verificar si algún jugador tiene a sí mismo como amigo
            for ($i = 0; $i < count($playerIds); $i++) {
                if ($playerIds[$i] === $friendIds[$i]) {
                    $hasSelfAssignment = true;
                    // Intercambiar con otro jugador aleatorio
                    $swapIndex = ($i + 1) % count($friendIds);
                    if ($swapIndex === $i) {
                        $swapIndex = ($i + 1) % count($friendIds);
                    }
                    $temp = $friendIds[$i];
                    $friendIds[$i] = $friendIds[$swapIndex];
                    $friendIds[$swapIndex] = $temp;
                    break;
                }
            }
            
            if (!$hasSelfAssignment) {
                break;
            }
            
            $attempt++;
        }

        // Si después de varios intentos aún hay auto-asignación, usar algoritmo circular
        if ($attempt >= $maxAttempts) {
            // Algoritmo circular: cada jugador tiene al siguiente
            $friendIds = [];
            for ($i = 0; $i < count($playerIds); $i++) {
                $friendIds[] = $playerIds[($i + 1) % count($playerIds)];
            }
        }

        // Crear un mapa de ID -> jugador
        $playerMap = $players->keyBy('id');
        $assigned = [];

        // Asignar amigos en la tabla urls
        foreach ($playerIds as $index => $playerId) {
            $friendId = $friendIds[$index];
            $friend = $playerMap[$friendId];
            
            $player = $players->find($playerId);
            
            // Actualizar o crear el registro en la tabla urls
            $urlRecord = Url::where('player_id', $playerId)->first();
            
            if ($urlRecord) {
                // Si existe la URL, actualizar el campo friend
                $urlRecord->update([
                    'friend' => $friend->nombre,
                ]);
            } else {
                // Si no existe, crear una URL nueva
                $uniqueUrl = bin2hex(random_bytes(16));
                while (Url::where('url', $uniqueUrl)->exists()) {
                    $uniqueUrl = bin2hex(random_bytes(16));
                }
                
                $urlRecord = Url::create([
                    'url' => $uniqueUrl,
                    'player_id' => $playerId,
                    'friend' => $friend->nombre,
                    'viewed' => false,
                ]);
            }
            
            $assigned[] = [
                'player' => $player->nombre,
                'friend' => $friend->nombre,
                'url' => $urlRecord->url,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Amigos asignados aleatoriamente',
            'data' => $assigned,
        ], 200);
    }

    /**
     * Generar URLs únicas aleatorias para todos los jugadores (una por cada jugador).
     * Crea registros en la tabla urls.
     *
     * @return JsonResponse
     */
    public function generateUrls(): JsonResponse
    {
        $players = Player::all();

        if ($players->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay jugadores registrados',
                'data' => [],
            ], 404);
        }

        $generated = [];
        foreach ($players as $player) {
            // Verificar si ya existe una URL para este jugador
            $existingUrl = Url::where('player_id', $player->id)->first();
            
            if ($existingUrl) {
                // Si ya existe, usar la existente
                $generated[] = [
                    'id' => $player->id,
                    'nombre' => $player->nombre,
                    'url' => $existingUrl->url,
                    'full_url' => url('/secret-friend/' . $existingUrl->url),
                    'friend' => $existingUrl->friend,
                ];
                continue;
            }

            // Generar una URL única aleatoria
            $uniqueUrl = bin2hex(random_bytes(16));
            
            // Verificar que la URL no exista en la tabla urls
            while (Url::where('url', $uniqueUrl)->exists()) {
                $uniqueUrl = bin2hex(random_bytes(16));
            }

            // Crear el registro en la tabla urls
            $urlRecord = Url::create([
                'url' => $uniqueUrl,
                'player_id' => $player->id,
                'friend' => null, // Se asignará después con assign-friends
                'viewed' => false,
            ]);
            
            $generated[] = [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $uniqueUrl,
                'full_url' => url('/secret-friend/' . $uniqueUrl),
                'friend' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($generated) . ' URLs generadas exitosamente',
            'data' => $generated,
        ], 200);
    }

    /**
     * Asignar URLs únicas aleatorias a TODOS los jugadores (regenera si ya tienen).
     * Usa la tabla urls.
     *
     * @return JsonResponse
     */
    public function assignUrlsToAll(): JsonResponse
    {
        $players = Player::all();

        if ($players->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay jugadores registrados',
                'data' => [],
            ], 404);
        }

        $assigned = [];
        foreach ($players as $player) {
            // Eliminar URL existente si existe
            Url::where('player_id', $player->id)->delete();
            
            // Generar una URL única aleatoria
            $uniqueUrl = bin2hex(random_bytes(16));
            
            // Verificar que la URL no exista
            while (Url::where('url', $uniqueUrl)->exists()) {
                $uniqueUrl = bin2hex(random_bytes(16));
            }

            // Crear el registro en la tabla urls
            $urlRecord = Url::create([
                'url' => $uniqueUrl,
                'player_id' => $player->id,
                'friend' => null, // Se asignará después con assign-friends
                'viewed' => false,
            ]);
            
            $assigned[] = [
                'id' => $player->id,
                'nombre' => $player->nombre,
                'url' => $uniqueUrl,
                'full_url' => url('/secret-friend/' . $uniqueUrl),
                'friend' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($assigned) . ' URLs asignadas exitosamente a todos los jugadores',
            'data' => $assigned,
        ], 200);
    }

    /**
     * Obtener solo las URLs de todos los jugadores desde la tabla urls.
     *
     * @return JsonResponse
     */
    public function getAllUrls(): JsonResponse
    {
        $urls = Url::with('player')
            ->orderBy('created_at', 'desc')
            ->get();

        $urlsData = $urls->map(function ($url) {
            return [
                'id' => $url->id,
                'player_id' => $url->player_id,
                'nombre' => $url->player->nombre ?? 'N/A',
                'url' => $url->url,
                'full_url' => url('/secret-friend/' . $url->url),
                'viewed' => $url->viewed,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'URLs obtenidas exitosamente',
            'data' => $urlsData,
            'total' => $urlsData->count(),
        ], 200);
    }

    /**
     * Sincronizar URLs con jugadores y asignar nombres aleatorios.
     * Asegura que haya el mismo número de URLs que jugadores activos.
     * Si hay más jugadores que URLs, crea las URLs faltantes.
     * Si hay más URLs que jugadores, elimina las URLs sobrantes.
     * Asigna un nombre aleatorio a cada URL sin repeticiones.
     *
     * @return JsonResponse
     */
    public function syncUrlsAndAssignNames(): JsonResponse
    {
        // Obtener todos los nombres de jugadores activos
        $players = Player::where('estado', true)->get();
        $playerNames = $players->pluck('nombre')->toArray();

        if (count($playerNames) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Se necesitan al menos 2 jugadores activos para asignar nombres',
            ], 400);
        }

        // Obtener todas las URLs existentes
        $existingUrls = Url::all();
        $urlsCount = $existingUrls->count();
        $playersCount = count($playerNames);

        // Sincronizar: ajustar el número de URLs según el número de jugadores
        if ($urlsCount < $playersCount) {
            // Crear URLs faltantes
            $urlsToCreate = $playersCount - $urlsCount;
            
            // Obtener IDs de jugadores que ya tienen URL
            $playersWithUrl = Url::whereNotNull('player_id')->pluck('player_id')->toArray();
            
            // Obtener jugadores que no tienen URL usando Query Builder
            $playersWithoutUrl = Player::where('estado', true)
                ->whereNotIn('id', $playersWithUrl)
                ->take($urlsToCreate)
                ->get();
            
            foreach ($playersWithoutUrl as $player) {
                $uniqueUrl = bin2hex(random_bytes(16));
                while (Url::where('url', $uniqueUrl)->exists()) {
                    $uniqueUrl = bin2hex(random_bytes(16));
                }
                
                Url::create([
                    'url' => $uniqueUrl,
                    'player_id' => $player->id,
                    'viewed' => false,
                ]);
            }
            
            // Si aún faltan URLs, crear sin asignar a jugador específico
            $remaining = $playersCount - Url::count();
            for ($i = 0; $i < $remaining; $i++) {
                $uniqueUrl = bin2hex(random_bytes(16));
                while (Url::where('url', $uniqueUrl)->exists()) {
                    $uniqueUrl = bin2hex(random_bytes(16));
                }
                
                Url::create([
                    'url' => $uniqueUrl,
                    'player_id' => null,
                    'viewed' => false,
                ]);
            }
        } elseif ($urlsCount > $playersCount) {
            // Eliminar URLs sobrantes (mantener solo las necesarias)
            $urlsToDelete = $urlsCount - $playersCount;
            $urlsToRemove = Url::orderBy('created_at', 'desc')
                ->take($urlsToDelete)
                ->get();
            
            foreach ($urlsToRemove as $url) {
                $url->delete();
            }
        }

        // Obtener todas las URLs actualizadas
        $allUrls = Url::all();
        
        // Preparar respuesta con las URLs creadas/actualizadas
        $assigned = [];
        
        foreach ($allUrls as $url) {
            $assigned[] = [
                'url_id' => $url->id,
                'url' => $url->url,
                'player_id' => $url->player_id,
                'player_name' => $url->player ? $url->player->nombre : 'N/A',
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'URLs sincronizadas exitosamente',
            'data' => [
                'total_players' => $playersCount,
                'total_urls' => $allUrls->count(),
                'assigned' => $assigned,
            ],
        ], 200);
    }

    /**
     * Eliminar todos los registros de la tabla urls.
     *
     * @return JsonResponse
     */
    public function destroyAllUrls(): JsonResponse
    {
        $count = Url::count();
        Url::truncate();

        return response()->json([
            'success' => true,
            'message' => $count . ' URLs eliminadas exitosamente',
        ], 200);
    }

    /**
     * Actualizar el player_id de una URL.
     *
     * @param Request $request
     * @param string|int $urlId
     * @return JsonResponse
     */
    public function updateUrlPlayer(Request $request, string|int $urlId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'nullable|integer|exists:players,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = Url::find((int) $urlId);

        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found',
            ], 404);
        }

        $playerId = $request->player_id ? (int) $request->player_id : null;

        // Si había un jugador asignado previamente en friends, limpiar su selección
        if ($url->friends && $url->friends != $playerId) {
            // El jugador anterior ya no está seleccionado en esta URL
        }

        // Si se está asignando un jugador al campo friends
        if ($playerId) {
            // Verificar que el jugador no haya seleccionado ya otra URL
            $existingUrl = Url::where('friends', $playerId)
                ->where('id', '!=', $url->id)
                ->first();
            
            if ($existingUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este jugador ya ha seleccionado otra URL',
                ], 400);
            }
            
            // Asignar el id del jugador al campo friends de la URL
            // Esto indica que este jugador seleccionó esta URL (es el dueño)
            $url->update(['friends' => $playerId]);
        } else {
            // Si se desasigna, limpiar el campo friends
            $url->update(['friends' => null]);
        }

        // Obtener todas las URLs actualizadas con sus jugadores
        $allUrls = Url::with(['player', 'friendPlayer'])->get();
        
        // Obtener todos los jugadores activos
        $allPlayers = Player::where('estado', true)->orderBy('nombre')->get();
        
        // Para cada URL, mostrar solo los jugadores disponibles
        $urlsData = $allUrls->map(function($urlItem) use ($allPlayers) {
            // Obtener IDs de jugadores que ya seleccionaron una URL (en otra URL diferente)
            $selectedPlayerIds = Url::whereNotNull('friends')
                ->where('id', '!=', $urlItem->id)
                ->pluck('friends')
                ->toArray();
            
            // Jugadores disponibles: los que no han seleccionado otra URL
            // O el jugador que ya está asignado a esta URL (friends)
            $availablePlayers = $allPlayers->filter(function($player) use ($urlItem, $selectedPlayerIds) {
                return !in_array($player->id, $selectedPlayerIds) || $urlItem->friends == $player->id;
            })->map(function($player) {
                return [
                    'id' => $player->id,
                    'nombre' => $player->nombre,
                ];
            })->values();

            return [
                'id' => $urlItem->id,
                'url' => $urlItem->url,
                'player_id' => $urlItem->player_id,
                'player_name' => $urlItem->player ? $urlItem->player->nombre : null,
                'friends' => $urlItem->friends,
                'friends_name' => $urlItem->friendPlayer ? $urlItem->friendPlayer->nombre : null,
                'available_players' => $availablePlayers,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'URL player updated successfully',
            'data' => [
                'urls' => $urlsData,
            ],
        ], 200);
    }

    /**
     * Validar que ningún jugador tenga a sí mismo como amigo secreto.
     * Verifica que player_id != friends en la tabla urls.
     *
     * @return JsonResponse
     */
    public function validateAssignments(): JsonResponse
    {
        // Obtener todas las URLs con sus relaciones
        $urls = Url::with(['player', 'friendPlayer'])->get();
        
        $invalidUrls = [];
        $validUrls = [];
        
        foreach ($urls as $url) {
            // Validar que player_id sea diferente de friends
            // Si ambos están definidos y son iguales, es inválido
            if ($url->player_id !== null && $url->friends !== null && $url->player_id == $url->friends) {
                $invalidUrls[] = [
                    'id' => $url->id,
                    'url' => $url->url,
                    'player_id' => $url->player_id,
                    'player_name' => $url->player ? $url->player->nombre : 'N/A',
                    'friends' => $url->friends,
                    'friends_name' => $url->friendPlayer ? $url->friendPlayer->nombre : 'N/A',
                ];
            } else {
                $validUrls[] = [
                    'id' => $url->id,
                    'url' => $url->url,
                    'player_id' => $url->player_id,
                    'player_name' => $url->player ? $url->player->nombre : 'N/A',
                    'friends' => $url->friends,
                    'friends_name' => $url->friendPlayer ? $url->friendPlayer->nombre : 'N/A',
                ];
            }
        }
        
        $isValid = count($invalidUrls) === 0;
        
        return response()->json([
            'success' => true,
            'message' => $isValid 
                ? 'Todas las asignaciones son válidas' 
                : 'Se encontraron asignaciones inválidas',
            'data' => [
                'is_valid' => $isValid,
                'invalid_count' => count($invalidUrls),
                'valid_count' => count($validUrls),
                'invalid_urls' => $invalidUrls,
                'valid_urls' => $validUrls,
            ],
        ], 200);
    }
}
