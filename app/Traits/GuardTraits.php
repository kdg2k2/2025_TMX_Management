<?php

namespace App\Traits;

trait GuardTraits
{
    public function getGuard()
    {
        return auth('api')->check() ? auth('api') : auth();
    }

    public function getUser()
    {
        return $this->getGuard()->user();
    }

    public function getUserId()
    {
        return $this->getUser()->id ?? null;
    }
}
