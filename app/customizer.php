<?php

namespace App;

/**
 * ------------------------------------------------------------
 * THEME CUSTOMIZER
 * Panels, sections, settings, colour pickers and output CSS.
 * ------------------------------------------------------------
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {

    /**
     * MASTER PANEL
     */
    $wp_customize->add_panel('hayden_theme_panel', [
        'title'       => __('Theme Settings', 'hayden'),
        'description' => __('Global layout, footer, grid and colours.', 'hayden'),
        'priority'    => 10,
    ]);

    /**
     * ------------------------------------------------------------
     * HEADER LAYOUT SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_header_section', [
        'title'       => __('Header Layout', 'hayden'),
        'description' => __('Choose a layout style for the header.', 'hayden'),
        'priority'    => 20,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Header layout setting
    $wp_customize->add_setting('hayden_header_layout', [
        'default'           => 'default',
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            $allowed = ['default', 'logo-top', 'nav-center-cta'];
            return in_array($value, $allowed, true) ? $value : 'default';
        },
    ]);

    // Header layout control
    $wp_customize->add_control('hayden_header_layout_control', [
        'label'    => __('Header layout style', 'hayden'),
        'section'  => 'hayden_header_section',
        'settings' => 'hayden_header_layout',
        'type'     => 'select',
        'choices'  => [
            'default'        => __('Default – logo left, nav right', 'hayden'),
            'logo-top'       => __('Logo top, nav below', 'hayden'),
            'nav-center-cta' => __('Logo left, nav centre, CTA right', 'hayden'),
        ],
    ]);

    /**
     * CTA fields – only visible when layout = nav-center-cta
     */
    // CTA label
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
        'active_callback' => function ($control) {
            return $control->manager
                    ->get_setting('hayden_header_layout')
                    ->value() === 'nav-center-cta';
        },
    ]);

    // CTA URL
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
        'active_callback' => function ($control) {
            return $control->manager
                    ->get_setting('hayden_header_layout')
                    ->value() === 'nav-center-cta';
        },
    ]);

    /**
     * Logo max height (applies to all layouts)
     */
    $wp_customize->add_setting('hayden_logo_max_height', [
        'default'           => 80, // px
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('hayden_logo_max_height_control', [
        'label'       => __('Logo max height (px)', 'hayden'),
        'section'     => 'hayden_header_section',
        'settings'    => 'hayden_logo_max_height',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 40,
            'max'  => 200,
            'step' => 4,
        ],
        'description' => __('Controls the rendered logo height when a custom logo is set.', 'hayden'),
    ]);

    /**
     * ------------------------------------------------------------
     * LAYOUT / CONTAINER SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_layout_section', [
        'title'       => __('Layout & Container', 'hayden'),
        'description' => __('Control the maximum width of the main content container.', 'hayden'),
        'priority'    => 25,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Site container max width (px) → --site-max-width
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
            'description' => sprintf(
                '<span id="hayden-container-width-value">%dpx</span>',
                $current_width
            ),
        ]
    ));

    /**
     * ------------------------------------------------------------
     * GLOBAL SPACING SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_spacing_section', [
        'title'       => __('Global Spacing', 'hayden'),
        'description' => __('Controls vertical spacing between sections and blocks.', 'hayden'),
        'priority'    => 28,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Global spacing scale: compact / comfortable / spacious
    $wp_customize->add_setting('hayden_spacing_scale', [
        'default'           => 'comfortable',
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            $allowed = ['compact', 'comfortable', 'spacious'];
            return in_array($value, $allowed, true) ? $value : 'comfortable';
        },
    ]);

    $wp_customize->add_control('hayden_spacing_scale_control', [
        'label'       => __('Vertical spacing scale', 'hayden'),
        'section'     => 'hayden_spacing_section',
        'settings'    => 'hayden_spacing_scale',
        'type'        => 'select',
        'choices'     => [
            'compact'      => __('Compact', 'hayden'),
            'comfortable'  => __('Comfortable (default)', 'hayden'),
            'spacious'     => __('Spacious', 'hayden'),
        ],
        'description' => __('Affects global section/block spacing via CSS variables.', 'hayden'),
    ]);

    /**
     * ------------------------------------------------------------
     * TYPOGRAPHY SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_typography_section', [
        'title'       => __('Typography', 'hayden'),
        'description' => __('Upload custom fonts. Leave empty to use the default theme fonts.', 'hayden'),
        'panel'       => 'hayden_theme_panel',
        'priority'    => 40,
    ]);

    // Heading (serif) font file
    $wp_customize->add_setting('hayden_font_serif_file', [
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            return absint($value);
        },
    ]);

    $wp_customize->add_control(new \WP_Customize_Media_Control(
        $wp_customize,
        'hayden_font_serif_file',
        [
            'label'       => __('Heading font (serif)', 'hayden'),
            'description' => __('Upload a WOFF2/WOFF/TTF file. This will replace the default --font-serif stack.', 'hayden'),
            'section'     => 'hayden_typography_section',
            'mime_type'   => '',
        ]
    ));

    // Body (sans) font file
    $wp_customize->add_setting('hayden_font_sans_file', [
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            return absint($value);
        },
    ]);

    $wp_customize->add_control(new \WP_Customize_Media_Control(
        $wp_customize,
        'hayden_font_sans_file',
        [
            'label'       => __('Body font (sans-serif)', 'hayden'),
            'description' => __('Upload a WOFF2/WOFF/TTF file. This will replace the default --font-sans stack.', 'hayden'),
            'section'     => 'hayden_typography_section',
            'mime_type'   => '',
        ]
    ));

    // Typography: font sizes (Tailwind scale)
    $tw_font_sizes = [
        'text-sm'   => __('Small (Tailwind text-sm)', 'hayden'),
        'text-base' => __('Base (Tailwind text-base)', 'hayden'),
        'text-lg'   => __('Large (Tailwind text-lg)', 'hayden'),
        'text-xl'   => __('XL (Tailwind text-xl)', 'hayden'),
        'text-2xl'  => __('2XL (Tailwind text-2xl)', 'hayden'),
        'text-3xl'  => __('3XL (Tailwind text-3xl)', 'hayden'),
        'text-4xl'  => __('4XL (Tailwind text-4xl)', 'hayden'),
    ];

    // Body font size
    $wp_customize->add_setting('hayden_body_font_size', [
        'default'           => 'text-lg',
        'transport'         => 'refresh',
        'sanitize_callback' => 'App\\hayden_sanitize_font_scale',
    ]);

    $wp_customize->add_control('hayden_body_font_size_control', [
        'label'       => __('Body font size', 'hayden'),
        'section'     => 'hayden_typography_section',
        'settings'    => 'hayden_body_font_size',
        'type'        => 'select',
        'choices'     => $tw_font_sizes,
        'description' => __('Based on Tailwind font-size scale.', 'hayden'),
    ]);

    // H1 size
    $wp_customize->add_setting('hayden_h1_font_size', [
        'default'           => 'text-4xl',
        'transport'         => 'refresh',
        'sanitize_callback' => 'App\\hayden_sanitize_font_scale',
    ]);

    $wp_customize->add_control('hayden_h1_font_size_control', [
        'label'    => __('H1 font size', 'hayden'),
        'section'  => 'hayden_typography_section',
        'settings' => 'hayden_h1_font_size',
        'type'     => 'select',
        'choices'  => $tw_font_sizes,
    ]);

    // H2 size
    $wp_customize->add_setting('hayden_h2_font_size', [
        'default'           => 'text-3xl',
        'transport'         => 'refresh',
        'sanitize_callback' => 'App\\hayden_sanitize_font_scale',
    ]);

    $wp_customize->add_control('hayden_h2_font_size_control', [
        'label'    => __('H2 font size', 'hayden'),
        'section'  => 'hayden_typography_section',
        'settings' => 'hayden_h2_font_size',
        'type'     => 'select',
        'choices'  => $tw_font_sizes,
    ]);

    // H3 size
    $wp_customize->add_setting('hayden_h3_font_size', [
        'default'           => 'text-2xl',
        'transport'         => 'refresh',
        'sanitize_callback' => 'App\\hayden_sanitize_font_scale',
    ]);

    $wp_customize->add_control('hayden_h3_font_size_control', [
        'label'    => __('H3 font size', 'hayden'),
        'section'  => 'hayden_typography_section',
        'settings' => 'hayden_h3_font_size',
        'type'     => 'select',
        'choices'  => $tw_font_sizes,
    ]);

    /**
     * ------------------------------------------------------------
     * THEME COLOUR SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_color_section', [
        'title'       => __('Theme Colours', 'hayden'),
        'description' => '',
        'priority'    => 30,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Intro text: "Control global brand colours." in white
    $wp_customize->add_setting('hayden_color_intro', [
        'sanitize_callback' => '__return_null',
    ]);

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

    /**
     * Global colours
     */
    // Primary colour → --color-primary
    $wp_customize->add_setting('hayden_primary_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_primary_color_control',
        [
            'label'    => __('Primary Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_primary_color',
        ]
    ));

    // Background colour → --color-surface
    $wp_customize->add_setting('hayden_surface_color', [
        'default'           => '#FFFAF8',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_surface_color_control',
        [
            'label'    => __('Background Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_surface_color',
        ]
    ));

    // Heading colour → --color-headings
    $wp_customize->add_setting('hayden_heading_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_heading_color_control',
        [
            'label'    => __('Heading Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_heading_color',
        ]
    ));

    // Body text colour → --color-body
    $wp_customize->add_setting('hayden_body_color', [
        'default'           => '#111111',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_body_color_control',
        [
            'label'    => __('Body Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_body_color',
        ]
    ));

    // Body muted text colour → --color-body-muted
    $wp_customize->add_setting('hayden_body_muted_color', [
        'default'           => '#262626',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_body_muted_color_control',
        [
            'label'    => __('Body Muted Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_body_muted_color',
        ]
    ));

    // Footer background colour → --color-footer
    $wp_customize->add_setting('hayden_footer_color', [
        'default'           => '#020617',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_footer_color_control',
        [
            'label'    => __('Footer Background Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_footer_color',
        ]
    ));

    // Footer text colour → --color-footer-text
    $wp_customize->add_setting('hayden_footer_text_color', [
        'default'           => '#94a3b8',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_footer_text_color_control',
        [
            'label'    => __('Footer Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_footer_text_color',
        ]
    ));

    // Widget background → --color-surface-soft / --color-widget-bg
    $wp_customize->add_setting('hayden_widget_bg_color', [
        'default'           => '#000000',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_widget_bg_color_control',
        [
            'label'    => __('Widget Background Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_widget_bg_color',
        ]
    ));

    /**
     * Sub-heading: Widget colours
     */
    $wp_customize->add_setting('hayden_color_heading_widgets', [
        'sanitize_callback' => '__return_null',
    ]);

    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_widgets_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_widgets',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                           . '<h3 class="hayden-customizer-subheading" '
                           . 'style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                           . esc_html__('Widget colours', 'hayden')
                           . '</h3>',
        ]
    ));

    // Widget title colour → --color-widget-heading
    $wp_customize->add_setting('hayden_widget_title_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_widget_title_color_control',
        [
            'label'    => __('Widget Title Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_widget_title_color',
        ]
    ));

    // Widget text colour → --color-widget-text
    $wp_customize->add_setting('hayden_widget_text_color', [
        'default'           => '#ffffff',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_widget_text_color_control',
        [
            'label'    => __('Widget Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_widget_text_color',
        ]
    ));

    // Widget link colour → --color-widget-link
    $wp_customize->add_setting('hayden_widget_link_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_widget_link_color_control',
        [
            'label'    => __('Widget Link Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_widget_link_color',
        ]
    ));

    /**
     * Sub-heading: Footer widget colours
     */
    $wp_customize->add_setting('hayden_color_heading_footer_widgets', [
        'sanitize_callback' => '__return_null',
    ]);

    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_footer_widgets_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_footer_widgets',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                           . '<h3 class="hayden-customizer-subheading" '
                           . 'style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                           . esc_html__('Footer widget colours', 'hayden')
                           . '</h3>',
        ]
    ));

    // Footer widget title colour → --color-footer-widget-heading
    $wp_customize->add_setting('hayden_footer_widget_title_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_footer_widget_title_color_control',
        [
            'label'    => __('Footer Widget Title Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_footer_widget_title_color',
        ]
    ));

    // Footer widget text colour → --color-footer-widget-text
    $wp_customize->add_setting('hayden_footer_widget_text_color', [
        'default'           => '#ffffff',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_footer_widget_text_color_control',
        [
            'label'    => __('Footer Widget Text Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_footer_widget_text_color',
        ]
    ));

    // Footer widget link colour → --color-footer-widget-link
    $wp_customize->add_setting('hayden_footer_widget_link_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_footer_widget_link_color_control',
        [
            'label'    => __('Footer Widget Link Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_footer_widget_link_color',
        ]
    ));

    /**
     * Sub-heading: Navigation colours
     */
    $wp_customize->add_setting('hayden_color_heading_nav', [
        'sanitize_callback' => '__return_null',
    ]);

    $wp_customize->add_control(new \WP_Customize_Control(
        $wp_customize,
        'hayden_color_heading_nav_control',
        [
            'section'     => 'hayden_color_section',
            'settings'    => 'hayden_color_heading_nav',
            'type'        => 'custom',
            'description' => '<hr style="margin:10px 0;border-color:rgba(255,255,255,0.2);">'
                           . '<h3 class="hayden-customizer-subheading" '
                           . 'style="margin:4px 0 4px;font-weight:600;color:#ffffff;">'
                           . esc_html__('Navigation colours', 'hayden')
                           . '</h3>',
        ]
    ));

    /**
     * Navigation colours
     */
    // Top-level (parent) link colour → --color-nav-link
    $wp_customize->add_setting('hayden_nav_link_color', [
        'default'           => '#111111',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_nav_link_color_control',
        [
            'label'    => __('Nav Parent Link Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_nav_link_color',
        ]
    ));

    // Top-level (parent) hover / active colour → --color-nav-link-hover
    $wp_customize->add_setting('hayden_nav_link_hover_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_nav_link_hover_color_control',
        [
            'label'    => __('Nav Link Hover/Active Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_nav_link_hover_color',
        ]
    ));

    // Dropdown panel background → --color-nav-sub-bg
    $wp_customize->add_setting('hayden_nav_sub_bg_color', [
        'default'           => '#020617',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_nav_sub_bg_color_control',
        [
            'label'    => __('Dropdown Background Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_nav_sub_bg_color',
        ]
    ));

    // Dropdown link colour → --color-nav-sub-link
    $wp_customize->add_setting('hayden_nav_sub_link_color', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_nav_sub_link_color_control',
        [
            'label'    => __('Dropdown Link Colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_nav_sub_link_color',
        ]
    ));

    // Dropdown link hover background → --color-nav-sub-hover-bg
    $wp_customize->add_setting('hayden_nav_sub_hover_bg_color', [
        'default'           => '#3b1d08',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_nav_sub_hover_bg_color_control',
        [
            'label'    => __('Dropdown Link Hover Background', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_nav_sub_hover_bg_color',
        ]
    ));

    /**
     * ------------------------------------------------------------
     * CONTENT CARDS SECTION (blog / portfolio cards)
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_cards_section', [
        'title'       => __('Content Cards', 'hayden'),
        'description' => __('Colours for blog, portfolio and other content cards.', 'hayden'),
        'priority'    => 32,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Card background → --card-bg
    $wp_customize->add_setting('hayden_card_bg', [
        'default'           => '#000000',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_card_bg_control',
        [
            'label'    => __('Card Background Colour', 'hayden'),
            'section'  => 'hayden_cards_section',
            'settings' => 'hayden_card_bg',
        ]
    ));

    // Card heading colour → --card-heading
    $wp_customize->add_setting('hayden_card_heading', [
        'default'           => '#f97316',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_card_heading_control',
        [
            'label'    => __('Card Heading Colour', 'hayden'),
            'section'  => 'hayden_cards_section',
            'settings' => 'hayden_card_heading',
        ]
    ));

    // Card text colour → --card-text
    $wp_customize->add_setting('hayden_card_text', [
        'default'           => '#ffffff',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_card_text_control',
        [
            'label'    => __('Card Text Colour', 'hayden'),
            'section'  => 'hayden_cards_section',
            'settings' => 'hayden_card_text',
        ]
    ));

    // Card muted text (excerpt) → --card-text-muted
    $wp_customize->add_setting('hayden_card_text_muted', [
        'default'           => '#e5e5e5',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_card_text_muted_control',
        [
            'label'    => __('Card Muted Text Colour', 'hayden'),
            'section'  => 'hayden_cards_section',
            'settings' => 'hayden_card_text_muted',
        ]
    ));

    /**
     * ------------------------------------------------------------
     * GRID SETTINGS SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('grid_display_section', [
        'title'       => __('Grid Display', 'hayden'),
        'description' => __('Controls how many posts/projects show initially.', 'hayden'),
        'priority'    => 35,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Projects initial items
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

    // Blog initial items
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
     * ------------------------------------------------------------
     * FOOTER LAYOUT SECTION (number of widget columns)
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_footer_section', [
        'title'       => __('Footer Layout', 'hayden'),
        'description' => __('Footer layout and number of widget columns.', 'hayden'),
        'priority'    => 40,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Number of footer columns (1–4)
    $wp_customize->add_setting('hayden_footer_columns', [
        'default'           => 3,
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            $value = (int) $value;
            return ($value >= 1 && $value <= 4) ? $value : 3;
        },
    ]);

    $wp_customize->add_control('hayden_footer_columns_control', [
        'label'    => __('Footer columns', 'hayden'),
        'section'  => 'hayden_footer_section',
        'settings' => 'hayden_footer_columns',
        'type'     => 'select',
        'choices'  => [
            1 => __('1 column', 'hayden'),
            2 => __('2 columns', 'hayden'),
            3 => __('3 columns', 'hayden'),
            4 => __('4 columns', 'hayden'),
        ],
    ]);

    /**
     * ------------------------------------------------------------
     * MOVE FOOTER WIDGET AREAS INTO THE PANEL
     * ------------------------------------------------------------
     */
    $footer_sidebars = [
        'sidebar-footer-1',
        'sidebar-footer-2',
        'sidebar-footer-3',
        'sidebar-footer-4',
    ];

    $priority = 50;

    foreach ($footer_sidebars as $index => $sidebar_id) {
        $section_id = 'sidebar-widgets-' . $sidebar_id;

        if ($section = $wp_customize->get_section($section_id)) {
            $section->panel    = 'hayden_theme_panel';
            $section->priority = $priority + $index;
            $section->title    = sprintf(__('Footer Column %d Widgets', 'hayden'), $index + 1);
        }
    }












    /**
     * ------------------------------------------------------------
     * PANEL FOOTER – CUSTOM HTML (info / link / image)
     * ------------------------------------------------------------
     */
$wp_customize->add_section('hayden_panel_footer_section', [
    'title'       => __('Need Help?', 'hayden'),
    'description' => '',
    'priority'    => 200, // after all the other sections
    'panel'       => 'hayden_theme_panel',
]);


    // Dummy setting just so we can attach a custom control
    $wp_customize->add_setting('hayden_panel_footer_note', [
        'sanitize_callback' => '__return_null',
    ]);

    $image_url = esc_url( get_theme_file_uri('resources/images/customizer-footer-logo.svg') );
    $site_url  = esc_url( 'https://wp.bbi.co.uk' );

$html  = '<div class="hayden-customizer-panel-footer" style="margin:16px 0 24px;padding:12px 14px;border-radius:8px;background:#020617;border:1px solid rgba(148,163,184,0.4);color:#e5e7eb;font-size:13px;line-height:1.5;">';

$html .= '  <div style="display:flex;align-items:center;gap:10px;">';


$html .= '    <div style="flex:1 1 auto;">';
$html .= '      <strong style="display:block;margin-bottom:2px;font-weight:600;">Hayden Sage Starter</strong>';
$html .= '      <span style="display:block;margin-bottom:4px;opacity:.9;">Need help or want a custom build based on this theme?</span>';
$html .= '      <a href="' . $site_url . '" target="_blank" rel="noopener" style="color:#f97316;text-decoration:none;">Visit bbi.co.uk →</a>';
$html .= '    </div>';
$html .= '  </div>'; // end logo/text row

// --------------------------------------------
// BUTTONS SECTION
// --------------------------------------------
$html .= '  <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px;">';

$html .= '    <a href="https://wp.bbi.co.uk/blog"
                  target="_blank"
                  rel="noopener"
                  style="display:block;width:100%;text-align:center;padding:10px 14px;
                         background:#1e293b;color:#fff;border-radius:6px;text-decoration:none;
                         font-weight:600;">
                    Blog
                </a>';

$html .= '    <a href="https://wp.bbi.co.uk/contact"
                  target="_blank"
                  rel="noopener"
                  style="display:block;width:100%;text-align:center;padding:10px 14px;
                         background:#f97316;color:#000;border-radius:6px;text-decoration:none;
                         font-weight:600;">
                    Contact Us
                </a>';

$html .= '  </div>'; // end buttons column

$html .= '</div>'; // end wrapper




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










add_action('enqueue_block_editor_assets', function () {
    // Colours
    $primary    = sanitize_hex_color( get_theme_mod('hayden_primary_color', '#f97316') ) ?: '#f97316';
    $surface    = sanitize_hex_color( get_theme_mod('hayden_surface_color', '#FFFAF8') ) ?: '#FFFAF8';

    // Card colours (same defaults as wp_head)
    $card_bg         = sanitize_hex_color( get_theme_mod('hayden_card_bg', '#000000') ) ?: '#000000';
    $card_heading    = sanitize_hex_color( get_theme_mod('hayden_card_heading', '#f97316') ) ?: '#f97316';
    $card_text       = sanitize_hex_color( get_theme_mod('hayden_card_text', '#ffffff') ) ?: '#ffffff';
    $card_text_muted = sanitize_hex_color( get_theme_mod('hayden_card_text_muted', '#e5e5e5') ) ?: '#e5e5e5';

    // Global spacing scale (same logic as wp_head)
    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');

    $spacing_presets = [
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

    $spacing = $spacing_presets[ $spacing_choice ] ?? $spacing_presets['comfortable'];

    $css = sprintf(
        ':root {' .
            '--color-primary:%1$s;' .
            '--color-surface:%2$s;' .
            '--card-bg:%3$s;' .
            '--card-heading:%4$s;' .
            '--card-text:%5$s;' .
            '--card-text-muted:%6$s;' .
            '--section-space-mobile:%7$s;' .
            '--section-space-desktop:%8$s;' .
        '}',
        esc_html( $primary ),
        esc_html( $surface ),
        esc_html( $card_bg ),
        esc_html( $card_heading ),
        esc_html( $card_text ),
        esc_html( $card_text_muted ),
        esc_html( $spacing['mobile'] ),
        esc_html( $spacing['desktop'] )
    );

    wp_add_inline_style( 'wp-block-library', $css );
});



















/**
 * ------------------------------------------------------------
 * OUTPUT DYNAMIC CSS VARIABLES TO THE FRONTEND
 * ------------------------------------------------------------
 */
add_action('wp_head', function () {

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

    // Card colours
    $card_bg         = sanitize_hex_color(get_theme_mod('hayden_card_bg', '#000000')) ?: '#000000';
    $card_heading    = sanitize_hex_color(get_theme_mod('hayden_card_heading', '#f97316')) ?: '#f97316';
    $card_text       = sanitize_hex_color(get_theme_mod('hayden_card_text', '#ffffff')) ?: '#ffffff';
    $card_text_muted = sanitize_hex_color(get_theme_mod('hayden_card_text_muted', '#e5e5e5')) ?: '#e5e5e5';

    $nav_link         = sanitize_hex_color(get_theme_mod('hayden_nav_link_color', '#111111')) ?: '#111111';
    $nav_link_hover   = sanitize_hex_color(get_theme_mod('hayden_nav_link_hover_color', '#f97316')) ?: '#f97316';
    $nav_sub_bg       = sanitize_hex_color(get_theme_mod('hayden_nav_sub_bg_color', '#020617')) ?: '#020617';
    $nav_sub_link     = sanitize_hex_color(get_theme_mod('hayden_nav_sub_link_color', '#f97316')) ?: '#f97316';
    $nav_sub_hover_bg = sanitize_hex_color(get_theme_mod('hayden_nav_sub_hover_bg_color', '#3b1d08')) ?: '#3b1d08';

    // Footer widget colours
    $footer_widget_title = sanitize_hex_color(get_theme_mod('hayden_footer_widget_title_color', $widget_title)) ?: $widget_title;
    $footer_widget_text  = sanitize_hex_color(get_theme_mod('hayden_footer_widget_text_color', $widget_text)) ?: $widget_text;
    $footer_widget_link  = sanitize_hex_color(get_theme_mod('hayden_footer_widget_link_color', $widget_link)) ?: $widget_link;

    $logo_height = absint(get_theme_mod('hayden_logo_max_height', 80));

    // Work out a good contrast colour for the mobile toggle based on the background
    $surface_hex = ltrim($surface, '#');
    $nav_toggle  = '#111111'; // fallback

    if (strlen($surface_hex) === 6) {
        $r = hexdec(substr($surface_hex, 0, 2));
        $g = hexdec(substr($surface_hex, 2, 2));
        $b = hexdec(substr($surface_hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        $nav_toggle = $brightness > 150 ? '#111111' : '#ffffff';
    }

    // Site container max width (px)
    $container_width = absint(get_theme_mod('hayden_container_width', 1120));

    // Tailwind font-size scale → CSS var references
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

    // Given a chosen key (desktop), pick one step smaller for mobile
    $hayden_font_mobile = function (string $key) use ($tw_keys, $tw_scale): string {
        $index = array_search($key, $tw_keys, true);

        if ($index === false) {
            return $tw_scale['text-base']; // sensible fallback
        }

        $mobile_key = $tw_keys[max(0, $index - 1)];

        return $tw_scale[$mobile_key];
    };

    // Values saved from Customizer (desktop “target” sizes)
    $body_size_key = get_theme_mod('hayden_body_font_size', 'text-lg');
    $h1_size_key   = get_theme_mod('hayden_h1_font_size', 'text-4xl');
    $h2_size_key   = get_theme_mod('hayden_h2_font_size', 'text-3xl');
    $h3_size_key   = get_theme_mod('hayden_h3_font_size', 'text-2xl');

    // Body desktop + mobile
    $body_desktop = $tw_scale[$body_size_key] ?? 'var(--text-lg)';
    $body_mobile  = $hayden_font_mobile($body_size_key);

    // Headings desktop sizes
    $h1_desktop = $tw_scale[$h1_size_key] ?? 'var(--text-4xl)';
    $h2_desktop = $tw_scale[$h2_size_key] ?? 'var(--text-3xl)';
    $h3_desktop = $tw_scale[$h3_size_key] ?? 'var(--text-2xl)';

    // Headings mobile sizes (one step smaller)
    $h1_mobile = $hayden_font_mobile($h1_size_key);
    $h2_mobile = $hayden_font_mobile($h2_size_key);
    $h3_mobile = $hayden_font_mobile($h3_size_key);

    // Global spacing scale → CSS variables
    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');

    $spacing_presets = [
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

    $spacing = $spacing_presets[$spacing_choice] ?? $spacing_presets['comfortable'];

    $section_space_mobile  = $spacing['mobile'];
    $section_space_desktop = $spacing['desktop'];

    ?>
    <style id="hayden-theme-colors">
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

        --site-max-width: <?php echo $container_width; ?>px;
        --site-logo-max-height: <?php echo $logo_height; ?>px;

        /* Typography sizes (Tailwind-based) */
        --body-font-size-mobile: <?php echo esc_html($body_mobile); ?>;
        --body-font-size-desktop: <?php echo esc_html($body_desktop); ?>;
        --body-font-size: <?php echo esc_html($body_desktop); ?>;

        --h1-font-size-mobile: <?php echo esc_html($h1_mobile); ?>;
        --h1-font-size-desktop: <?php echo esc_html($h1_desktop); ?>;

        --h2-font-size-mobile: <?php echo esc_html($h2_mobile); ?>;
        --h2-font-size-desktop: <?php echo esc_html($h2_desktop); ?>;

        --h3-font-size-mobile: <?php echo esc_html($h3_mobile); ?>;
        --h3-font-size-desktop: <?php echo esc_html($h3_desktop); ?>;

        /* Global section spacing */
        --section-space-mobile: <?php echo esc_html($section_space_mobile); ?>;
        --section-space-desktop: <?php echo esc_html($section_space_desktop); ?>;
      }

      /* Clamp the custom logo using our variable */
      .custom-logo,
      .site-logo img,
      .site-branding img {
        max-height: var(--site-logo-max-height);
        height: auto;
        width: auto;
      }
    </style>
    <?php
});

/**
 * CUSTOMIZER PANEL STYLING + RANGE VALUE JS (controls frame)
 */
add_action('customize_controls_enqueue_scripts', function () {
    wp_enqueue_style(
        'hayden-customizer-style',
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Primary colour for Customizer controls UI
    $primary = get_theme_mod('hayden_primary_color', '#f97316');

    $accent_css = sprintf(
        ':root { --color-primary: %s; }',
        esc_attr($primary)
    );

    wp_add_inline_style('hayden-customizer-style', $accent_css);

    $js = <<<JS
(function(api) {
  function updateWidthValue(val) {
    var el = document.getElementById('hayden-container-width-value');
    if (el) {
      el.textContent = val + 'px';
    }
  }

  // Live label for container width
  api('hayden_container_width', function(setting) {
    updateWidthValue(setting.get());
    setting.bind(function(newVal) {
      updateWidthValue(newVal);
    });
  });

  document.addEventListener('input', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('hayden-container-width-range')) {
      updateWidthValue(e.target.value);
    }
  });

  // Live primary colour for Customizer controls UI
  api('hayden_primary_color', function(setting) {
    function applyPrimary(val) {
      if (!val) {
        val = '#f97316';
      }

      var styleEl = document.getElementById('hayden-customizer-primary');
      if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'hayden-customizer-primary';
        document.head.appendChild(styleEl);
      }

      styleEl.textContent = ':root { --color-primary: ' + val + '; }';
    }

    applyPrimary(setting.get());

    setting.bind(function(newVal) {
      applyPrimary(newVal);
    });
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-controls', $js);
});

/**
 * Initial primary colour in Customizer controls frame
 */
add_action('customize_controls_print_styles', function () {
    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    ?>
    <style id="hayden-customizer-primary">
      :root {
        --color-primary: <?php echo esc_html($primary); ?>;
      }
    </style>
    <?php
});

/**
 * Live preview script – updates ALL colour CSS variables in the PREVIEW frame
 * so colours react instantly without saving.
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('customize-preview');

    $js = <<<JS
(function(api) {
  if (!api) return;

  // Map Customizer setting IDs -> CSS variables
  var colorMap = {
    // Global theme colours
    hayden_primary_color: '--color-primary',
    hayden_surface_color: '--color-surface',
    hayden_heading_color: '--color-headings',
    hayden_body_color: '--color-body',
    hayden_body_muted_color: '--color-body-muted',

    // Footer colours
    hayden_footer_color: '--color-footer',
    hayden_footer_text_color: '--color-footer-text',

    // Widget colours
    hayden_widget_bg_color: '--color-widget-bg',
    hayden_widget_title_color: '--color-widget-heading',
    hayden_widget_text_color: '--color-widget-text',
    hayden_widget_link_color: '--color-widget-link',

    // Footer widget colours
    hayden_footer_widget_title_color: '--color-footer-widget-heading',
    hayden_footer_widget_text_color: '--color-footer-widget-text',
    hayden_footer_widget_link_color: '--color-footer-widget-link',

    // Card colours
    hayden_card_bg: '--card-bg',
    hayden_card_heading: '--card-heading',
    hayden_card_text: '--card-text',
    hayden_card_text_muted: '--card-text-muted',

    // Nav colours
    hayden_nav_link_color: '--color-nav-link',
    hayden_nav_link_hover_color: '--color-nav-link-hover',
    hayden_nav_sub_bg_color: '--color-nav-sub-bg',
    hayden_nav_sub_link_color: '--color-nav-sub-link',
    hayden_nav_sub_hover_bg_color: '--color-nav-sub-hover-bg'
  };

  function setCssVar(name, value) {
    if (!name || !value) return;
    document.documentElement.style.setProperty(name, value);
  }

  // Recalculate nav toggle contrast from surface colour (matches PHP logic)
  function updateNavToggleFromSurface(hex) {
    if (!hex) return;

    hex = hex.replace('#', '');
    if (hex.length !== 6) return;

    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);

    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    var toggleColor = brightness > 150 ? '#111111' : '#ffffff';

    setCssVar('--color-nav-toggle', toggleColor);
  }

  Object.keys(colorMap).forEach(function(settingId) {
    var cssVar = colorMap[settingId];

    api(settingId, function(setting) {
      // Initial value when preview loads
      var initial = setting.get();
      if (initial) {
        setCssVar(cssVar, initial);

        if (settingId === 'hayden_surface_color') {
          updateNavToggleFromSurface(initial);
        }
        if (settingId === 'hayden_widget_bg_color') {
          // Keep surface-soft in sync with widget bg
          setCssVar('--color-surface-soft', initial);
        }
      }

      // Update on change
      setting.bind(function(newVal) {
        if (!newVal) return;
        setCssVar(cssVar, newVal);

        if (settingId === 'hayden_surface_color') {
          updateNavToggleFromSurface(newVal);
        }
        if (settingId === 'hayden_widget_bg_color') {
          setCssVar('--color-surface-soft', newVal);
        }
      });
    });
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-preview', $js);
});

/**
 * Apply Customizer logo max height to the custom logo markup.
 */
add_filter('get_custom_logo', function ($html) {
    $height = absint(get_theme_mod('hayden_logo_max_height', 80));

    if (!$height || !$html) {
        return $html;
    }

    $html = preg_replace(
        '/<img([^>]+)>/',
        '<img$1 style="max-height:' . $height . 'px;height:auto;width:auto;">',
        $html,
        1
    );

    return $html;
});

/**
 * Build the CSS for any custom fonts from the Customizer.
 *
 * @return string
 */
function hayden_get_custom_font_css(): string
{
    $body_id    = get_theme_mod('hayden_font_sans_file');
    $heading_id = get_theme_mod('hayden_font_serif_file');

    if (!$body_id && !$heading_id) {
        return '';
    }

    $css = '';
    $rootVars = '';

    // Heading font → overrides --font-sans (Cabin stack)
    if ($heading_id) {
        $heading_url = wp_get_attachment_url($heading_id);
        if ($heading_url) {
            $format = hayden_detect_font_format($heading_url);

            $css .= "@font-face{
                font-family:'HaydenHeading';
                src:url('{$heading_url}') format('{$format}');
                font-weight:400;
                font-style:normal;
                font-display:swap;
            }\n";

            $rootVars .= "--font-sans: 'HaydenHeading', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;";
        }
    }

    // Body font → overrides --font-serif (Merriweather stack)
    if ($body_id) {
        $body_url = wp_get_attachment_url($body_id);
        if ($body_url) {
            $format = hayden_detect_font_format($body_url);

            $css .= "@font-face{
                font-family:'HaydenBody';
                src:url('{$body_url}') format('{$format}');
                font-weight:400;
                font-style:normal;
                font-display:swap;
            }\n";

            $rootVars .= "--font-serif: 'HaydenBody', 'Times New Roman', Georgia, serif;";
        }
    }

    if ($rootVars) {
        $css .= ":root{{$rootVars}}\n";
    }

    return trim($css);
}

/**
 * Rough font format detection from file extension.
 */
function hayden_detect_font_format(string $url): string
{
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

    switch ($ext) {
        case 'woff2':
            return 'woff2';
        case 'woff':
            return 'woff';
        case 'otf':
            return 'opentype';
        case 'ttf':
        default:
            return 'truetype';
    }
}

/**
 * Echo custom font CSS in <head> on front-end + admin.
 */
add_action('wp_head', function () {
    $css = hayden_get_custom_font_css();
    if (!$css) {
        return;
    }

    echo '<style id="hayden-custom-fonts">' . $css . '</style>';
}, 50);

add_action('admin_head', function () {
    $css = hayden_get_custom_font_css();
    if (!$css) {
        return;
    }

    echo '<style id="hayden-custom-fonts-admin">' . $css . '</style>';
}, 50);

/**
 * Ensure block editor iframe also gets the fonts/variables.
 */
add_action('enqueue_block_editor_assets', function () {
    $css = '';

    // -------------------------------------------------
    // 1. Custom fonts (same as before)
    // -------------------------------------------------
    $font_css = hayden_get_custom_font_css();
    if ($font_css) {
        $css .= $font_css;

        $css .= "
        .editor-styles-wrapper {
            font-family: var(--font-serif);
        }

        .editor-styles-wrapper p,
        .editor-styles-wrapper li {
            font-family: inherit;
        }

        .editor-styles-wrapper h1,
        .editor-styles-wrapper h2,
        .editor-styles-wrapper h3,
        .editor-styles-wrapper h4,
        .editor-styles-wrapper h5,
        .editor-styles-wrapper h6 {
            font-family: var(--font-sans);
        }

        .edit-post-visual-editor__post-title-wrapper .editor-post-title__input {
            font-family: var(--font-sans);
        }
        ";
    }

    // -------------------------------------------------
    // 2. Global spacing vars (mirror wp_head output)
    // -------------------------------------------------
    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');

    $spacing_presets = [
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

    $spacing = $spacing_presets[$spacing_choice] ?? $spacing_presets['comfortable'];

    $section_space_mobile  = $spacing['mobile'];
    $section_space_desktop = $spacing['desktop'];

    $css .= "
    :root {
        --section-space-mobile: {$section_space_mobile};
        --section-space-desktop: {$section_space_desktop};
    }

    /* Only apply global section spacing to our Smart hero block
       in the editor, not all blocks. */
    .editor-styles-wrapper .wp-block-smart-hero-primary {
        margin-top: var(--section-space-mobile);
        margin-bottom: var(--section-space-mobile);
    }

    @media (min-width: 768px) {
        .editor-styles-wrapper .wp-block-smart-hero-primary {
            margin-top: var(--section-space-desktop);
            margin-bottom: var(--section-space-desktop);
        }
    }
    ";

    // -------------------------------------------------
    // 3. Inject into editor styles
    // -------------------------------------------------
    if (trim($css) !== '') {
        wp_add_inline_style('wp-block-library', $css);
        wp_add_inline_style('wp-block-library-theme', $css);

        if (wp_style_is('sage/editor', 'registered')) {
            wp_add_inline_style('sage/editor', $css);
        }
    }
});




/**
 * Limit font-size choices to our Tailwind keys.
 */
function hayden_sanitize_font_scale(string $value): string
{
    $allowed = [
        'text-sm',
        'text-base',
        'text-lg',
        'text-xl',
        'text-2xl',
        'text-3xl',
        'text-4xl',
    ];

    return in_array($value, $allowed, true) ? $value : 'text-base';
}


