<?php

namespace App\Traits;

use Str;

trait CheckLocalTraits
{
    public function isLocal()
    {
        $url = url('/');
        return Str::contains($url, ['127.0.0.1', 'localhost']);
    }
}
