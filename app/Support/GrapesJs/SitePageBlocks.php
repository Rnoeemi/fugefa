<?php

namespace App\Support\GrapesJs;

/**
 * @deprecated Use SiteBlockCatalog — kept as a thin compatibility wrapper.
 */
class SitePageBlocks
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definitions(): array
    {
        return SiteBlockCatalog::definitionsFor('page');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function layoutDefinitions(string $part): array
    {
        return SiteBlockCatalog::definitionsFor($part);
    }
}
