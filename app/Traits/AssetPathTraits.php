<?php

namespace App\Traits;

trait AssetPathTraits
{
    public function getAssetImage($url)
    {
        $path = config('custom.DEFAULT_AVATAR');
        if (!empty($url))
            $path = $url;
        return $this->getAssetUrl($path);
    }

    public function getAssetUrl($url = null)
    {
        if (!$url)
            return null;
        return asset($url);
    }
}
