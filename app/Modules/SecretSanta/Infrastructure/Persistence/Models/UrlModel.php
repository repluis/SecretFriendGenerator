<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlModel extends Model
{
    use HasFactory;

    protected $table = 'urls';

    protected $fillable = [
        'url',
        'player_id',
        'friends',
        'viewed',
    ];

    protected function casts(): array
    {
        return [
            'viewed' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(PlayerModel::class, 'player_id');
    }

    public function friendPlayer(): BelongsTo
    {
        return $this->belongsTo(PlayerModel::class, 'friends');
    }
}
