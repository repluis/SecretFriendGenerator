<?php

namespace App\Modules\Transaction\Domain\Entities;

class Transaction
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $type,
        public readonly float $amount,
        public readonly ?string $description,
        public readonly bool $active,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'],
            type: $data['type'],
            amount: (float) $data['amount'],
            description: $data['description'] ?? null,
            active: (bool) ($data['active'] ?? true),
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
