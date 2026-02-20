<?php

namespace App\Modules\SecretSanta\Domain\Services;

class SecretSantaAssigner
{
    /**
     * Genera una asignacion aleatoria de amigos secretos sin auto-asignaciones.
     *
     * @param array $playerIds Lista de IDs de jugadores
     * @return array Mapa de playerIds[i] => friendIds[i]
     */
    public function assignFriends(array $playerIds): array
    {
        $friendIds = $playerIds;
        shuffle($friendIds);

        $maxAttempts = 100;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $hasSelfAssignment = false;

            for ($i = 0; $i < count($playerIds); $i++) {
                if ($playerIds[$i] === $friendIds[$i]) {
                    $hasSelfAssignment = true;
                    $swapIndex = ($i + 1) % count($friendIds);
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

        // Fallback: algoritmo circular
        if ($attempt >= $maxAttempts) {
            $friendIds = [];
            for ($i = 0; $i < count($playerIds); $i++) {
                $friendIds[] = $playerIds[($i + 1) % count($playerIds)];
            }
        }

        $assignments = [];
        foreach ($playerIds as $index => $playerId) {
            $assignments[$playerId] = $friendIds[$index];
        }

        return $assignments;
    }

    /**
     * Genera una URL unica aleatoria.
     */
    public function generateUniqueUrlHash(): string
    {
        return bin2hex(random_bytes(16));
    }
}
