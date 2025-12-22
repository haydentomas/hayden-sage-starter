<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * Customizer Defaults & Presets
 * -------------------------------------------------------------------------
 *
 * This file is the single source of truth for:
 * - default colours
 * - spacing presets
 * - radius presets (Tailwind v4 compatible)
 * - typography defaults
 *
 * Nothing in here outputs CSS or touches hooks.
 */

/**
 * Default colour values (Customizer fallbacks)
 */
function defaults_colors(): array
{
    return [
        'primary'              => '#f97316',
        'surface'              => '#FFFAF8',
        'headings'             => '#f97316',
        'body'                 => '#111111',
        'body_muted'           => '#262626',

        'footer_bg'            => '#020617',
        'footer_text'          => '#94a3b8',

        'widget_bg'            => '#000000',
        'widget_heading'       => '#f97316',
        'widget_text'          => '#ffffff',
        'widget_link'          => '#f97316',

        'footer_widget_heading'=> '#f97316',
        'footer_widget_text'   => '#ffffff',
        'footer_widget_link'   => '#f97316',

        'card_bg'              => '#000000',
        'card_heading'         => '#f97316',
        'card_text'            => '#ffffff',
        'card_text_muted'      => '#e5e5e5',

        'nav_link'             => '#111111',
        'nav_link_hover'       => '#f97316',
        'nav_sub_bg'           => '#020617',
        'nav_sub_link'         => '#f97316',
        'nav_sub_hover_bg'     => '#3b1d08',
    ];
}

/**
 * Radius presets (Tailwind v4 tokens)
 */
function presets_radius(): array
{
    return [
        'sharp' => [
            'sm' => '0rem',
            'md' => '0rem',
            'lg' => '0rem',
            'xl' => '0rem',
            '2xl'=> '0rem',
            'full'=>'0rem',
        ],
        'soft' => [
            'sm' => '0.125rem',
            'md' => '0.375rem',
            'lg' => '0.5rem',
            'xl' => '0.75rem',
            '2xl'=> '1rem',
            'full'=>'9999px',
        ],
        'round' => [
            'sm' => '0.375rem',
            'md' => '0.5rem',
            'lg' => '0.75rem',
            'xl' => '1rem',
            '2xl'=> '1.25rem',
            'full'=>'9999px',
        ],
    ];
}

/**
 * Spacing presets
 */
function presets_spacing(): array
{
    return [
        'compact' => [
            'mobile'  => '1.75rem',
            'desktop' => '3rem',
        ],
        'comfortable' => [
            'mobile'  => '2.5rem',
            'desktop' => '4rem',
        ],
        'spacious' => [
            'mobile'  => '3.5rem',
            'desktop' => '6rem',
        ],
    ];
}

/**
 * Typography defaults
 */
function defaults_typography(): array
{
    return [
        'container_width' => 1120,
        'logo_height'     => 80,

        'body_size' => 'text-lg',
        'h1_size'   => 'text-4xl',
        'h2_size'   => 'text-3xl',
        'h3_size'   => 'text-2xl',
    ];
}
