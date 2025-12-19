<?php

namespace App;

/**
 * ------------------------------------------------------------
 * THEME CUSTOMIZER
 * Panels, sections, settings, colour pickers and output CSS.
 * Clean rewrite: single-pass, no duplicates, Tailwind v4 radius vars,
 * live preview for colors + spacing + radius.
 * ------------------------------------------------------------
 */

/**
 * ------------------------------------------------------------
 * Helpers (namespaced)
 * ------------------------------------------------------------
 */
if (!function_exists(__NAMESPACE__ . '\\hayden_sanitize_font_scale')) {
    function hayden_sanitize_font_scale(string $value): string
    {
        $allowed = ['text-sm','text-base','text-lg','text-xl','text-2xl','text-3xl','text-4xl'];
        return in_array($value, $allowed, true) ? $value : 'text-base';
    }
}

if (!function_exists(__NAMESPACE__ . '\\hayden_detect_font_format')) {
    function hayden_detect_font_format(string $url): string
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
}

if (!function_exists(__NAMESPACE__ . '\\hayden_get_custom_font_css')) {
    function hayden_get_custom_font_css(): string
    {
        $body_id    = get_theme_mod('hayden_font_sans_file');  // Body upload
        $heading_id = get_theme_mod('hayden_font_serif_file'); // Heading upload

        if (!$body_id && !$heading_id) {
            return '';
        }

        $css      = '';
        $rootVars = '';

        // NOTE: Keeping your existing mapping as-is (matches your current theme):
        // - Heading upload -> --font-sans
        // - Body upload    -> --font-serif

        if ($heading_id) {
            $heading_url = wp_get_attachment_url($heading_id);
            if ($heading_url) {
                $format   = hayden_detect_font_format($heading_url);
                $css     .= "@font-face{font-family:'HaydenHeading';src:url('{$heading_url}') format('{$format}');font-weight:400;font-style:normal;font-display:swap;}\n";
                $rootVars .= "--font-sans:'HaydenHeading',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;";
            }
        }

        if ($body_id) {
            $body_url = wp_get_attachment_url($body_id);
            if ($body_url) {
                $format   = hayden_detect_font_format($body_url);
                $css     .= "@font-face{font-family:'HaydenBody';src:url('{$body_url}') format('{$format}');font-weight:400;font-style:normal;font-display:swap;}\n";
                $rootVars .= "--font-serif:'HaydenBody','Times New Roman',Georgia,serif;";
            }
        }

        if ($rootVars) {
            $css .= ":root{{$rootVars}}\n";
        }

        return trim($css);
    }
}

