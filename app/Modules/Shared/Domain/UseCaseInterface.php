<?php

namespace App\Modules\Shared\Domain;

interface UseCaseInterface
{
    public function execute(array $params = []): mixed;
}
