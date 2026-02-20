<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence;

use App\Modules\SecretSanta\Domain\Entities\Player;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Infrastructure\Persistence\Models\PlayerModel;
use Illuminate\Support\Collection;

class EloquentPlayerRepository implements PlayerRepositoryInterface
{
    public function findAll(): Collection
    {
        return PlayerModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn(PlayerModel $model) => $this->toEntity($model));
    }

    public function findAllActive(): Collection
    {
        return PlayerModel::where('estado', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(PlayerModel $model) => $this->toEntity($model));
    }

    public function findById(int $id): ?Player
    {
        $model = PlayerModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $nombre): ?Player
    {
        $model = PlayerModel::where('nombre', $nombre)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findActiveByName(string $nombre): ?Player
    {
        $model = PlayerModel::where('nombre', $nombre)
            ->where('estado', true)
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function create(string $nombre, bool $estado = true): Player
    {
        $model = PlayerModel::create([
            'nombre' => $nombre,
            'estado' => $estado,
        ]);

        return $this->toEntity($model);
    }

    public function createMany(array $nombres): Collection
    {
        $players = collect();
        foreach ($nombres as $nombre) {
            $model = PlayerModel::create([
                'nombre' => $nombre,
                'estado' => true,
            ]);
            $players->push($this->toEntity($model));
        }

        return $players;
    }

    public function deleteByName(string $nombre): bool
    {
        $model = PlayerModel::where('nombre', $nombre)->first();
        if (!$model) {
            return false;
        }
        $model->delete();
        return true;
    }

    public function deleteAll(): int
    {
        $count = PlayerModel::count();
        PlayerModel::truncate();
        return $count;
    }

    public function count(): int
    {
        return PlayerModel::count();
    }

    public function countActive(): int
    {
        return PlayerModel::where('estado', true)->count();
    }

    /**
     * Obtiene el modelo Eloquent con relaciones para uso en controllers.
     */
    public function getModelWithUrlRecord(int $id): ?PlayerModel
    {
        return PlayerModel::with('urlRecord')->find($id);
    }

    /**
     * Obtiene todos los modelos activos con relaciones.
     */
    public function getActiveModelsWithUrlRecord(): Collection
    {
        return PlayerModel::where('estado', true)
            ->with('urlRecord')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene todos los modelos activos ordenados por nombre.
     */
    public function getActiveModelsOrderedByName(): Collection
    {
        return PlayerModel::where('estado', true)
            ->orderBy('nombre')
            ->get();
    }

    private function toEntity(PlayerModel $model): Player
    {
        return Player::fromArray($model->toArray());
    }
}
