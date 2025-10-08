<?php

namespace App\Traits;

trait GetValueFromArrayByKeyTraits
{
    public function getValueFromArrayByKey(array $array, mixed $key)
    {
        if ($key !== null)
            $res = $array[$key] ?? null;
        else
            $res = $array;
        return $res;
    }
}
