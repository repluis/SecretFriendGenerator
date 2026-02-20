<?php

namespace App\Modules\SecretSanta\Domain\Repositories;

use App\Modules\SecretSanta\Domain\Entities\Player;
use Illuminate\Support\Collection;

interface PlayerRepositoryInterface
{
    public function findAll(): Collection;

    public function findAllActive(): Collection;

    public function findById(int $id): ?Player;

    public function findByName(string $nombre): ?Player;

    public function findActiveByName(string $nombre): ?Player;

    public function create(string $nombre, bool $estado = true): Player;

    public function createMany(array $nombres): Collection;

    public function deleteByName(string $nombre): bool;

    public function deleteAll(): int;

    public function count(): int;

    public function countActive(): int;

    public function getActiveModelsOrderedByName(): Collection;
}
