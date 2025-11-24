<?php

namespace App;

/**
 * Theme Customizer: panels, sections, settings & controls.
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    /**
     * Main Theme Settings panel
     */
    $wp_customize->add_panel('hayden_theme_panel', [
        'title'       => __('Theme Settings', 'hayden'),
        'description' => __('Global layout, header, footer and colour settings.', 'hayden'),
        'priority'    => 10,
    ]);

    /**
     * Header Layout section
     */
    $wp_customize->add_section('hayden_header_section', [
        'title'       => __('Header Layout', 'hayden'),
        'description' => __('Choose the layout style for the main header.', 'hayden'),
        'priority'    => 20,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Setting: header layout
    $wp_customize->add_setting('hayden_header_layout', [
        'default'           => 'default',
        'transport'         => 'refresh',
        'sanitize_callback' => function ($value) {
            $allowed = ['default', 'logo-top'];
            return in_array($value, $allowed, true) ? $value : 'default';
        },
    ]);

    // Control: header layout select
    $wp_customize->add_control('hayden_header_layout_control', [
        'label'    => __('Header layout style', 'hayden'),
        'section'  => 'hayden_header_section',
        'settings' => 'hayden_header_layout',
        'type'     => 'select',
        'choices'  => [
            'default'  => __('Default – logo left, nav right', 'hayden'),
            'logo-top' => __('Logo top, nav beneath', 'hayden'),
        ],
    ]);

    /**
     * Theme Colours section
     */
    $wp_customize->add_section('hayden_color_section', [
        'title'       => __('Theme Colours', 'hayden'),
        'description' => __('Base brand colours used across the site.', 'hayden'),
        'priority'    => 30,
        'panel'       => 'hayden_theme_panel',
    ]);

    // Primary colour (maps to --color-primary)
    $wp_customize->add_setting('hayden_primary_color', [
        'default'           => '#f97316',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_primary_color_control',
        [
            'label'    => __('Primary colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_primary_color',
        ]
    ));

    // Primary soft colour (maps to --color-primary-soft)
    $wp_customize->add_setting('hayden_primary_soft_color', [
        'default'           => '#ffedd5',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_primary_soft_color_control',
        [
            'label'    => __('Primary soft colour', 'hayden'),
            'section'  => 'hayden_color_section',
            'settings' => 'hayden_primary_soft_color',
        ]
    ));

    /**
     * Grid Display section (Projects + Blog initial items)
     */
    $wp_customize->add_section('grid_display_section', [
        'title'       => __('Grid Display', 'hayden'),
        'description' => __('Control how many items show initially in grids.', 'hayden'),
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
        'description' => __('Number of projects to show before “Load more”.', 'hayden'),
        'section'     => 'grid_display_section',
        'settings'    => 'grid_projects_initial_items',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 1,
            'max'  => 48,
            'step' => 1,
        ],
    ]);

    // Blog initial items
    $wp_customize->add_setting('grid_blog_initial_items', [
        'default'           => 6,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ]);

    $wp_customize->add_control('grid_blog_initial_items_control', [
        'label'       => __('Blog: initial items', 'hayden'),
        'description' => __('Number of blog posts to show before “Load more”.', 'hayden'),
        'section'     => 'grid_display_section',
        'settings'    => 'grid_blog_initial_items',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 1,
            'max'  => 48,
            'step' => 1,
        ],
    ]);

    /**
     * Footer Layout section
     */
    $wp_customize->add_section('hayden_footer_section', [
        'title'       => __('Footer Layout', 'hayden'),
        'description' => __('Footer layout and widget columns.', 'hayden'),
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
     * Move footer widget areas under Theme Settings panel
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
            $section->title    = sprintf(__('Footer column %d widgets', 'hayden'), $index + 1);
        }
    }
});

/**
 * Output dynamic CSS variables for theme colours.
 */
add_action('wp_head', function () {
    $primary      = get_theme_mod('hayden_primary_color', '#f97316');
    $primary_soft = get_theme_mod('hayden_primary_soft_color', '#ffedd5');

    $primary      = sanitize_hex_color($primary) ?: '#f97316';
    $primary_soft = sanitize_hex_color($primary_soft) ?: '#ffedd5';
    ?>
    <style id="hayden-theme-colors">
      :root {
        --color-primary: <?php echo esc_html($primary); ?>;
        --color-primary-soft: <?php echo esc_html($primary_soft); ?>;
      }
    </style>
    <?php
});

/**
 * Customizer UI styling (left panel only)
 */
add_action('customize_controls_enqueue_scripts', function () {
    wp_enqueue_style(
        'hayden-customizer-style',
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );
});

/**
 * Inject primary colour into Customizer controls (left panel)
 */
add_action('customize_controls_print_styles', function () {
    $primary = get_theme_mod('hayden_primary_color', '#f97316');
    $primary = sanitize_hex_color($primary) ?: '#f97316';
    ?>
    <style id="hayden-customizer-primary">
      :root {
        --color-primary: <?php echo esc_html($primary); ?>;
      }
    </style>
    <?php
});

/**
 * Live-update the Customizer controls panel when primary colour changes.
 */
add_action('customize_controls_enqueue_scripts', function () {
    wp_add_inline_script(
        'customize-controls',
        "(function(api) {
            api('hayden_primary_color', function(setting) {
                setting.bind(function(newVal) {
                    if (!newVal) { return; }
                    // Update CSS variable in controls frame
                    document.documentElement.style.setProperty('--color-primary', newVal);
                });
            });
        })(wp.customize);"
    );
});
