<?php

namespace App\Modules\SecretSanta\Domain\Entities;

class SecretFriendUrl
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $url,
        public readonly ?int $playerId,
        public readonly ?int $friends,
        public readonly bool $viewed,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            url: $data['url'],
            playerId: $data['player_id'] ?? null,
            friends: $data['friends'] ?? null,
            viewed: $data['viewed'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'player_id' => $this->playerId,
            'friends' => $this->friends,
            'viewed' => $this->viewed,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function isSelfAssigned(): bool
    {
        return $this->playerId !== null
            && $this->friends !== null
            && $this->playerId === $this->friends;
    }
}
