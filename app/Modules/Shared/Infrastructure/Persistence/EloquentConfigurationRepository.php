<?php

namespace App\Modules\Shared\Infrastructure\Persistence;

use App\Modules\Shared\Domain\Repositories\ConfigurationRepositoryInterface;
use App\Modules\Shared\Infrastructure\Persistence\Models\ConfigurationModel;

class EloquentConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function all(): array
    {
        return ConfigurationModel::pluck('value', 'variable')->toArray();
    }

    public function get(string $variable, mixed $default = null): mixed
    {
        $model = ConfigurationModel::where('variable', $variable)->first();
        return $model?->value ?? $default;
    }

    public function set(string $variable, mixed $value): void
    {
        ConfigurationModel::updateOrCreate(
            ['variable' => $variable],
            ['value'    => $value]
        );
    }
}
