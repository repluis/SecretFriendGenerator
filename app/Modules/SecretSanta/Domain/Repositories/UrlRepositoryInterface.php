<?php

namespace App\Modules\SecretSanta\Domain\Repositories;

use App\Modules\SecretSanta\Domain\Entities\SecretFriendUrl;
use Illuminate\Support\Collection;

interface UrlRepositoryInterface
{
    public function findAll(): Collection;

    public function findAllWithRelations(): Collection;

    public function findById(int $id): ?SecretFriendUrl;

    public function findByUrl(string $url): ?SecretFriendUrl;

    public function findByPlayerId(int $playerId): ?SecretFriendUrl;

    public function findByFriendsExcluding(int $playerId, int $excludeUrlId): ?SecretFriendUrl;

    public function create(string $url, ?int $playerId = null, ?int $friends = null, bool $viewed = false): SecretFriendUrl;

    public function updateFriends(int $urlId, ?int $friends): bool;

    public function updateViewed(int $urlId, bool $viewed): bool;

    public function updateUrl(int $urlId, string $url): bool;

    public function deleteByPlayerId(int $playerId): bool;

    public function deleteAll(): int;

    public function truncate(): void;

    public function count(): int;

    public function urlExists(string $url): bool;

    public function resetAllViewed(): int;

    public function getPlayerIdsWithFriends(): array;

    public function getAllModelsWithFriendPlayer(): Collection;
}
