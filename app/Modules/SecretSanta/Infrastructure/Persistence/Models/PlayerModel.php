<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlayerModel extends Model
{
    use HasFactory;

    protected $table = 'players';

    protected $fillable = [
        'nombre',
        'estado',
        'viewed',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'viewed' => 'boolean',
        ];
    }

    public function urlRecord(): HasOne
    {
        return $this->hasOne(UrlModel::class, 'player_id');
    }
}
