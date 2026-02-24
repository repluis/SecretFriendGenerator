<?php

namespace App\Modules\Shared\Domain\Services;

use App\Modules\Shared\Domain\Repositories\ConfigurationRepositoryInterface;

/**
 * Registered as a singleton in ModuleServiceProvider.
 * Loads all configuration from DB once per request and caches in memory.
 */
class ConfigurationService
{
    private array $cache = [];
    private bool $loaded = false;

    public function __construct(
        private ConfigurationRepositoryInterface $repo
    ) {}

    public function get(string $variable, mixed $default = null): mixed
    {
        $this->load();
        return $this->cache[$variable] ?? $default;
    }

    public function all(): array
    {
        $this->load();
        return $this->cache;
    }

    public function set(string $variable, mixed $value): void
    {
        $this->repo->set($variable, $value);
        $this->cache[$variable] = $value;
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->cache  = $this->repo->all();
        $this->loaded = true;
    }
}
