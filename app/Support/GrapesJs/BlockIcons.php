<?php

namespace App\Support\GrapesJs;

final class BlockIcons
{
    /**
     * @return array<string, string> icon key => path d (or multi-path SVG inner)
     */
    public static function paths(): array
    {
        return [
            'square' => '<rect x="5" y="5" width="14" height="14" rx="2"/>',
            'columns-2' => '<rect x="4" y="5" width="7" height="14" rx="1"/><rect x="13" y="5" width="7" height="14" rx="1"/>',
            'columns-3' => '<rect x="3" y="5" width="5" height="14" rx="1"/><rect x="9.5" y="5" width="5" height="14" rx="1"/><rect x="16" y="5" width="5" height="14" rx="1"/>',
            'sparkles' => '<path d="M12 3l1.2 4.2L17.5 8.5 13.2 9.8 12 14l-1.2-4.2L6.5 8.5l4.3-1.3L12 3z"/><path d="M18 13l.7 2.3L21 16l-2.3.7L18 19l-.7-2.3L15 16l2.3-.7L18 13z"/>',
            'squares-2x2' => '<rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>',
            'megaphone' => '<path d="M4 11v2a2 2 0 002 2h1l3 4h2l-1.5-4H15a5 5 0 005-5V9a5 5 0 00-5-5H9L6 3H6a2 2 0 00-2 2v6z"/>',
            'document-text' => '<path d="M7 3h7l4 4v14a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
            'chat-bubble' => '<path d="M5 6a3 3 0 013-3h8a3 3 0 013 3v7a3 3 0 01-3 3H11l-4 3v-3H8a3 3 0 01-3-3V6z"/>',
            'phone' => '<path d="M7 3h3l1.5 4-2 1.5a12 12 0 006 6L17 13l4 1.5V18a2 2 0 01-2 2A15 15 0 015 5a2 2 0 012-2z"/>',
            'photo' => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="M3 16l5-4 4 3 3-2 6 3"/>',
            'arrows-up-down' => '<path d="M8 5v14M8 5l-3 3M8 5l3 3M16 19V5M16 19l-3-3M16 19l3-3"/>',
            'home' => '<path d="M4 11l8-7 8 7v8a1 1 0 01-1 1h-5v-5H10v5H5a1 1 0 01-1-1v-8z"/>',
            'list-bullet' => '<path d="M9 7h11M9 12h11M9 17h11"/><circle cx="5" cy="7" r="1.2"/><circle cx="5" cy="12" r="1.2"/><circle cx="5" cy="17" r="1.2"/>',
            'star' => '<path d="M12 3l2.5 6.5L21 11l-5 4.2L17.5 21 12 17.5 6.5 21 8 15.2 3 11l6.5-1.5L12 3z"/>',
            'calendar' => '<rect x="4" y="6" width="16" height="14" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>',
            'bars-3' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'bars-3-bottom-left' => '<path d="M4 7h16M4 12h10M4 17h7"/>',
            'rectangle-group' => '<rect x="3" y="4" width="8" height="7" rx="1"/><rect x="13" y="4" width="8" height="7" rx="1"/><rect x="3" y="13" width="18" height="7" rx="1"/>',
            'minus' => '<path d="M5 12h14"/>',
            'check-badge' => '<path d="M9 12l2 2 4-4"/><path d="M12 3l2.1 1.2L16.5 4l.4 2.4L19 8l-1.2 2.1L18 12l-2.1 1.2L15.5 16l-2.4.4L12 19l-1.2-2.1L8.5 16l-.4-2.4L6 12l1.2-2.1L7 8l2.4-.4L10 4.5 12 3z"/>',
            'question-mark' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 114 2c-.8.5-1.5 1-1.5 2v.5"/><circle cx="12" cy="17" r=".8" fill="currentColor" stroke="none"/>',
            'banknotes' => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M7 10v4M17 10v4"/>',
            'queue-list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
            'play' => '<circle cx="12" cy="12" r="9"/><path d="M10 8l6 4-6 4V8z"/>',
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'map' => '<path d="M9 4l6 2 5-2v16l-5 2-6-2-5 2V6l5-2z"/><path d="M15 6v16M9 4v16"/>',
            'map-pin' => '<path d="M12 21s7-5.2 7-11a7 7 0 10-14 0c0 5.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'share' => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.5 13.5l7 4M15.5 6.5l-7 4"/>',
            'scale' => '<path d="M12 3v18M5 7h14"/><path d="M7 7l-3 7h6L7 7zM17 7l-3 7h6l-3-7z"/>',
            'magnifying-glass' => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4 4"/>',
            'information-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
            'envelope' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 7 9-7"/>',
        ];
    }

    public static function media(?string $icon): string
    {
        $paths = self::paths();
        $inner = $paths[$icon ?? ''] ?? $paths['square'];

        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$inner.'</svg>';
    }
}
