<?php

namespace App\Filament\MenuManager\Livewire;

use App\Models\Accommodation;
use App\Models\SitePage;
use App\Support\SiteMenuEndpoints;
use Illuminate\Support\Collection;
use NoteBrainsLab\FilamentMenuManager\MenuManager;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;

class MenuPanel extends \NoteBrainsLab\FilamentMenuManager\Livewire\MenuPanel
{
  /** @var list<string> */
  public array $usedEndpointKeys = [];

  public function refreshUsedModels(): void
  {
    parent::refreshUsedModels();

    if (! $this->menuId) {
      $this->usedEndpointKeys = [];

      return;
    }

    $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);

    $urls = $itemModel::query()
      ->where('menu_id', $this->menuId)
      ->pluck('url');

    $this->usedEndpointKeys = collect(SiteMenuEndpoints::all())
      ->filter(fn (array $endpoint): bool => $urls->contains(
        fn (?string $url): bool => SiteMenuEndpoints::urlsMatch($url, $endpoint['url'])
      ))
      ->pluck('key')
      ->all();
  }

  public function addEndpoint(string $key): void
  {
    $endpoint = SiteMenuEndpoints::find($key);

    if ($endpoint === null || in_array($key, $this->usedEndpointKeys, true)) {
      return;
    }

    $this->dispatch('menuItemAdded', [
      'title' => $endpoint['label'],
      'url' => $endpoint['url'],
      'target' => '_self',
      'icon' => $endpoint['icon'],
      'type' => 'custom',
    ]);

    $this->usedEndpointKeys[] = $key;
  }

  /**
   * @return list<array{key: string, label: string, url: string, icon: string, description?: string}>
   */
  public function getEndpoints(): array
  {
    return SiteMenuEndpoints::all();
  }

  public function getModelSources(): array
  {
    return array_values(array_unique(app(MenuManager::class)->getModelSources()));
  }

  public function getModelRecords(string $modelClass): Collection
  {
    if (! class_exists($modelClass)) {
      return collect();
    }

    $query = $modelClass::query();

    if ($modelClass === SitePage::class) {
      $query->published()->orderByDesc('is_homepage')->orderBy('sort_order')->orderBy('title');
    } elseif ($modelClass === Accommodation::class) {
      $query->bookableOnline()->orderBy('sort_order')->orderBy('name');
    }

    if ($this->modelSearch) {
      $table = (new $modelClass())->getTable();
      $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);
      $search = $this->modelSearch;

      $query->where(function ($q) use ($columns, $search) {
        foreach (['name', 'title', 'label'] as $col) {
          if (in_array($col, $columns)) {
            $q->orWhere($col, 'like', "%{$search}%");
          }
        }
      });
    }

    return $query->limit(50)->get();
  }
}
