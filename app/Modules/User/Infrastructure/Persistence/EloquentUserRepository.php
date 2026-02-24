<?php

namespace App\Modules\User\Infrastructure\Persistence;

use App\Models\User;
use App\Modules\User\Domain\Entities\UserEntity;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findAll(): Collection
    {
        return User::orderBy('name')
            ->get(['id', 'name', 'email', 'active', 'identification', 'created_at'])
            ->map(fn(User $model) => $this->toEntity($model));
    }

    public function findAllActive(): Collection
    {
        return User::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'active', 'identification', 'created_at'])
            ->map(fn(User $model) => $this->toEntity($model));
    }

    public function findById(int $id): ?UserEntity
    {
        $model = User::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function create(array $data): UserEntity
    {
        $model = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? Str::slug($data['name']) . '@secretfriend.local',
            'password' => bcrypt(Str::random(16)),
            'active' => true,
        ]);

        return $this->toEntity($model);
    }

    public function update(int $id, array $data): UserEntity
    {
        $model = User::findOrFail($id);

        if (isset($data['name'])) {
            $model->name = $data['name'];
        }

        if (isset($data['email'])) {
            $model->email = $data['email'];
        }

        if (array_key_exists('identification', $data)) {
            $model->identification = $data['identification'];
        }

        $model->save();

        return $this->toEntity($model);
    }

    public function toggleActive(int $id): UserEntity
    {
        $model = User::findOrFail($id);
        $model->active = !$model->active;
        $model->save();

        return $this->toEntity($model);
    }

    public function count(): int
    {
        return User::count();
    }

    public function getAllIds(): array
    {
        return User::pluck('id')->toArray();
    }

    public function existsByIdentification(string $identification, ?int $excludeUserId = null): bool
    {
        $query = User::where('identification', $identification);

        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    public function updatePassword(int $id, string $password): UserEntity
    {
        $model = User::findOrFail($id);
        $model->password = $password;
        $model->save();

        return $this->toEntity($model);
    }

    public function verifyPassword(int $id, string $plainPassword): bool
    {
        $model = User::findOrFail($id);

        return Hash::check($plainPassword, $model->password);
    }

    private function toEntity(User $model): UserEntity
    {
        return UserEntity::fromArray($model->toArray());
    }
}
