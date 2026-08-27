<?php

namespace App\Models;

use App\Services\SitePageCssWriter;
use NoteBrainsLab\FilamentMenuManager\Concerns\HasMenuItems;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'html',
    'css',
    'grapes_data',
    'seo',
    'is_published',
    'is_homepage',
    'sort_order',
])]
class SitePage extends Model
{
    use HasMenuItems;

    protected function casts(): array
    {
        return [
            'grapes_data' => 'array',
            'seo' => 'array',
            'is_published' => 'boolean',
            'is_homepage' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SitePage $page): void {
            $page->slug = self::uniqueSlugFor($page);

            if ($page->is_homepage) {
                static::query()
                    ->whereKeyNot($page->id ?? 0)
                    ->update(['is_homepage' => false]);
            }
        });

        static::saved(function (SitePage $page): void {
            if ($page->wasChanged('css') || $page->wasRecentlyCreated) {
                app(SitePageCssWriter::class)->write($page, (string) ($page->css ?? ''));
            }
        });

        static::deleted(function (SitePage $page): void {
            app(SitePageCssWriter::class)->delete($page);
        });
    }

    /**
     * Üres / foglalt slug helyett mindig érvényes, egyedi értéket ad.
     */
    public static function uniqueSlugFor(self $page): string
    {
        $base = Str::slug((string) $page->slug);
        if ($base === '') {
            $base = Str::slug((string) $page->title);
        }
        if ($base === '') {
            $base = 'oldal';
        }

        $slug = $base;
        $i = 2;
        while (
            static::query()
                ->where('slug', $slug)
                ->when($page->exists, fn (Builder $q) => $q->whereKeyNot($page->getKey()))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public static function homepage(): ?self
    {
        return static::query()->published()->where('is_homepage', true)->first();
    }

    public function cssPublicUrl(): ?string
    {
        return app(SitePageCssWriter::class)->url($this);
    }

    public function getMenuLabel(): string
    {
        return $this->is_homepage
            ? $this->title.' (Fooldal)'
            : $this->title;
    }

    public function getMenuUrl(): string
    {
        if ($this->is_homepage) {
            return route('home');
        }

        $slug = trim((string) $this->slug);
        if ($slug === '') {
            return route('home');
        }

        return route('site-pages.show', ['slug' => $slug]);
    }

    public function getMenuIcon(): ?string
    {
        return $this->is_homepage ? 'heroicon-o-home' : 'heroicon-o-document-text';
    }
}
