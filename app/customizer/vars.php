<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * CSS Variable Builder
 * -------------------------------------------------------------------------
 *
 * Generates a consistent CSS variables map using:
 * - Customizer theme_mod values
 * - defaults from defaults.php
 * - presets (radius/spacing)
 *
 * Nothing here prints output; output.php/editor.php will consume this.
 */

if (! function_exists(__NAMESPACE__ . '\\get_theme_mod_hex')) {
    /**
     * Fetch a hex theme_mod safely with a fallback.
     */
    function get_theme_mod_hex(string $mod_key, string $fallback): string
    {
        $val = sanitize_hex_color(get_theme_mod($mod_key, $fallback));
        return $val ?: $fallback;
    }
}

if (! function_exists(__NAMESPACE__ . '\\calculate_contrast_from_hex')) {
    /**
     * Basic brightness check to choose a contrasting text colour.
     */
    function calculate_contrast_from_hex(string $hex, string $light = '#ffffff', string $dark = '#111111'): string
    {
        $hex = ltrim((string) $hex, '#');
        if (strlen($hex) !== 6) {
            return $dark;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return ($brightness > 150) ? $dark : $light;
    }
}

if (! function_exists(__NAMESPACE__ . '\\tailwind_scale_map')) {
    /**
     * Map Tailwind font scale keys to CSS vars (provided by theme css).
     */
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
}

if (! function_exists(__NAMESPACE__ . '\\mobile_variant_for_scale')) {
    /**
     * Choose a “one step down” mobile size from a Tailwind scale key.
     */
    function mobile_variant_for_scale(string $key): string
    {
        $map  = tailwind_scale_map();
        $keys = array_keys($map);

        $index = array_search($key, $keys, true);
        if ($index === false) {
            return $map['text-base'];
        }

        $mobile_key = $keys[max(0, $index - 1)];
        return $map[$mobile_key] ?? $map['text-base'];
    }
}

if (! function_exists(__NAMESPACE__ . '\\build_css_vars')) {
    /**
     * Build CSS var map for the theme.
     *
     * @param array $overrides Optional overrides (useful for editor context).
     * @return array<string,string> CSS variables without the leading colon block.
     */
    function build_css_vars(array $overrides = []): array
    {
        $colors   = defaults_colors();
        $radius   = presets_radius();
        $spacing  = presets_spacing();
        $type     = defaults_typography();
        $tw_scale = tailwind_scale_map();

        // --- Read theme_mod values with fallbacks ---
        $primary   = get_theme_mod_hex('hayden_primary_color', $colors['primary']);
        $surface   = get_theme_mod_hex('hayden_surface_color', $colors['surface']);
        $headings  = get_theme_mod_hex('hayden_heading_color', $colors['headings']);
        $body      = get_theme_mod_hex('hayden_body_color', $colors['body']);
        $muted     = get_theme_mod_hex('hayden_body_muted_color', $colors['body_muted']);

        $footer_bg   = get_theme_mod_hex('hayden_footer_color', $colors['footer_bg']);
        $footer_text = get_theme_mod_hex('hayden_footer_text_color', $colors['footer_text']);

        $widget_bg      = get_theme_mod_hex('hayden_widget_bg_color', $colors['widget_bg']);
        $widget_heading = get_theme_mod_hex('hayden_widget_title_color', $colors['widget_heading']);
        $widget_text    = get_theme_mod_hex('hayden_widget_text_color', $colors['widget_text']);
        $widget_link    = get_theme_mod_hex('hayden_widget_link_color', $colors['widget_link']);

        // Footer widget colours default back to widget colours if unset
        $footer_widget_heading = get_theme_mod_hex('hayden_footer_widget_title_color', $widget_heading);
        $footer_widget_text    = get_theme_mod_hex('hayden_footer_widget_text_color', $widget_text);
        $footer_widget_link    = get_theme_mod_hex('hayden_footer_widget_link_color', $widget_link);

        $card_bg         = get_theme_mod_hex('hayden_card_bg', $colors['card_bg']);
        $card_heading    = get_theme_mod_hex('hayden_card_heading', $colors['card_heading']);
        $card_text       = get_theme_mod_hex('hayden_card_text', $colors['card_text']);
        $card_text_muted = get_theme_mod_hex('hayden_card_text_muted', $colors['card_text_muted']);

        $nav_link         = get_theme_mod_hex('hayden_nav_link_color', $colors['nav_link']);
        $nav_link_hover   = get_theme_mod_hex('hayden_nav_link_hover_color', $colors['nav_link_hover']);
        $nav_sub_bg       = get_theme_mod_hex('hayden_nav_sub_bg_color', $colors['nav_sub_bg']);
        $nav_sub_link     = get_theme_mod_hex('hayden_nav_sub_link_color', $colors['nav_sub_link']);
        $nav_sub_hover_bg = get_theme_mod_hex('hayden_nav_sub_hover_bg_color', $colors['nav_sub_hover_bg']);

        // Derived: nav toggle contrast from surface
        $nav_toggle = calculate_contrast_from_hex($surface, '#ffffff', '#111111');

        // Radius preset
        $radius_style = get_theme_mod('hayden_radius_style', 'soft');
        if (! is_string($radius_style) || ! isset($radius[$radius_style])) {
            $radius_style = 'soft';
        }
        $rad = $radius[$radius_style];

        // Spacing preset
        $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');
        if (! is_string($spacing_choice) || ! isset($spacing[$spacing_choice])) {
            $spacing_choice = 'comfortable';
        }
        $space = $spacing[$spacing_choice];

        // Layout numeric
        $container_width = absint(get_theme_mod('hayden_container_width', $type['container_width']));
        if ($container_width < 960 || $container_width > 1440) {
            $container_width = (int) $type['container_width'];
        }

        $logo_height = absint(get_theme_mod('hayden_logo_max_height', $type['logo_height']));
        if ($logo_height < 40 || $logo_height > 200) {
            $logo_height = (int) $type['logo_height'];
        }

        // Type scale
        $body_key = (string) get_theme_mod('hayden_body_font_size', $type['body_size']);
        $h1_key   = (string) get_theme_mod('hayden_h1_font_size', $type['h1_size']);
        $h2_key   = (string) get_theme_mod('hayden_h2_font_size', $type['h2_size']);
        $h3_key   = (string) get_theme_mod('hayden_h3_font_size', $type['h3_size']);

        $body_desktop = $tw_scale[$body_key] ?? $tw_scale[$type['body_size']];
        $body_mobile  = mobile_variant_for_scale($body_key);

        $h1_desktop = $tw_scale[$h1_key] ?? $tw_scale[$type['h1_size']];
        $h2_desktop = $tw_scale[$h2_key] ?? $tw_scale[$type['h2_size']];
        $h3_desktop = $tw_scale[$h3_key] ?? $tw_scale[$type['h3_size']];

        $h1_mobile = mobile_variant_for_scale($h1_key);
        $h2_mobile = mobile_variant_for_scale($h2_key);
        $h3_mobile = mobile_variant_for_scale($h3_key);

        // --- Final vars map ---
        $vars = [
            '--color-primary'              => $primary,
            '--color-surface'              => $surface,

            // if you're using bg-surface-soft in markup, keep it in sync with widget bg
            '--color-surface-soft'         => $widget_bg,

            '--color-headings'             => $headings,
            '--color-body'                 => $body,
            '--color-body-muted'           => $muted,

            '--color-footer'               => $footer_bg,
            '--color-footer-text'          => $footer_text,

            '--color-widget-bg'            => $widget_bg,
            '--color-widget-heading'       => $widget_heading,
            '--color-widget-text'          => $widget_text,
            '--color-widget-link'          => $widget_link,

            '--color-footer-widget-heading'=> $footer_widget_heading,
            '--color-footer-widget-text'   => $footer_widget_text,
            '--color-footer-widget-link'   => $footer_widget_link,

            '--card-bg'                    => $card_bg,
            '--card-heading'               => $card_heading,
            '--card-text'                  => $card_text,
            '--card-text-muted'            => $card_text_muted,

            '--color-nav-link'             => $nav_link,
            '--color-nav-link-hover'       => $nav_link_hover,
            '--color-nav-sub-bg'           => $nav_sub_bg,
            '--color-nav-sub-link'         => $nav_sub_link,
            '--color-nav-sub-hover-bg'     => $nav_sub_hover_bg,
            '--color-nav-toggle'           => $nav_toggle,

            '--site-max-width'             => $container_width . 'px',
            '--site-logo-max-height'       => $logo_height . 'px',

            '--body-font-size-mobile'      => $body_mobile,
            '--body-font-size-desktop'     => $body_desktop,
            '--body-font-size'             => $body_desktop,

            '--h1-font-size-mobile'        => $h1_mobile,
            '--h1-font-size-desktop'       => $h1_desktop,

            '--h2-font-size-mobile'        => $h2_mobile,
            '--h2-font-size-desktop'       => $h2_desktop,

            '--h3-font-size-mobile'        => $h3_mobile,
            '--h3-font-size-desktop'       => $h3_desktop,

            '--section-space-mobile'       => $space['mobile'],
            '--section-space-desktop'      => $space['desktop'],

            // Tailwind v4 radius tokens
            '--radius-sm'                  => $rad['sm'],
            '--radius-md'                  => $rad['md'],
            '--radius-lg'                  => $rad['lg'],
            '--radius-xl'                  => $rad['xl'],
            '--radius-2xl'                 => $rad['2xl'],
            '--radius-full'                => $rad['full'],

            // Legacy alias still used by some CSS
            '--radius-pill'                => $rad['full'],
        ];

        // Apply overrides (used for editor context if needed later)
        foreach ($overrides as $k => $v) {
            if (is_string($k) && (is_string($v) || is_numeric($v))) {
                $vars[$k] = (string) $v;
            }
        }

        return $vars;
    }
}

function vars_to_css(array $vars, string $selector = ':root'): string
{
    $lines = [];

    foreach ($vars as $name => $value) {
        $name  = trim((string) $name);
        $value = trim((string) $value);

        if ($name === '' || $value === '') {
            continue;
        }

        // Only allow real CSS custom property names like --color-primary
        if (! preg_match('/^--[a-z0-9\-_]+$/i', $name)) {
            continue;
        }

        // Hardening: prevent breaking out of <style> context or injecting markup
        $value = str_ireplace(['</style', '<', '>'], '', $value);

        $lines[] = $name . ':' . $value . ';';
    }

    return $selector . '{' . implode('', $lines) . '}';
}



if (! function_exists(__NAMESPACE__ . '\\live_preview_payload')) {
    /**
     * Data payload for Customizer preview JS.
     * Keeps JS in sync with PHP presets.
     */
    function live_preview_payload(): array
    {
        return [
            'radius'  => presets_radius(),
            'spacing' => presets_spacing(),
        ];
    }
}
