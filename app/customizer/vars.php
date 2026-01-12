<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * CSS Variable Builder
 * -------------------------------------------------------------------------
 *
 * Responsibility:
 * - Read Customizer values
 * - Resolve presets + defaults
 * - Produce a flat map of CSS custom properties
 *
 * This file:
 * - DOES NOT echo CSS
 * - DOES NOT register settings
 * - DOES NOT know about blocks
 *
 * output.php / editor.php consume the result.
 */

/* -------------------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------------------- */

function get_theme_mod_hex(string $key, string $fallback): string
{
    $val = sanitize_hex_color(get_theme_mod($key, $fallback));
    return $val ?: $fallback;
}

function calculate_contrast_from_hex(string $hex, string $light = '#ffffff', string $dark = '#111111'): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) {
        return $dark;
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
    return ($brightness > 150) ? $dark : $light;
}

function tailwind_scale_map(): array
{
    return [
        'text-sm'   => 'var(--text-sm)',
        'text-base' => 'var(--text-base)',
        'text-lg'   => 'var(--text-lg)',
        'text-xl'   => 'var(--text-xl)',
        'text-2xl'  => 'var(--text-2xl)',
        'text-3xl'  => 'var(--text-3xl)',
        'text-4xl'  => 'var(--text-4xl)',
    ];
}

function mobile_variant_for_scale(string $key): string
{
    $map  = tailwind_scale_map();
    $keys = array_keys($map);

    $index = array_search($key, $keys, true);
    if ($index === false) {
        return $map['text-base'];
    }

    return $map[$keys[max(0, $index - 1)]] ?? $map['text-base'];
}

/* -------------------------------------------------------------------------
 * Main builder
 * ------------------------------------------------------------------------- */

