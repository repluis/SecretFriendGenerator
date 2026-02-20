<?php

namespace App\Modules\SecretSanta\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameConfigurationModel extends Model
{
    use HasFactory;

    protected $table = 'game_configurations';

    protected $fillable = [
        'startgame',
    ];

    protected function casts(): array
    {
        return [
            'startgame' => 'integer',
        ];
    }

    public static function getCurrent(): self
    {
        $config = self::first();

        if (!$config) {
            $config = self::create([
                'startgame' => 0,
            ]);
        }

        return $config;
    }
}
