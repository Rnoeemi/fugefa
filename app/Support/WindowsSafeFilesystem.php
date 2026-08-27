<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

/**
 * Windows cannot atomically rename over an existing file; PHP rename()
 * then fails with "Access denied (code: 5)". Blade view compilation hits this often.
 */
class WindowsSafeFilesystem extends Filesystem
{
    public function replace($path, $content, $mode = null)
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $directory = dirname($path);
        $tempPath = tempnam($directory, basename($path));

        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            if (file_exists($path)) {
                @unlink($path);
            }

            if (@rename($tempPath, $path)) {
                return;
            }

            usleep(50_000);
        }

        if (@copy($tempPath, $path)) {
            @unlink($tempPath);

            return;
        }

        @unlink($tempPath);

        throw new \ErrorException("Unable to replace [{$path}].");
    }
}
