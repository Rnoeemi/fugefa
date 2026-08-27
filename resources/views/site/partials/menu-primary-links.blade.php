@php
    use App\Services\ModuleService;

    $mode = $mode ?? 'desktop'; // desktop|mobile
    $linkClass = $linkClass ?? 'nav-link';
    $modules = app(ModuleService::class);
    $accommodationEnabled = $accommodationModuleEnabled ?? $modules->accommodationEnabled();

    $menuManager = app(\NoteBrainsLab\FilamentMenuManager\MenuManager::class);
    $menu = $menuManager->menusForLocation('primary')->first();
    $tree = $menu?->getTree() ?? [];

    // Ha még nincs menü konfigurálva, legyen legalább a korábbi 4 elem látható.
    if (! is_array($tree) || count($tree) === 0) {
        $tree = [
            ['title' => 'Rólunk', 'url' => route('home').'#rolunk', 'target' => '_self', 'children' => []],
            ['title' => 'Szállások', 'url' => route('accommodations.index'), 'target' => '_self', 'children' => []],
            ['title' => 'Foglalás', 'url' => url('/foglalas-panel'), 'target' => '_self', 'children' => []],
            ['title' => 'Időpontfoglalás', 'url' => url('/foglalas-panel'), 'target' => '_self', 'children' => []],
            ['title' => 'Kapcsolat', 'url' => route('contact'), 'target' => '_self', 'children' => []],
        ];
    }

    $filterTree = function (array $items) use (&$filterTree, $accommodationEnabled, $modules): array {
        $filtered = [];

        foreach ($items as $item) {
            $url = $item['url'] ?? '';

            if (! $accommodationEnabled && $modules->isAccommodationRelatedUrl($url)) {
                continue;
            }

            $children = $item['children'] ?? [];
            if (is_array($children) && count($children) > 0) {
                $item['children'] = $filterTree($children);
            }

            $filtered[] = $item;
        }

        return $filtered;
    };

    $tree = $filterTree($tree);

    $e = fn ($value) => e((string) $value);

    $renderDesktop = function (array $items, int $level) use (&$renderDesktop, $e, $linkClass): string {
        $html = '';

        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            $url = $item['url'] ?? '';
            if ($title === '' || $url === '') {
                continue;
            }

            $target = $item['target'] ?? '_self';
            $targetAttr = $target && $target !== '_self'
                ? ' target="'.$e($target).'" rel="noopener noreferrer"'
                : '';

            // Flex sorban is maradjon vizuálisan tagolva.
            $marginLeft = $level > 0 ? ('margin-left:'.($level * 12).'px;') : '';
            $levelClass = $level > 0 ? 'text-xs opacity-80' : '';

            $html .= '<a class="'.$e($linkClass).' '.$levelClass.'" href="'.$e($url).'" style="'.$marginLeft.'"'.$targetAttr.'>'
                . $e($title)
                .'</a>';

            $children = $item['children'] ?? [];
            if (is_array($children) && count($children) > 0) {
                $html .= $renderDesktop($children, $level + 1);
            }
        }

        return $html;
    };

    $renderMobile = function (array $items, int $level) use (&$renderMobile, $e, $linkClass): string {
        $html = '';

        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            $url = $item['url'] ?? '';
            if ($title === '' || $url === '') {
                continue;
            }

            $target = $item['target'] ?? '_self';
            $targetAttr = $target && $target !== '_self'
                ? ' target="'.$e($target).'" rel="noopener noreferrer"'
                : '';

            $levelClass = $level > 0 ? 'text-xs opacity-80' : '';
            $html .= '<a class="'.$e($linkClass).' '.$levelClass.'" href="'.$e($url).'"'.$targetAttr.'>'
                . $e($title)
                .'</a>';

            $children = $item['children'] ?? [];
            if (is_array($children) && count($children) > 0) {
                $html .= '<div class="flex flex-col gap-3 pl-4">';
                $html .= $renderMobile($children, $level + 1);
                $html .= '</div>';
            }
        }

        return $html;
    };
@endphp

@if ($mode === 'mobile')
    {!! $renderMobile($tree, 0) !!}
@else
    {!! $renderDesktop($tree, 0) !!}
@endif
