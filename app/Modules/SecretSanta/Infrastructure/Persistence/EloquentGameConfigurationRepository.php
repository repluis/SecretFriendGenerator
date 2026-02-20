<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence;

use App\Modules\SecretSanta\Domain\Entities\GameConfiguration;
use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Infrastructure\Persistence\Models\GameConfigurationModel;

class EloquentGameConfigurationRepository implements GameConfigurationRepositoryInterface
{
    public function getCurrent(): GameConfiguration
    {
        $model = GameConfigurationModel::getCurrent();

        return GameConfiguration::fromArray($model->toArray());
    }

    public function update(int $startgame): GameConfiguration
    {
        $model = GameConfigurationModel::getCurrent();
        $model->update(['startgame' => $startgame]);

        return GameConfiguration::fromArray($model->toArray());
    }
}
