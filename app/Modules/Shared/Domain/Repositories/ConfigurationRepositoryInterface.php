<?php

namespace App\Modules\Shared\Domain\Repositories;

interface ConfigurationRepositoryInterface
{
    /** Returns all configurations as ['variable' => 'value'] */
    public function all(): array;

    public function get(string $variable, mixed $default = null): mixed;

    public function set(string $variable, mixed $value): void;
}
