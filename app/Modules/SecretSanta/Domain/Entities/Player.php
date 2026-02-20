<?php

namespace App\Modules\SecretSanta\Domain\Entities;

class Player
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $nombre,
        public readonly bool $estado,
        public readonly bool $viewed,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            nombre: $data['nombre'],
            estado: $data['estado'] ?? true,
            viewed: $data['viewed'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'estado' => $this->estado,
            'viewed' => $this->viewed,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
