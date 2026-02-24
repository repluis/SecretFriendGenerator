<?php

namespace App\Modules\User\Domain\Repositories;

use App\Modules\User\Domain\Entities\UserEntity;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findAll(): Collection;

    public function findAllActive(): Collection;

    public function findById(int $id): ?UserEntity;

    public function create(array $data): UserEntity;

    public function update(int $id, array $data): UserEntity;

    public function toggleActive(int $id): UserEntity;

    public function count(): int;

    public function getAllIds(): array;

    public function existsByIdentification(string $identification, ?int $excludeUserId = null): bool;

    public function updatePassword(int $id, string $password): UserEntity;

    public function verifyPassword(int $id, string $plainPassword): bool;
}