function build_css_vars(array $overrides = []): array
{
    /**
     * Presets & defaults
     */
    $colors  = defaults_colors();
    $radius  = presets_radius();
    $spacing = presets_spacing();
    $type    = defaults_typography();
    $tw      = tailwind_scale_map();

    /**
     * ---------------------------------------------------------------------
     * Colours
     * ---------------------------------------------------------------------
     */
    $vars = [
        '--color-primary'       => get_theme_mod_hex('hayden_primary_color', $colors['primary']),
        '--color-surface'       => get_theme_mod_hex('hayden_surface_color', $colors['surface']),
        '--color-headings'      => get_theme_mod_hex('hayden_heading_color', $colors['headings']),
        '--color-body'          => get_theme_mod_hex('hayden_body_color', $colors['body']),
        '--color-footer'        => get_theme_mod_hex('hayden_footer_color', $colors['footer_bg']),
        '--color-footer-text'   => get_theme_mod_hex('hayden_footer_text_color', $colors['footer_text']),
    ];

    $widget_bg = get_theme_mod_hex('hayden_widget_bg_color', $colors['widget_bg']);

    $vars += [
        '--color-widget-bg'       => $widget_bg,
        '--color-widget-heading' => get_theme_mod_hex('hayden_widget_title_color', $colors['widget_heading']),
        '--color-widget-text'    => get_theme_mod_hex('hayden_widget_text_color', $colors['widget_text']),
        '--color-widget-link'    => get_theme_mod_hex('hayden_widget_link_color', $colors['widget_link']),
        '--color-surface-soft'   => $widget_bg, // alias
    ];

    /**
     * Footer widget colours (explicit settings)
     */
    $vars += [
        '--color-footer-widget-heading' => get_theme_mod_hex('hayden_footer_widget_title_color', $colors['footer_widget_heading']),
        '--color-footer-widget-text'    => get_theme_mod_hex('hayden_footer_widget_text_color', $colors['footer_widget_text']),
        '--color-footer-widget-link'    => get_theme_mod_hex('hayden_footer_widget_link_color', $colors['footer_widget_link']),
    ];

    $vars += [
        '--card-bg'         => get_theme_mod_hex('hayden_card_bg', $colors['card_bg']),
        '--card-heading'    => get_theme_mod_hex('hayden_card_heading', $colors['card_heading']),
        '--card-text'       => get_theme_mod_hex('hayden_card_text', $colors['card_text']),
        '--card-text-muted' => get_theme_mod_hex('hayden_card_text_muted', $colors['card_text_muted']),
    ];

    /**
     * Navigation colours
     */
    $surface = $vars['--color-surface'];

    $vars += [
        '--color-nav-link'         => get_theme_mod_hex('hayden_nav_link_color', $colors['nav_link']),
        '--color-nav-link-hover'   => get_theme_mod_hex('hayden_nav_link_hover_color', $colors['nav_link_hover']),
        '--color-nav-sub-bg'       => get_theme_mod_hex('hayden_nav_sub_bg_color', $colors['nav_sub_bg']),
        '--color-nav-sub-link'     => get_theme_mod_hex('hayden_nav_sub_link_color', $colors['nav_sub_link']),
        '--color-nav-sub-hover-bg' => get_theme_mod_hex('hayden_nav_sub_hover_bg_color', $colors['nav_sub_hover_bg']),
        '--color-nav-toggle'       => calculate_contrast_from_hex($surface),
    ];

    /**
     * ---------------------------------------------------------------------
     * Layout
     * ---------------------------------------------------------------------
     */
    $container = absint(get_theme_mod('hayden_container_width', $type['container_width']));
    $logo      = absint(get_theme_mod('hayden_logo_max_height', $type['logo_height']));

    $vars += [
        '--site-max-width'       => max(960, min(1920, $container)) . 'px',
        '--site-logo-max-height' => max(40, min(200, $logo)) . 'px',
    ];

    /**
     * ---------------------------------------------------------------------
     * Typography
     * ---------------------------------------------------------------------
     */
    $body_key = get_theme_mod('hayden_body_font_size', $type['body_size']);
    $h1_key   = get_theme_mod('hayden_h1_font_size', $type['h1_size']);
    $h2_key   = get_theme_mod('hayden_h2_font_size', $type['h2_size']);
    $h3_key   = get_theme_mod('hayden_h3_font_size', $type['h3_size']);

    $vars += [
        '--body-font-size-mobile'  => mobile_variant_for_scale($body_key),
        '--body-font-size-desktop' => $tw[$body_key] ?? $tw[$type['body_size']],
        '--body-font-size'         => $tw[$body_key] ?? $tw[$type['body_size']],

        '--h1-font-size-mobile'    => mobile_variant_for_scale($h1_key),
        '--h1-font-size-desktop'   => $tw[$h1_key] ?? $tw[$type['h1_size']],

        '--h2-font-size-mobile'    => mobile_variant_for_scale($h2_key),
        '--h2-font-size-desktop'   => $tw[$h2_key] ?? $tw[$type['h2_size']],

        '--h3-font-size-mobile'    => mobile_variant_for_scale($h3_key),
        '--h3-font-size-desktop'   => $tw[$h3_key] ?? $tw[$type['h3_size']],
    ];

    /**
     * ---------------------------------------------------------------------
     * Spacing (external)
     * ---------------------------------------------------------------------
     */
    $spacing_key = get_theme_mod('hayden_spacing_scale', 'medium');
    $space       = $spacing[$spacing_key] ?? $spacing['medium'];

    $vars += [
        '--section-space-mobile'  => $space['mobile'],
        '--section-space-desktop' => $space['desktop'],
    ];

    /**
     * ---------------------------------------------------------------------
     * Block inner spacing (padding)
     * ---------------------------------------------------------------------
     */
    $block_padding_key = get_theme_mod('hayden_block_padding', 'default');

    $block_padding_map = [
        'compact'  => $spacing['compact']['desktop'],
        'default'  => $spacing['comfortable']['desktop'],
        'spacious' => $spacing['spacious']['desktop'],
    ];

    $vars['--block-padding'] = $block_padding_map[$block_padding_key]
        ?? $block_padding_map['default'];

    /**
     * ---------------------------------------------------------------------
     * Radius
     * ---------------------------------------------------------------------
     */
    $radius_key = get_theme_mod('hayden_radius_style', 'soft');
    $rad        = $radius[$radius_key] ?? $radius['soft'];

    $vars += [
        '--radius-sm'   => $rad['sm'],
        '--radius-md'   => $rad['md'],
        '--radius-lg'   => $rad['lg'],
        '--radius-xl'   => $rad['xl'],
        '--radius-2xl'  => $rad['2xl'],
        '--radius-full' => $rad['full'],
        '--radius-pill' => $rad['full'], // legacy alias
    ];

    /**
     * Apply overrides (editor context etc.)
     */
    foreach ($overrides as $k => $v) {
        if (is_string($k) && (is_string($v) || is_numeric($v))) {
            $vars[$k] = (string) $v;
        }
    }

    return $vars;
}

/* -------------------------------------------------------------------------
 * Utilities
 * ------------------------------------------------------------------------- */

function vars_to_css(array $vars, string $selector = ':root'): string
{
    $lines = [];

    foreach ($vars as $name => $value) {
        if (! preg_match('/^--[a-z0-9\-_]+$/i', $name)) {
            continue;
        }

        $value = str_ireplace(['</style', '<', '>'], '', (string) $value);
        $lines[] = $name . ':' . trim($value) . ';';
    }

    return $selector . '{' . implode('', $lines) . '}';
}

function live_preview_payload(): array
{
    return [
        'radius'  => presets_radius(),
        'spacing' => presets_spacing(),
    ];
}
