<?php

namespace App\Services;

use App\Models\SitePage;
use App\Support\SiteBuilderCss;
use App\Support\SiteColors;
use Illuminate\Support\Facades\File;

class SitePageCssWriter
{
    public function relativePath(SitePage $page): string
    {
        return "css/site-pages/{$page->id}.css";
    }

    public function absolutePath(SitePage $page): string
    {
        return public_path($this->relativePath($page));
    }

    public function write(SitePage $page, string $css): string
    {
        $path = $this->absolutePath($page);
        $css = SiteBuilderCss::sanitize($css);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $css);

        return $this->relativePath($page);
    }

    public function delete(SitePage $page): void
    {
        $path = $this->absolutePath($page);

        if (is_file($path)) {
            File::delete($path);
        }
    }

    public function url(SitePage $page): ?string
    {
        $path = $this->absolutePath($page);

        if (! is_file($path)) {
            return null;
        }

        $version = $page->updated_at?->timestamp ?? filemtime($path);

        return asset($this->relativePath($page)).'?v='.$version;
    }

    public function writeLayoutPart(string $part, string $css): string
    {
        $part = $this->assertLayoutPart($part);
        $relative = "css/site-layout/{$part}.css";
        $path = public_path($relative);
        $css = SiteBuilderCss::sanitize($css);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $css);

        return $relative;
    }

    public function layoutPartUrl(string $part, ?int $version = null): ?string
    {
        $part = $this->assertLayoutPart($part);
        $relative = "css/site-layout/{$part}.css";
        $path = public_path($relative);

        if (! is_file($path)) {
            return null;
        }

        $version ??= filemtime($path);

        return asset($relative).'?v='.$version;
    }

    protected function assertLayoutPart(string $part): string
    {
        if (! in_array($part, ['header', 'footer'], true)) {
            throw new \InvalidArgumentException("Invalid layout part [{$part}].");
        }

        return $part;
    }
}
