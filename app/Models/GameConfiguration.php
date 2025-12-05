<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameConfiguration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'startgame',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'startgame' => 'integer',
        ];
    }

    /**
     * Get the current game configuration (singleton pattern)
     *
     * @return GameConfiguration
     */
    public static function getCurrent(): GameConfiguration
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
