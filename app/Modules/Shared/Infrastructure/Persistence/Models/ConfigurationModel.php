<?php

namespace App\Modules\Shared\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationModel extends Model
{
    protected $table = 'configurations';

    protected $fillable = ['variable', 'value'];
}