/**
 * ------------------------------------------------------------
 * Customizer register
 * ------------------------------------------------------------
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {

    $sanitize_select = function (array $allowed, $default) {
        return function ($value) use ($allowed, $default) {
            return in_array($value, $allowed, true) ? $value : $default;
        };
    };

    $active_when = function (string $setting_id, $equals) {
        return function ($control) use ($setting_id, $equals) {
            $setting = $control->manager->get_setting($setting_id);
            $value   = $setting ? $setting->value() : null;
            return $value === $equals;
        };
    };

    $active_when_footer_variant = function (string $variant) {
        return function ($control) use ($variant) {
            $setting = $control->manager->get_setting('hayden_footer_variant');
            return $setting && $setting->value() === $variant;
        };
    };

    /**
     * MASTER PANEL
     */
    $wp_customize->add_panel('hayden_theme_panel', [
        'title'       => __('Theme Settings', 'hayden'),
        'description' => __('Global layout, footer, grid and colours.', 'hayden'),
        'priority'    => 10,
    ]);

    /**
     * HEADER
     */
    $wp_customize->add_section('hayden_header_section', [
        'title'       => __('Header Layout', 'hayden'),
        'description' => __('Choose a layout style for the header.', 'hayden'),
        'priority'    => 20,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_header_layout', [
        'default'           => 'default',
        'transport'         => 'refresh',
        'sanitize_callback' => $sanitize_select(['default', 'logo-top', 'nav-center-cta', 'none'], 'default'),
    ]);

    $wp_customize->add_control('hayden_header_layout_control', [
        'label'    => __('Header layout style', 'hayden'),
        'section'  => 'hayden_header_section',
        'settings' => 'hayden_header_layout',
        'type'     => 'select',
        'choices'  => [
            'default'        => __('Default – logo left, nav right', 'hayden'),
            'logo-top'       => __('Logo top, nav below', 'hayden'),
            'nav-center-cta' => __('Logo left, nav centre, CTA right', 'hayden'),
            'none'           => __('None (disable theme header)', 'hayden'),
        ],
    ]);

    $wp_customize->add_setting('hayden_header_content_page_id', [
        'default'           => (int) get_option('hayden_header_page_id', 0),
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('hayden_header_content_page_id_control', [
        'label'           => __('Header content page (blocks)', 'hayden'),
        'description'     => __('Choose the page whose blocks will render as the header when Header Layout is set to “None”.', 'hayden'),
        'section'         => 'hayden_header_section',
        'settings'        => 'hayden_header_content_page_id',
        'type'            => 'dropdown-pages',
        'active_callback' => $active_when('hayden_header_layout', 'none'),
    ]);

    $wp_customize->add_setting('hayden_header_sticky', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('hayden_header_sticky', [
        'type'        => 'checkbox',
        'section'     => 'hayden_header_section',
        'label'       => __('Sticky header', 'hayden'),
        'description' => __('Keep the header visible while scrolling (works with any header layout).', 'hayden'),
    ]);

    $wp_customize->add_setting('hayden_header_full_width', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('hayden_header_full_width', [
        'type'        => 'checkbox',
        'section'     => 'hayden_header_section',
        'label'       => __('Full-width header', 'hayden'),
        'description' => __('Header spans full width (navigation container becomes fluid).', 'hayden'),
    ]);

    $wp_customize->add_setting('hayden_nav_link_style', [
        'default'           => 'basic',
        'transport'         => 'refresh',
        'sanitize_callback' => $sanitize_select(['basic', 'pill', 'underline'], 'basic'),
    ]);

    $wp_customize->add_control('hayden_nav_link_style_control', [
        'label'       => __('Navigation link style', 'hayden'),
        'description' => __('Applies to top-level menu links only (dropdown styling stays consistent).', 'hayden'),
        'section'     => 'hayden_header_section',
        'settings'    => 'hayden_nav_link_style',
        'type'        => 'radio',
        'choices'     => [
            'basic'     => __('Basic (text hover)', 'hayden'),
            'pill'      => __('Pilled (background hover)', 'hayden'),
            'underline' => __('Underlined (underline hover)', 'hayden'),
        ],
    ]);

    $wp_customize->add_setting('hayden_header_cta_label', [
        'default'           => __('Start a Project', 'hayden'),
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('hayden_header_cta_label_control', [
        'label'           => __('CTA Button Text', 'hayden'),
        'section'         => 'hayden_header_section',
        'settings'        => 'hayden_header_cta_label',
        'type'            => 'text',
        'active_callback' => $active_when('hayden_header_layout', 'nav-center-cta'),
    ]);

    $wp_customize->add_setting('hayden_header_cta_url', [
        'default'           => home_url('/start-a-project'),
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control('hayden_header_cta_url_control', [
        'label'           => __('CTA Button URL', 'hayden'),
        'section'         => 'hayden_header_section',
        'settings'        => 'hayden_header_cta_url',
        'type'            => 'url',
        'active_callback' => $active_when('hayden_header_layout', 'nav-center-cta'),
    ]);

    $wp_customize->add_setting('hayden_logo_max_height', [
        'default'           => 80,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('hayden_logo_max_height_control', [
        'label'       => __('Logo max height (px)', 'hayden'),
        'section'     => 'hayden_header_section',
        'settings'    => 'hayden_logo_max_height',
        'type'        => 'number',
        'input_attrs' => ['min' => 40, 'max' => 200, 'step' => 4],
        'description' => __('Controls the rendered logo height when a custom logo is set.', 'hayden'),
    ]);

    /**
     * GLOBAL DESIGN (Radius)
     * Tailwind v4 friendly: sets --radius-* vars + legacy --radius-pill alias
     */
    $wp_customize->add_section('hayden_design_section', [
        'title'       => __('Global Design', 'hayden'),
        'description' => __('Global design tokens like border radius.', 'hayden'),
        'priority'    => 27,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_radius_style', [
        'default'           => 'soft',
        'transport'         => 'postMessage',
        'sanitize_callback' => $sanitize_select(['sharp', 'soft', 'round'], 'soft'),
    ]);

    $wp_customize->add_control('hayden_radius_style_control', [
        'label'       => __('Radius style', 'hayden'),
        'description' => __('Controls rounded corners across buttons, cards, widgets, menus, dropdowns, etc.', 'hayden'),
        'section'     => 'hayden_design_section',
        'settings'    => 'hayden_radius_style',
        'type'        => 'radio',
        'choices'     => [
            'sharp' => __('Sharp (0)', 'hayden'),
            'soft'  => __('Soft (default)', 'hayden'),
            'round' => __('Rounded', 'hayden'),
        ],
    ]);

    /**
     * LAYOUT / CONTAINER
     */
    $wp_customize->add_section('hayden_layout_section', [
        'title'       => __('Layout & Container', 'hayden'),
        'description' => __('Control the maximum width of the main content container.', 'hayden'),
        'priority'    => 25,
        'panel'       => 'hayden_theme_panel',
    ]);

    $default_container_width = 1120;

    $wp_customize->add_setting('hayden_container_width', [
        'default'           => $default_container_width,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $current_width = (int) get_theme_mod('hayden_container_width', $default_container_width);

    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_container_width_control',
        [
            'label'       => __('Site content max width (px)', 'hayden'),
            'section'     => 'hayden_layout_section',
            'settings'    => 'hayden_container_width',
            'type'        => 'range',
            'input_attrs' => [
                'min'   => 960,
                'max'   => 1440,
                'step'  => 10,
                'class' => 'hayden-container-width-range',
            ],
            'description' => sprintf('<span id="hayden-container-width-value">%dpx</span>', $current_width),
        ]
    ));

    /**
     * GLOBAL SPACING
     */
    $wp_customize->add_section('hayden_spacing_section', [
        'title'       => __('Global Spacing', 'hayden'),
        'description' => __('Controls vertical spacing between sections and blocks.', 'hayden'),
        'priority'    => 28,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_spacing_scale', [
        'default'           => 'comfortable',
        'transport'         => 'postMessage',
        'sanitize_callback' => $sanitize_select(['compact', 'comfortable', 'spacious'], 'comfortable'),
    ]);

    $wp_customize->add_control('hayden_spacing_scale_control', [
        'label'       => __('Vertical spacing scale', 'hayden'),
        'section'     => 'hayden_spacing_section',
        'settings'    => 'hayden_spacing_scale',
        'type'        => 'select',
        'choices'     => [
            'compact'     => __('Compact', 'hayden'),
            'comfortable' => __('Comfortable (default)', 'hayden'),
            'spacious'    => __('Spacious', 'hayden'),
        ],
        'description' => __('Affects global section/block spacing via CSS variables.', 'hayden'),
    ]);

    /**
     * THEME COLOURS
     */
    $wp_customize->add_section('hayden_color_section', [
        'title'       => __('Theme Colours', 'hayden'),
        'description' => '',
        'priority'    => 30,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_color_intro', ['sanitize_callback' => '__return_null']);
    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_intro_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_intro',
            'type'        => 'custom',
            'description' => '<p class="hayden-customizer-intro" style="margin:0 0 8px;color:#ffffff;">'
                . esc_html__('Control global brand colours.', 'hayden')
                . '</p>',
        ]
    ));

    $add_color = function (string $id, string $label, string $default) use ($wp_customize) {
        $wp_customize->add_setting($id, [
            'default'           => $default,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            "{$id}_control",
            [
                'label'    => __($label, 'hayden'),
                'section'  => 'hayden_color_section',
                'settings' => $id,
            ]
        ));
    };

    $add_color('hayden_primary_color',        'Primary Colour',                 '#f97316');
    $add_color('hayden_surface_color',        'Background Colour',              '#FFFAF8');
    $add_color('hayden_heading_color',        'Heading Text Colour',            '#f97316');
    $add_color('hayden_body_color',           'Body Text Colour',               '#111111');
    $add_color('hayden_body_muted_color',     'Body Muted Text Colour',         '#262626');
    $add_color('hayden_footer_color',         'Footer Background Colour',       '#020617');
    $add_color('hayden_footer_text_color',    'Footer Text Colour',             '#94a3b8');

    $add_color('hayden_widget_bg_color',      'Widget Background Colour',       '#000000');

    $wp_customize->add_setting('hayden_color_heading_widgets', ['sanitize_callback' => '__return_null']);
    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_widgets_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_widgets',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                . '<h3 style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                . esc_html__('Widget colours', 'hayden')
                . '</h3>',
        ]
    ));

    $add_color('hayden_widget_title_color',   'Widget Title Colour',            '#f97316');
    $add_color('hayden_widget_text_color',    'Widget Text Colour',             '#ffffff');
    $add_color('hayden_widget_link_color',    'Widget Link Colour',             '#f97316');

    $wp_customize->add_setting('hayden_color_heading_footer_widgets', ['sanitize_callback' => '__return_null']);
    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_footer_widgets_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_footer_widgets',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                . '<h3 style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                . esc_html__('Footer widget colours', 'hayden')
                . '</h3>',
        ]
    ));

    $add_color('hayden_footer_widget_title_color', 'Footer Widget Title Colour', '#f97316');
    $add_color('hayden_footer_widget_text_color',  'Footer Widget Text Colour',  '#ffffff');
    $add_color('hayden_footer_widget_link_color',  'Footer Widget Link Colour',  '#f97316');

    $wp_customize->add_setting('hayden_color_heading_nav', ['sanitize_callback' => '__return_null']);
    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_nav_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_nav',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                . '<h3 style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                . esc_html__('Navigation colours', 'hayden')
                . '</h3>',
        ]
    ));

    $add_color('hayden_nav_link_color',        'Nav Parent Link Colour',         '#111111');
    $add_color('hayden_nav_link_hover_color',  'Nav Link Hover/Active Colour',   '#f97316');
    $add_color('hayden_nav_sub_bg_color',      'Dropdown Background Colour',     '#020617');
    $add_color('hayden_nav_sub_link_color',    'Dropdown Link Colour',           '#f97316');
    $add_color('hayden_nav_sub_hover_bg_color','Dropdown Link Hover Background', '#3b1d08');

    /**
     * CONTENT CARDS
     */
    $wp_customize->add_section('hayden_cards_section', [
        'title'       => __('Content Cards', 'hayden'),
        'description' => __('Colours for blog, portfolio and other content cards.', 'hayden'),
        'priority'    => 32,
        'panel'       => 'hayden_theme_panel',
    ]);

    $card_colors = [
        'hayden_card_bg'         => ['Card Background Colour', '#000000'],
        'hayden_card_heading'    => ['Card Heading Colour',    '#f97316'],
        'hayden_card_text'       => ['Card Text Colour',       '#ffffff'],
        'hayden_card_text_muted' => ['Card Muted Text Colour', '#e5e5e5'],
    ];

    foreach ($card_colors as $id => [$label, $default]) {
        $wp_customize->add_setting($id, [
            'default'           => $default,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            "{$id}_control",
            [
                'label'    => __($label, 'hayden'),
                'section'  => 'hayden_cards_section',
                'settings' => $id,
            ]
        ));
    }

    /**
     * GRID DISPLAY
     */
    $wp_customize->add_section('grid_display_section', [
        'title'       => __('Grid Display', 'hayden'),
        'description' => __('Controls how many posts/projects show initially.', 'hayden'),
        'priority'    => 35,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('grid_projects_initial_items', [
        'default'           => 6,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('grid_projects_initial_items_control', [
        'label'       => __('Projects: initial items', 'hayden'),
        'section'     => 'grid_display_section',
        'settings'    => 'grid_projects_initial_items',
        'type'        => 'number',
        'input_attrs' => ['min' => 1, 'max' => 48],
    ]);

    $wp_customize->add_setting('grid_blog_initial_items', [
        'default'           => 6,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('grid_blog_initial_items_control', [
        'label'       => __('Blog: initial items', 'hayden'),
        'section'     => 'grid_display_section',
        'settings'    => 'grid_blog_initial_items',
        'type'        => 'number',
        'input_attrs' => ['min' => 1, 'max' => 48],
    ]);

    /**
     * TYPOGRAPHY
     */
    $wp_customize->add_section('hayden_typography_section', [
        'title'       => __('Typography', 'hayden'),
        'description' => __('Upload custom fonts. Leave empty to use the default theme fonts.', 'hayden'),
        'panel'       => 'hayden_theme_panel',
        'priority'    => 40,
    ]);

    $wp_customize->add_setting('hayden_font_serif_file', [
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control(new \WP_Customize_Media_Control(
        $wp_customize,
        'hayden_font_serif_file',
        [
            'label'       => __('Heading font (serif)', 'hayden'),
            'description' => __('Upload a WOFF2/WOFF/TTF file. This will replace the default --font-sans stack (your current theme mapping).', 'hayden'),
            'section'     => 'hayden_typography_section',
            'mime_type'   => '',
        ]
    ));

    $wp_customize->add_setting('hayden_font_sans_file', [
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control(new \WP_Customize_Media_Control(
        $wp_customize,
        'hayden_font_sans_file',
        [
            'label'       => __('Body font (sans-serif)', 'hayden'),
            'description' => __('Upload a WOFF2/WOFF/TTF file. This will replace the default --font-serif stack (your current theme mapping).', 'hayden'),
            'section'     => 'hayden_typography_section',
            'mime_type'   => '',
        ]
    ));

    $tw_font_sizes = [
        'text-sm'   => __('Small (Tailwind text-sm)', 'hayden'),
        'text-base' => __('Base (Tailwind text-base)', 'hayden'),
        'text-lg'   => __('Large (Tailwind text-lg)', 'hayden'),
        'text-xl'   => __('XL (Tailwind text-xl)', 'hayden'),
        'text-2xl'  => __('2XL (Tailwind text-2xl)', 'hayden'),
        'text-3xl'  => __('3XL (Tailwind text-3xl)', 'hayden'),
        'text-4xl'  => __('4XL (Tailwind text-4xl)', 'hayden'),
    ];

    foreach ([
        'hayden_body_font_size' => ['Body font size', 'text-lg'],
        'hayden_h1_font_size'   => ['H1 font size',   'text-4xl'],
        'hayden_h2_font_size'   => ['H2 font size',   'text-3xl'],
        'hayden_h3_font_size'   => ['H3 font size',   'text-2xl'],
    ] as $id => [$label, $default]) {
        $wp_customize->add_setting($id, [
            'default'           => $default,
            'transport'         => 'refresh',
            'sanitize_callback' => __NAMESPACE__ . '\\hayden_sanitize_font_scale',
        ]);

        $wp_customize->add_control("{$id}_control", [
            'label'    => __($label, 'hayden'),
            'section'  => 'hayden_typography_section',
            'settings' => $id,
            'type'     => 'select',
            'choices'  => $tw_font_sizes,
        ]);
    }

    /**
     * FOOTER LAYOUT
     */
    $wp_customize->add_section('hayden_footer_section', [
        'title'       => __('Footer Layout', 'hayden'),
        'description' => __('Choose a footer variant or use the widget-column layout.', 'hayden'),
        'priority'    => 45,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_footer_variant', [
        'default'           => 'theme-default',
        'transport'         => 'refresh',
        'sanitize_callback' => $sanitize_select(['none', 'theme-default', 'footer-a', 'footer-b', 'footer-c'], 'theme-default'),
    ]);

    $wp_customize->add_control('hayden_footer_variant_control', [
        'label'       => __('Footer variant', 'hayden'),
        'description' => __('Select which footer template to render.', 'hayden'),
        'section'     => 'hayden_footer_section',
        'settings'    => 'hayden_footer_variant',
        'type'        => 'select',
        'choices'     => [
            'theme-default' => __('Theme default (widgets)', 'hayden'),
            'footer-a'      => __('Footer A', 'hayden'),
            'footer-b'      => __('Footer B', 'hayden'),
            'footer-c'      => __('Footer C', 'hayden'),
            'none'          => __('None (disable theme footer)', 'hayden'),
        ],
    ]);

    $wp_customize->add_setting('hayden_footer_content_page_id', [
        'default'           => (int) get_option('hayden_footer_page_id', 0),
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('hayden_footer_content_page_id_control', [
        'label'           => __('Footer content page (blocks)', 'hayden'),
        'description'     => __('Choose the page whose blocks will render as the footer when Footer Variant is set to “None”.', 'hayden'),
        'section'         => 'hayden_footer_section',
        'settings'        => 'hayden_footer_content_page_id',
        'type'            => 'dropdown-pages',
        'active_callback' => $active_when('hayden_footer_variant', 'none'),
    ]);

    $wp_customize->add_setting('hayden_footer_c_cta_enabled', [
        'default'           => 1,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('hayden_footer_c_cta_enabled_control', [
        'label'           => __('Footer C: Enable CTA panel', 'hayden'),
        'section'         => 'hayden_footer_section',
        'settings'        => 'hayden_footer_c_cta_enabled',
        'type'            => 'checkbox',
        'active_callback' => $active_when_footer_variant('footer-c'),
    ]);

    $fields = [
        'kicker'     => ['Footer C: CTA kicker', 'Ready when you are'],
        'title'      => ['Footer C: CTA title', "Want a WordPress build that's fast, tidy, and easy to manage?"],
        'text'       => ['Footer C: CTA text', "Let's turn your design into a lightweight, high-performing site — with a clean editor experience for clients."],
        'btn1_label' => ['Footer C: Button 1 label', 'Start a project'],
        'btn1_url'   => ['Footer C: Button 1 URL', home_url('/contact')],
        'btn2_label' => ['Footer C: Button 2 label', 'View work'],
        'btn2_url'   => ['Footer C: Button 2 URL', home_url('/projects')],
    ];

    foreach ($fields as $key => [$label, $default]) {
        $setting_id = "hayden_footer_c_cta_{$key}";
        $is_url     = (substr($key, -4) === '_url');

        $wp_customize->add_setting($setting_id, [
            'default'           => $default,
            'transport'         => 'refresh',
            'sanitize_callback' => $is_url ? 'esc_url_raw' : 'sanitize_text_field',
        ]);

        $wp_customize->add_control("{$setting_id}_control", [
            'label'           => __($label, 'hayden'),
            'section'         => 'hayden_footer_section',
            'settings'        => $setting_id,
            'type'            => $is_url ? 'url' : 'text',
            'active_callback' => function ($control) use ($active_when_footer_variant) {
                $is_footer_c = $active_when_footer_variant('footer-c')($control);

                $cta_setting = $control->manager->get_setting('hayden_footer_c_cta_enabled');
                $cta_on      = $cta_setting ? (bool) $cta_setting->value() : false;

                return $is_footer_c && $cta_on;
            },
        ]);
    }

    $wp_customize->add_setting('hayden_footer_columns', [
        'default'           => 3,
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            $value = (int) $value;
            return ($value >= 1 && $value <= 4) ? $value : 3;
        },
    ]);

    $wp_customize->add_control('hayden_footer_columns_control', [
        'label'       => __('Footer columns (widgets)', 'hayden'),
        'description' => __('Used by the theme default widget footer.', 'hayden'),
        'section'     => 'hayden_footer_section',
        'settings'    => 'hayden_footer_columns',
        'type'        => 'select',
        'choices'     => [
            1 => __('1 column', 'hayden'),
            2 => __('2 columns', 'hayden'),
            3 => __('3 columns', 'hayden'),
            4 => __('4 columns', 'hayden'),
        ],
    ]);

    // Move widget sections into panel
    $footer_sidebars = ['sidebar-footer-1','sidebar-footer-2','sidebar-footer-3','sidebar-footer-4'];
    $priority        = 50;

    foreach ($footer_sidebars as $index => $sidebar_id) {
        $section_id = 'sidebar-widgets-' . $sidebar_id;
        $section    = $wp_customize->get_section($section_id);

        if ($section) {
            $section->panel    = 'hayden_theme_panel';
            $section->priority = $priority + $index;
            $section->title    = sprintf(__('Footer Column %d Widgets', 'hayden'), $index + 1);
        }
    }

    /**
     * Panel footer note
     */
    $wp_customize->add_section('hayden_panel_footer_section', [
        'title'       => __('Need Help?', 'hayden'),
        'description' => '',
        'priority'    => 200,
        'panel'       => 'hayden_theme_panel',
    ]);

    $wp_customize->add_setting('hayden_panel_footer_note', ['sanitize_callback' => '__return_null']);

    $site_url = esc_url('https://wp.bbi.co.uk');

    $html  = '<div class="hayden-customizer-panel-footer" style="margin:16px 0 24px;padding:12px 14px;border-radius:8px;background:#020617;border:1px solid rgba(148,163,184,0.4);color:#e5e7eb;font-size:13px;line-height:1.5;">';
    $html .= '  <div style="display:flex;align-items:center;gap:10px;">';
    $html .= '    <div style="flex:1 1 auto;">';
    $html .= '      <strong style="display:block;margin-bottom:2px;font-weight:600;">Hayden Sage Starter</strong>';
    $html .= '      <span style="display:block;margin-bottom:4px;opacity:.9;">Need help or want a custom build based on this theme?</span>';
    $html .= '      <a href="' . $site_url . '" target="_blank" rel="noopener" style="color:#f97316;text-decoration:none;">Visit wp.bbi.co.uk →</a>';
    $html .= '    </div>';
    $html .= '  </div>';
    $html .= '  <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px;">';
    $html .= '    <a href="https://wp.bbi.co.uk/blog" target="_blank" rel="noopener" style="display:block;width:100%;text-align:center;padding:10px 14px;background:#1e293b;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;">Blog</a>';
    $html .= '    <a href="https://wp.bbi.co.uk/contact" target="_blank" rel="noopener" style="display:block;width:100%;text-align:center;padding:10px 14px;background:#f97316;color:#000;border-radius:6px;text-decoration:none;font-weight:600;">Contact Us</a>';
    $html .= '  </div>';
    $html .= '</div>';

    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_panel_footer_note_control',
        [
            'section'     => 'hayden_panel_footer_section',
            'settings'    => 'hayden_panel_footer_note',
            'type'        => 'custom',
            'description' => $html,
        ]
    ));
});

/**
 * ------------------------------------------------------------
 * Body class: nav style (single)
 * ------------------------------------------------------------
 */
add_filter('body_class', function (array $classes) {
    $style = get_theme_mod('hayden_nav_link_style', 'basic');
    if (!in_array($style, ['basic', 'pill', 'underline'], true)) {
        $style = 'basic';
    }
    $classes[] = 'nav-style-' . $style;
    return $classes;
});

/**
 * ------------------------------------------------------------
 * Output dynamic CSS variables to frontend
 * Includes Tailwind v4 radius vars + legacy vars
 * ------------------------------------------------------------
 */
add_action('wp_head', function () {

    $radius_style = get_theme_mod('hayden_radius_style', 'soft');

    $radius_presets = [
        'sharp' => ['sm'=>'0rem','md'=>'0rem','lg'=>'0rem','xl'=>'0rem','2xl'=>'0rem','full'=>'0rem'],
        'soft'  => ['sm'=>'0.125rem','md'=>'0.375rem','lg'=>'0.5rem','xl'=>'0.75rem','2xl'=>'1rem','full'=>'9999px'],
        'round' => ['sm'=>'0.375rem','md'=>'0.5rem','lg'=>'0.75rem','xl'=>'1rem','2xl'=>'1.25rem','full'=>'9999px'],
    ];
    $rad = $radius_presets[$radius_style] ?? $radius_presets['soft'];

    $primary    = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    $surface    = sanitize_hex_color(get_theme_mod('hayden_surface_color', '#FFFAF8')) ?: '#FFFAF8';
    $headings   = sanitize_hex_color(get_theme_mod('hayden_heading_color', '#f97316')) ?: '#f97316';
    $body_text  = sanitize_hex_color(get_theme_mod('hayden_body_color', '#111111')) ?: '#111111';
    $body_muted = sanitize_hex_color(get_theme_mod('hayden_body_muted_color', '#262626')) ?: '#262626';

    $footer      = sanitize_hex_color(get_theme_mod('hayden_footer_color', '#020617')) ?: '#020617';
    $footer_text = sanitize_hex_color(get_theme_mod('hayden_footer_text_color', '#94a3b8')) ?: '#94a3b8';

    $widget_bg    = sanitize_hex_color(get_theme_mod('hayden_widget_bg_color', '#000000')) ?: '#000000';
    $widget_title = sanitize_hex_color(get_theme_mod('hayden_widget_title_color', '#f97316')) ?: '#f97316';
    $widget_text  = sanitize_hex_color(get_theme_mod('hayden_widget_text_color', '#ffffff')) ?: '#ffffff';
    $widget_link  = sanitize_hex_color(get_theme_mod('hayden_widget_link_color', '#f97316')) ?: '#f97316';

    $card_bg         = sanitize_hex_color(get_theme_mod('hayden_card_bg', '#000000')) ?: '#000000';
    $card_heading    = sanitize_hex_color(get_theme_mod('hayden_card_heading', '#f97316')) ?: '#f97316';
    $card_text       = sanitize_hex_color(get_theme_mod('hayden_card_text', '#ffffff')) ?: '#ffffff';
    $card_text_muted = sanitize_hex_color(get_theme_mod('hayden_card_text_muted', '#e5e5e5')) ?: '#e5e5e5';

    $nav_link         = sanitize_hex_color(get_theme_mod('hayden_nav_link_color', '#111111')) ?: '#111111';
    $nav_link_hover   = sanitize_hex_color(get_theme_mod('hayden_nav_link_hover_color', '#f97316')) ?: '#f97316';
    $nav_sub_bg       = sanitize_hex_color(get_theme_mod('hayden_nav_sub_bg_color', '#020617')) ?: '#020617';
    $nav_sub_link     = sanitize_hex_color(get_theme_mod('hayden_nav_sub_link_color', '#f97316')) ?: '#f97316';
    $nav_sub_hover_bg = sanitize_hex_color(get_theme_mod('hayden_nav_sub_hover_bg_color', '#3b1d08')) ?: '#3b1d08';

    $footer_widget_title = sanitize_hex_color(get_theme_mod('hayden_footer_widget_title_color', $widget_title)) ?: $widget_title;
    $footer_widget_text  = sanitize_hex_color(get_theme_mod('hayden_footer_widget_text_color', $widget_text)) ?: $widget_text;
    $footer_widget_link  = sanitize_hex_color(get_theme_mod('hayden_footer_widget_link_color', $widget_link)) ?: $widget_link;

    $logo_height     = absint(get_theme_mod('hayden_logo_max_height', 80));
    $container_width = absint(get_theme_mod('hayden_container_width', 1120));

    // Nav toggle contrast from surface
    $surface_hex = ltrim($surface, '#');
    $nav_toggle  = '#111111';
    if (strlen($surface_hex) === 6) {
        $rr = hexdec(substr($surface_hex, 0, 2));
        $gg = hexdec(substr($surface_hex, 2, 2));
        $bb = hexdec(substr($surface_hex, 4, 2));
        $brightness = ($rr * 299 + $gg * 587 + $bb * 114) / 1000;
        $nav_toggle = $brightness > 150 ? '#111111' : '#ffffff';
    }

    // Spacing
    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');
    $spacing_presets = [
        'compact'     => ['mobile' => '1.75rem', 'desktop' => '3rem'],
        'comfortable' => ['mobile' => '2.5rem',  'desktop' => '4rem'],
        'spacious'    => ['mobile' => '3.5rem',  'desktop' => '6rem'],
    ];
    $spacing = $spacing_presets[$spacing_choice] ?? $spacing_presets['comfortable'];

    // Type scale vars mapping
    $tw_scale = [
        'text-sm'   => 'var(--text-sm)',
        'text-base' => 'var(--text-base)',
        'text-lg'   => 'var(--text-lg)',
        'text-xl'   => 'var(--text-xl)',
        'text-2xl'  => 'var(--text-2xl)',
        'text-3xl'  => 'var(--text-3xl)',
        'text-4xl'  => 'var(--text-4xl)',
    ];
    $tw_keys = array_keys($tw_scale);

    $mobile_variant = function (string $key) use ($tw_keys, $tw_scale): string {
        $index = array_search($key, $tw_keys, true);
        if ($index === false) return $tw_scale['text-base'];
        $mobile_key = $tw_keys[max(0, $index - 1)];
        return $tw_scale[$mobile_key] ?? $tw_scale['text-base'];
    };

    $body_size_key = get_theme_mod('hayden_body_font_size', 'text-lg');
    $h1_size_key   = get_theme_mod('hayden_h1_font_size', 'text-4xl');
    $h2_size_key   = get_theme_mod('hayden_h2_font_size', 'text-3xl');
    $h3_size_key   = get_theme_mod('hayden_h3_font_size', 'text-2xl');

    $body_desktop = $tw_scale[$body_size_key] ?? 'var(--text-lg)';
    $body_mobile  = $mobile_variant($body_size_key);

    $h1_desktop = $tw_scale[$h1_size_key] ?? 'var(--text-4xl)';
    $h2_desktop = $tw_scale[$h2_size_key] ?? 'var(--text-3xl)';
    $h3_desktop = $tw_scale[$h3_size_key] ?? 'var(--text-2xl)';

    $h1_mobile = $mobile_variant($h1_size_key);
    $h2_mobile = $mobile_variant($h2_size_key);
    $h3_mobile = $mobile_variant($h3_size_key);

    ?>
    <style id="hayden-theme-vars">
      :root {
        --color-primary: <?php echo esc_html($primary); ?>;
        --color-surface: <?php echo esc_html($surface); ?>;
        --color-surface-soft: <?php echo esc_html($widget_bg); ?>;

        --color-headings: <?php echo esc_html($headings); ?>;
        --color-body: <?php echo esc_html($body_text); ?>;
        --color-body-muted: <?php echo esc_html($body_muted); ?>;

        --color-footer: <?php echo esc_html($footer); ?>;
        --color-footer-text: <?php echo esc_html($footer_text); ?>;

        --color-widget-bg: <?php echo esc_html($widget_bg); ?>;
        --color-widget-heading: <?php echo esc_html($widget_title); ?>;
        --color-widget-text: <?php echo esc_html($widget_text); ?>;
        --color-widget-link: <?php echo esc_html($widget_link); ?>;

        --color-footer-widget-heading: <?php echo esc_html($footer_widget_title); ?>;
        --color-footer-widget-text: <?php echo esc_html($footer_widget_text); ?>;
        --color-footer-widget-link: <?php echo esc_html($footer_widget_link); ?>;

        --card-bg: <?php echo esc_html($card_bg); ?>;
        --card-heading: <?php echo esc_html($card_heading); ?>;
        --card-text: <?php echo esc_html($card_text); ?>;
        --card-text-muted: <?php echo esc_html($card_text_muted); ?>;

        --color-nav-link: <?php echo esc_html($nav_link); ?>;
        --color-nav-link-hover: <?php echo esc_html($nav_link_hover); ?>;
        --color-nav-sub-bg: <?php echo esc_html($nav_sub_bg); ?>;
        --color-nav-sub-link: <?php echo esc_html($nav_sub_link); ?>;
        --color-nav-sub-hover-bg: <?php echo esc_html($nav_sub_hover_bg); ?>;
        --color-nav-toggle: <?php echo esc_html($nav_toggle); ?>;

        --site-max-width: <?php echo (int) $container_width; ?>px;
        --site-logo-max-height: <?php echo (int) $logo_height; ?>px;

        --body-font-size-mobile: <?php echo esc_html($body_mobile); ?>;
        --body-font-size-desktop: <?php echo esc_html($body_desktop); ?>;
        --body-font-size: <?php echo esc_html($body_desktop); ?>;

        --h1-font-size-mobile: <?php echo esc_html($h1_mobile); ?>;
        --h1-font-size-desktop: <?php echo esc_html($h1_desktop); ?>;

        --h2-font-size-mobile: <?php echo esc_html($h2_mobile); ?>;
        --h2-font-size-desktop: <?php echo esc_html($h2_desktop); ?>;

        --h3-font-size-mobile: <?php echo esc_html($h3_mobile); ?>;
        --h3-font-size-desktop: <?php echo esc_html($h3_desktop); ?>;

        --section-space-mobile: <?php echo esc_html($spacing['mobile']); ?>;
        --section-space-desktop: <?php echo esc_html($spacing['desktop']); ?>;

        /* Tailwind v4 radius tokens (rounded-2xl / rounded-full etc.) */
        --radius-sm: <?php echo esc_html($rad['sm']); ?>;
        --radius-md: <?php echo esc_html($rad['md']); ?>;
        --radius-lg: <?php echo esc_html($rad['lg']); ?>;
        --radius-xl: <?php echo esc_html($rad['xl']); ?>;
        --radius-2xl: <?php echo esc_html($rad['2xl']); ?>;
        --radius-full: <?php echo esc_html($rad['full']); ?>;

        /* Legacy alias used by your theme CSS (keep for compatibility) */
        --radius-pill: <?php echo esc_html($rad['full']); ?>;
      }

      .custom-logo,
      .site-logo img,
      .site-branding img {
        max-height: var(--site-logo-max-height);
        height: auto;
        width: auto;
      }
    </style>
    <?php
}, 999);

/**
 * ------------------------------------------------------------
 * Custom fonts in frontend + admin
 * ------------------------------------------------------------
 */
add_action('wp_head', function () {
    $css = hayden_get_custom_font_css();
    if ($css) {
        echo '<style id="hayden-custom-fonts">' . $css . '</style>';
    }
}, 50);

add_action('admin_head', function () {
    $css = hayden_get_custom_font_css();
    if ($css) {
        echo '<style id="hayden-custom-fonts-admin">' . $css . '</style>';
    }
}, 50);

/**
 * ------------------------------------------------------------
 * Customizer controls styling + controls-frame JS
 * ------------------------------------------------------------
 */
add_action('customize_controls_enqueue_scripts', function () {

    wp_enqueue_style(
        'hayden-customizer-style',
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    $primary = get_theme_mod('hayden_primary_color', '#f97316');
    wp_add_inline_style('hayden-customizer-style', sprintf(':root { --color-primary: %s; }', esc_attr($primary)));

    $controls_css = <<<CSS
#customize-controls .customize-control .customize-inside-control-row { color:#fff !important; }
#customize-controls .customize-control .description { color:#fafafa !important; }
CSS;
    wp_add_inline_style('hayden-customizer-style', $controls_css);

    $js = <<<JS
(function(api) {
  function updateWidthValue(val) {
    var el = document.getElementById('hayden-container-width-value');
    if (el) el.textContent = val + 'px';
  }

  api('hayden_container_width', function(setting) {
    updateWidthValue(setting.get());
    setting.bind(function(newVal) { updateWidthValue(newVal); });
  });

  document.addEventListener('input', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('hayden-container-width-range')) {
      updateWidthValue(e.target.value);
    }
  });

  api('hayden_primary_color', function(setting) {
    function applyPrimary(val) {
      if (!val) val = '#f97316';
      var styleEl = document.getElementById('hayden-customizer-primary');
      if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'hayden-customizer-primary';
        document.head.appendChild(styleEl);
      }
      styleEl.textContent = ':root { --color-primary: ' + val + '; }';
    }
    applyPrimary(setting.get());
    setting.bind(applyPrimary);
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-controls', $js);
});

add_action('customize_controls_print_styles', function () {
    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    ?>
    <style id="hayden-customizer-primary">
      :root { --color-primary: <?php echo esc_html($primary); ?>; }
    </style>
    <?php
});

/**
 * ------------------------------------------------------------
 * Live preview script – updates CSS vars in the PREVIEW frame
 * Includes radius + spacing live updates (and Tailwind radius vars)
 * ------------------------------------------------------------
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('customize-preview');

    $js = <<<JS
(function(api) {
  if (!api) return;

  var colorMap = {
    hayden_primary_color: '--color-primary',
    hayden_surface_color: '--color-surface',
    hayden_heading_color: '--color-headings',
    hayden_body_color: '--color-body',
    hayden_body_muted_color: '--color-body-muted',

    hayden_footer_color: '--color-footer',
    hayden_footer_text_color: '--color-footer-text',

    hayden_widget_bg_color: '--color-widget-bg',
    hayden_widget_title_color: '--color-widget-heading',
    hayden_widget_text_color: '--color-widget-text',
    hayden_widget_link_color: '--color-widget-link',

    hayden_footer_widget_title_color: '--color-footer-widget-heading',
    hayden_footer_widget_text_color: '--color-footer-widget-text',
    hayden_footer_widget_link_color: '--color-footer-widget-link',

    hayden_card_bg: '--card-bg',
    hayden_card_heading: '--card-heading',
    hayden_card_text: '--card-text',
    hayden_card_text_muted: '--card-text-muted',

    hayden_nav_link_color: '--color-nav-link',
    hayden_nav_link_hover_color: '--color-nav-link-hover',
    hayden_nav_sub_bg_color: '--color-nav-sub-bg',
    hayden_nav_sub_link_color: '--color-nav-sub-link',
    hayden_nav_sub_hover_bg_color: '--color-nav-sub-hover-bg'
  };

  function setCssVar(name, value) {
    if (!name) return;
    document.documentElement.style.setProperty(name, value);
  }

  function updateNavToggleFromSurface(hex) {
    if (!hex) return;
    hex = ('' + hex).replace('#', '');
    if (hex.length !== 6) return;

    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);

    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    var toggleColor = brightness > 150 ? '#111111' : '#ffffff';
    setCssVar('--color-nav-toggle', toggleColor);
  }

  function applyRadiusPreset(style) {
    var presets = {
      sharp: { sm:'0rem', md:'0rem', lg:'0rem', xl:'0rem', '2xl':'0rem', full:'0rem' },
      soft:  { sm:'0.125rem', md:'0.375rem', lg:'0.5rem', xl:'0.75rem', '2xl':'1rem', full:'9999px' },
      round: { sm:'0.375rem', md:'0.5rem', lg:'0.75rem', xl:'1rem', '2xl':'1.25rem', full:'9999px' }
    };

    var p = presets[style] || presets.soft;

    // Tailwind v4 tokens:
    setCssVar('--radius-sm', p.sm);
    setCssVar('--radius-md', p.md);
    setCssVar('--radius-lg', p.lg);
    setCssVar('--radius-xl', p.xl);
    setCssVar('--radius-2xl', p['2xl']);
    setCssVar('--radius-full', p.full);

    // Legacy alias:
    setCssVar('--radius-pill', p.full);
  }

  function applySpacingPreset(choice) {
    var presets = {
      compact:     { mobile:'1.75rem', desktop:'3rem' },
      comfortable: { mobile:'2.5rem',  desktop:'4rem' },
      spacious:    { mobile:'3.5rem',  desktop:'6rem' }
    };
    var p = presets[choice] || presets.comfortable;
    setCssVar('--section-space-mobile', p.mobile);
    setCssVar('--section-space-desktop', p.desktop);
  }

  Object.keys(colorMap).forEach(function(settingId) {
    var cssVar = colorMap[settingId];

    api(settingId, function(setting) {
      var initial = setting.get();
      if (initial) {
        setCssVar(cssVar, initial);

        if (settingId === 'hayden_surface_color') updateNavToggleFromSurface(initial);
        if (settingId === 'hayden_widget_bg_color') setCssVar('--color-surface-soft', initial);
      }

      setting.bind(function(newVal) {
        if (!newVal) return;
        setCssVar(cssVar, newVal);

        if (settingId === 'hayden_surface_color') updateNavToggleFromSurface(newVal);
        if (settingId === 'hayden_widget_bg_color') setCssVar('--color-surface-soft', newVal);
      });
    });
  });

  api('hayden_radius_style', function(setting) {
    applyRadiusPreset(setting.get());
    setting.bind(applyRadiusPreset);
  });

  api('hayden_spacing_scale', function(setting) {
    applySpacingPreset(setting.get());
    setting.bind(applySpacingPreset);
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-preview', $js);
});

/**
 * ------------------------------------------------------------
 * Apply Customizer logo max height to the custom logo markup.
 * ------------------------------------------------------------
 */
add_filter('get_custom_logo', function ($html) {
    $height = absint(get_theme_mod('hayden_logo_max_height', 80));
    if (!$height || !$html) return $html;

    return preg_replace(
        '/<img([^>]+)>/',
        '<img$1 style="max-height:' . $height . 'px;height:auto;width:auto;">',
        $html,
        1
    );
});

/**
 * ------------------------------------------------------------
 * Editor: inject vars + fonts + spacing + Tailwind radius vars
 * ------------------------------------------------------------
 */
add_action('enqueue_block_editor_assets', function () {

    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    $surface = sanitize_hex_color(get_theme_mod('hayden_surface_color', '#FFFAF8')) ?: '#FFFAF8';

    $card_bg         = sanitize_hex_color(get_theme_mod('hayden_card_bg', '#000000')) ?: '#000000';
    $card_heading    = sanitize_hex_color(get_theme_mod('hayden_card_heading', '#f97316')) ?: '#f97316';
    $card_text       = sanitize_hex_color(get_theme_mod('hayden_card_text', '#ffffff')) ?: '#ffffff';
    $card_text_muted = sanitize_hex_color(get_theme_mod('hayden_card_text_muted', '#e5e5e5')) ?: '#e5e5e5';

    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');
    $spacing_presets = [
        'compact'     => ['mobile' => '1.75rem', 'desktop' => '3rem'],
        'comfortable' => ['mobile' => '2.5rem',  'desktop' => '4rem'],
        'spacious'    => ['mobile' => '3.5rem',  'desktop' => '6rem'],
    ];
    $spacing = $spacing_presets[$spacing_choice] ?? $spacing_presets['comfortable'];

    $radius_style = get_theme_mod('hayden_radius_style', 'soft');
    $radius_presets = [
        'sharp' => ['sm'=>'0rem','md'=>'0rem','lg'=>'0rem','xl'=>'0rem','2xl'=>'0rem','full'=>'0rem'],
        'soft'  => ['sm'=>'0.125rem','md'=>'0.375rem','lg'=>'0.5rem','xl'=>'0.75rem','2xl'=>'1rem','full'=>'9999px'],
        'round' => ['sm'=>'0.375rem','md'=>'0.5rem','lg'=>'0.75rem','xl'=>'1rem','2xl'=>'1.25rem','full'=>'9999px'],
    ];
    $rad = $radius_presets[$radius_style] ?? $radius_presets['soft'];

    $css = sprintf(
        ':root{--color-primary:%1$s;--color-surface:%2$s;--card-bg:%3$s;--card-heading:%4$s;--card-text:%5$s;--card-text-muted:%6$s;--section-space-mobile:%7$s;--section-space-desktop:%8$s;--radius-sm:%9$s;--radius-md:%10$s;--radius-lg:%11$s;--radius-xl:%12$s;--radius-2xl:%13$s;--radius-full:%14$s;--radius-pill:%14$s;}',
        esc_html($primary),
        esc_html($surface),
        esc_html($card_bg),
        esc_html($card_heading),
        esc_html($card_text),
        esc_html($card_text_muted),
        esc_html($spacing['mobile']),
        esc_html($spacing['desktop']),
        esc_html($rad['sm']),
        esc_html($rad['md']),
        esc_html($rad['lg']),
        esc_html($rad['xl']),
        esc_html($rad['2xl']),
        esc_html($rad['full'])
    );

    $font_css = hayden_get_custom_font_css();
    if ($font_css) {
        $css .= "\n" . $font_css;
        $css .= "
        .editor-styles-wrapper{font-family:var(--font-serif);}
        .editor-styles-wrapper p,
        .editor-styles-wrapper li{font-family:inherit;}
        .editor-styles-wrapper h1,
        .editor-styles-wrapper h2,
        .editor-styles-wrapper h3,
        .editor-styles-wrapper h4,
        .editor-styles-wrapper h5,
        .editor-styles-wrapper h6{font-family:var(--font-sans);}
        .edit-post-visual-editor__post-title-wrapper .editor-post-title__input{font-family:var(--font-sans);}
        ";
    }

    $css .= "
    .editor-styles-wrapper .wp-block-smart-hero-primary{margin-top:var(--section-space-mobile);margin-bottom:var(--section-space-mobile);}
    @media (min-width:768px){
      .editor-styles-wrapper .wp-block-smart-hero-primary{margin-top:var(--section-space-desktop);margin-bottom:var(--section-space-desktop);}
    }";

    if (trim($css) !== '') {
        wp_add_inline_style('wp-block-library', $css);
        wp_add_inline_style('wp-block-library-theme', $css);
        if (wp_style_is('sage/editor', 'registered')) {
            wp_add_inline_style('sage/editor', $css);
        }
    }
});
