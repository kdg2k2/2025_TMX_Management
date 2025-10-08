<?php

namespace App\Traits;

trait GuardTraits
{
    public function getGuard()
    {
        return auth('api')->check() ? auth('api') : auth();
    }
}
