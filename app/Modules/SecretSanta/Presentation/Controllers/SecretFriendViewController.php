<?php

namespace App\Modules\SecretSanta\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SecretSanta\Application\UseCases\View\ShowSecretFriend;

class SecretFriendViewController extends Controller
{
    public function show(string $url, ShowSecretFriend $useCase)
    {
        $result = $useCase->execute(['url' => $url]);

        if (isset($result['error'])) {
            return match ($result['error']) {
                'not_found' => abort(404, 'URL no encontrada'),
                'game_not_started' => view('modules.secret-santa.game-not-started'),
                'already_viewed' => view('modules.secret-santa.already-viewed'),
                'no_friend_assigned' => view('modules.secret-santa.no-friend-assigned'),
                default => abort(500),
            };
        }

        return view('modules.secret-santa.secret-friend', [
            'friendName' => $result['friendName'],
        ]);
    }
}
