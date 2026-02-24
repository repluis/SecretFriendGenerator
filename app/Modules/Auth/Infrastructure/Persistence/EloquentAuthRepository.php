<?php

namespace App\Modules\Auth\Infrastructure\Persistence;

use App\Models\User;
use App\Modules\Auth\Domain\Repositories\AuthRepositoryInterface;

class EloquentAuthRepository implements AuthRepositoryInterface
{
    /**
     * Busca un usuario por su identificación única en la base de datos.
     *
     * @param string $identification - La identificación del usuario a buscar.
     * @return User|null - El modelo User si se encuentra, null si no existe.
     */
    public function findByIdentification(string $identification): ?User
    {
        return User::where('identification', $identification)->first();
    }
}
