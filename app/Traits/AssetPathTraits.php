<?php

namespace App\Traits;

trait AssetPathTraits
{
    public function getAssetImage($url)
    {
        $path = config('custom.default_avatar');
        if (!empty($url))
            $path = $url;
        return $this->getAssetUrl($path);
    }

    public function getAssetUrl($url)
    {
        return asset($url);
    }
}
