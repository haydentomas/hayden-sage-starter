<?php

namespace App\Customizer;

/**
 * Sanitize Tailwind font scale select values.
 */
function sanitize_font_scale(string $value): string
{
    $allowed = ['text-sm','text-base','text-lg','text-xl','text-2xl','text-3xl','text-4xl'];
    return in_array($value, $allowed, true) ? $value : 'text-base';
}

function detect_font_format(string $url): string
{
    $path = parse_url($url, PHP_URL_PATH);
    $ext  = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));

    return match ($ext) {
        'woff2' => 'woff2',
        'woff'  => 'woff',
        'otf'   => 'opentype',
        default => 'truetype',
    };
}

/**
 * Build CSS for custom uploaded fonts + root vars.
 *
 * NOTE: Keeps your current mapping as-is:
 * - Heading upload -> --font-sans
 * - Body upload    -> --font-serif
 */
function get_custom_font_css(): string
{
    $body_id    = get_theme_mod('hayden_font_sans_file');  // Body upload
    $heading_id = get_theme_mod('hayden_font_serif_file'); // Heading upload

    if (!$body_id && !$heading_id) {
        return '';
    }

    $css      = '';
    $rootVars = '';

    if ($heading_id) {
        $heading_url = wp_get_attachment_url($heading_id);
        if ($heading_url) {
            $format   = detect_font_format($heading_url);
            $css     .= "@font-face{font-family:'HaydenHeading';src:url('{$heading_url}') format('{$format}');font-weight:400;font-style:normal;font-display:swap;}\n";
            $rootVars .= "--font-sans:'HaydenHeading',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;";
        }
    }

    if ($body_id) {
        $body_url = wp_get_attachment_url($body_id);
        if ($body_url) {
            $format   = detect_font_format($body_url);
            $css     .= "@font-face{font-family:'HaydenBody';src:url('{$body_url}') format('{$format}');font-weight:400;font-style:normal;font-display:swap;}\n";
            $rootVars .= "--font-serif:'HaydenBody','Times New Roman',Georgia,serif;";
        }
    }

    if ($rootVars) {
        $css .= ":root{{$rootVars}}\n";
    }

    return trim($css);
}

/**
 * Utility: clamp footer columns to 1-4.
 */
function sanitize_columns($value): int
{
    $value = (int) $value;
    return ($value >= 1 && $value <= 4) ? $value : 3;
}
