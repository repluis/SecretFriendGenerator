<?php

namespace App\Modules\User\Domain\Entities;

class UserEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly bool $active,
        public readonly ?string $identification = null,
        public readonly ?string $createdAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'] ?? null,
            active: $data['active'] ?? true,
            identification: $data['identification'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'active' => $this->active,
            'identification' => $this->identification,
            'created_at' => $this->createdAt,
        ];
    }
}
