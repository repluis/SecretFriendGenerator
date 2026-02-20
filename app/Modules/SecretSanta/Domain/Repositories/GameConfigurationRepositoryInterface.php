<?php

namespace App\Modules\SecretSanta\Domain\Repositories;

use App\Modules\SecretSanta\Domain\Entities\GameConfiguration;

interface GameConfigurationRepositoryInterface
{
    public function getCurrent(): GameConfiguration;

    public function update(int $startgame): GameConfiguration;
}
