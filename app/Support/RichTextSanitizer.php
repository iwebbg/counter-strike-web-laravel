<?php

namespace App\Support;

/** Sanitizes the small HTML subset produced by the news editor. */
class RichTextSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><h2><h3><ul><ol><li><blockquote><a>';

    /** Removes executable markup and all attributes except safe link targets. */
    public function sanitize(string $html): string
    {
        $html = preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = strip_tags($html, self::ALLOWED_TAGS);

        return trim((string) preg_replace_callback('/<([a-z0-9]+)(\s[^>]*)?>/i', function (array $match): string {
            $tag = strtolower($match[1]);

            if ($tag !== 'a') {
                return '<'.$tag.'>';
            }

            $attributes = $match[2] ?? '';
            if (! preg_match('/href\s*=\s*(["\'])(https?:\/\/|mailto:)(.*?)\1/i', $attributes, $href)) {
                return '<a>';
            }

            $url = htmlspecialchars($href[2].$href[3], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            return '<a href="'.$url.'" rel="noopener noreferrer">';
        }, $html));
    }
}
