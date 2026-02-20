<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence;

use App\Modules\SecretSanta\Domain\Entities\SecretFriendUrl;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Infrastructure\Persistence\Models\UrlModel;
use Illuminate\Support\Collection;

class EloquentUrlRepository implements UrlRepositoryInterface
{
    public function findAll(): Collection
    {
        return UrlModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn(UrlModel $model) => $this->toEntity($model));
    }

    public function findAllWithRelations(): Collection
    {
        return UrlModel::with(['player', 'friendPlayer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?SecretFriendUrl
    {
        $model = UrlModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUrl(string $url): ?SecretFriendUrl
    {
        $model = UrlModel::where('url', $url)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByPlayerId(int $playerId): ?SecretFriendUrl
    {
        $model = UrlModel::where('player_id', $playerId)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByFriendsExcluding(int $playerId, int $excludeUrlId): ?SecretFriendUrl
    {
        $model = UrlModel::where('friends', $playerId)
            ->where('id', '!=', $excludeUrlId)
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function create(string $url, ?int $playerId = null, ?int $friends = null, bool $viewed = false): SecretFriendUrl
    {
        $model = UrlModel::create([
            'url' => $url,
            'player_id' => $playerId,
            'friends' => $friends,
            'viewed' => $viewed,
        ]);

        return $this->toEntity($model);
    }

    public function updateFriends(int $urlId, ?int $friends): bool
    {
        return UrlModel::where('id', $urlId)->update(['friends' => $friends]) > 0;
    }

    public function updateViewed(int $urlId, bool $viewed): bool
    {
        return UrlModel::where('id', $urlId)->update(['viewed' => $viewed]) > 0;
    }

    public function updateUrl(int $urlId, string $url): bool
    {
        return UrlModel::where('id', $urlId)->update(['url' => $url]) > 0;
    }

    public function deleteByPlayerId(int $playerId): bool
    {
        return UrlModel::where('player_id', $playerId)->delete() > 0;
    }

    public function deleteAll(): int
    {
        $count = UrlModel::count();
        UrlModel::truncate();
        return $count;
    }

    public function truncate(): void
    {
        UrlModel::truncate();
    }

    public function count(): int
    {
        return UrlModel::count();
    }

    public function urlExists(string $url): bool
    {
        return UrlModel::where('url', $url)->exists();
    }

    public function resetAllViewed(): int
    {
        return UrlModel::where('viewed', true)->update(['viewed' => false]);
    }

    public function getPlayerIdsWithFriends(): array
    {
        return UrlModel::whereNotNull('friends')->pluck('friends')->toArray();
    }

    /**
     * Obtiene el modelo Eloquent con relaciones para uso en controllers.
     */
    public function getModelByUrl(string $url): ?UrlModel
    {
        return UrlModel::with('player')->where('url', $url)->first();
    }

    /**
     * Obtiene el modelo Eloquent por ID.
     */
    public function getModelById(int $id): ?UrlModel
    {
        return UrlModel::find($id);
    }

    /**
     * Obtiene todos los modelos con relaciones.
     */
    public function getAllModelsWithRelations(): Collection
    {
        return UrlModel::with(['player', 'friendPlayer'])->get();
    }

    /**
     * Obtiene modelos con player relation.
     */
    public function getAllModelsWithPlayer(): Collection
    {
        return UrlModel::with('player')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene modelos con friendPlayer relation.
     */
    public function getAllModelsWithFriendPlayer(): Collection
    {
        return UrlModel::with('friendPlayer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function toEntity(UrlModel $model): SecretFriendUrl
    {
        return SecretFriendUrl::fromArray($model->toArray());
    }
}
