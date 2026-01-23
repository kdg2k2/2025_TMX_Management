<?php

namespace App\Traits;

use Illuminate\Support\Str;

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

    public function removeAssetUrl(string $path): string
    {
        $appUrl = rtrim(config('app.url'), '/');

        // Chuẩn hoá slash
        $normalizedPath = str_replace(['\\'], '/', $path);
        $normalizedAppUrl = str_replace(['\\'], '/', $appUrl);

        // Nếu là full asset url
        if (Str::startsWith($normalizedPath, $normalizedAppUrl)) {
            $relative = substr($normalizedPath, strlen($normalizedAppUrl));
            return ltrim($relative, '/');
        }

        return $path;
    }
}
