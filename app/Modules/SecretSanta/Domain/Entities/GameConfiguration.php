<?php

namespace App\Modules\SecretSanta\Domain\Entities;

class GameConfiguration
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $startgame,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            startgame: $data['startgame'] ?? 0,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'startgame' => $this->startgame,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function isStarted(): bool
    {
        return $this->startgame === 1;
    }
}
