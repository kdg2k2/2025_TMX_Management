<?php

namespace App\Http\Middleware;

use App\Traits\GuardTraits;
use Closure;
use App\Models\LogAccessHistory;
use Illuminate\Support\Facades\Route;

class LogAccess
{
    use GuardTraits;
  public function handle($request, Closure $next)
    {
        $currentUrl = $request->fullUrl();

        $body = $request->all();

        function formatFileSize($bytes, $precision = 2)
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
        }

        if ($request->hasFile(null)) {
            $files = [];
            foreach ($request->files->all() as $key => $file) {
                if (is_array($file)) {
                    foreach ($file as $f) {
                        $files[$key][] = [
                            'name' => $f->getClientOriginalName(),
                            'size' => formatFileSize($f->getSize()),
                            'mime' => $f->getMimeType(),
                        ];
                    }
                } else {
                    $files[$key] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => formatFileSize($file->getSize()),
                        'mime' => $file->getMimeType(),
                    ];
                }
            }
            $body['files'] = $files;
        }

        LogAccessHistory::create([
            'user_id' => $this->getGuard()->user()->id,
            'url' => $currentUrl,
            'method' => $request->method(),
            'body' => json_encode($body, JSON_UNESCAPED_UNICODE), // Tránh lỗi UTF-8
        ]);

        return $next($request);
    }
}
