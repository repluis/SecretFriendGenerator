<?php

namespace App\Modules\Auth\Domain\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Busca un usuario por su identificación única.
     *
     * @param string $identification - La identificación del usuario a buscar.
     * @return User|null - El modelo User si se encuentra, null si no existe.
     */
    public function findByIdentification(string $identification): ?User;
}
