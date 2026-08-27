<?php

namespace App\Support;

/**
 * GrapesJS oldal-CSS tisztítás: érvénytelen Desktop media query kibontása.
 * (width: '100%' device → @media (max-width: 100%) soha nem érvényesül a böngészőben.)
 */
final class SiteBuilderCss
{
    public static function sanitize(string $css): string
    {
        $css = SiteColors::replaceHardcodedColors($css);

        return self::unwrapInvalidDesktopMedia($css);
    }

    /**
     * @param  array<string, mixed>  $projectData
     * @return array<string, mixed>
     */
    public static function sanitizeProjectData(array $projectData): array
    {
        if (! isset($projectData['styles']) || ! is_array($projectData['styles'])) {
            return $projectData;
        }

        foreach ($projectData['styles'] as $index => $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $media = (string) ($rule['mediaText'] ?? $rule['media'] ?? '');
            if ($media === '' || ! self::isInvalidDesktopMedia($media)) {
                continue;
            }

            $rule['mediaText'] = '';
            unset($rule['media']);
            if (($rule['atRuleType'] ?? null) === 'media') {
                $rule['atRuleType'] = '';
            }
            $projectData['styles'][$index] = $rule;
        }

        return $projectData;
    }

    public static function unwrapInvalidDesktopMedia(string $css): string
    {
        if ($css === '' || ! str_contains(strtolower($css), 'max-width')) {
            return $css;
        }

        $pattern = '/@media\s*\(\s*max-width\s*:\s*100%\s*\)\s*\{/i';
        $result = '';
        $offset = 0;
        $length = strlen($css);

        while (preg_match($pattern, $css, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $matchStart = (int) $matches[0][1];
            $matchLen = strlen($matches[0][0]);
            $result .= substr($css, $offset, $matchStart - $offset);

            $bodyStart = $matchStart + $matchLen;
            $depth = 1;
            $i = $bodyStart;
            while ($i < $length && $depth > 0) {
                $ch = $css[$i];
                if ($ch === '{') {
                    $depth++;
                } elseif ($ch === '}') {
                    $depth--;
                }
                $i++;
            }

            $bodyEnd = $i - 1;
            $body = substr($css, $bodyStart, max(0, $bodyEnd - $bodyStart));
            $result .= trim($body);
            $offset = $i;
        }

        $result .= substr($css, $offset);

        return $result;
    }

    public static function isInvalidDesktopMedia(string $media): bool
    {
        return (bool) preg_match('/max-width\s*:\s*100%/i', $media);
    }
}
