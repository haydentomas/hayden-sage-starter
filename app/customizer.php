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
     * THEME COLOUR SECTION
     * ------------------------------------------------------------
     */
    $wp_customize->add_section('hayden_color_section', [
        'title'       => __('Theme Colours', 'hayden'),
        'description' => '',
        'priority'    => 30,
        'panel'       => 'hayden_theme_panel',
    ]);

    /**
     * Intro text: "Control global brand colours." in white
     */
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
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
        'default'           => '#ffffff',
        'transport'         => 'refresh',
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

    // Body text colour → --color-body & --color-body-muted
    $wp_customize->add_setting('hayden_body_color', [
        'default'           => '#111111',
        'transport'         => 'refresh',
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

    // Footer background colour → --color-footer
    $wp_customize->add_setting('hayden_footer_color', [
        'default'           => '#020617',
        'transport'         => 'refresh',
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

    // Widget / card background → --color-surface-soft / --color-widget-bg
    $wp_customize->add_setting('hayden_widget_bg_color', [
        'default'           => '#000000', // black by default
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    $wp_customize->add_control(new \WP_Customize_Color_Control(
        $wp_customize,
        'hayden_widget_bg_color_control',
        [
            'label'    => __('Widget / Card Background Colour', 'hayden'),
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
        'default'           => '#f97316', // primary
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
        'default'           => '#f97316', // primary
        'transport'         => 'refresh',
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
     * Sub-heading: Navigation colours (visual separator only)
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
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
        'transport'         => 'refresh',
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
});

/**
 * ------------------------------------------------------------
 * OUTPUT DYNAMIC CSS VARIABLES TO THE FRONTEND
 * ------------------------------------------------------------
 */
add_action('wp_head', function () {

    $primary    = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    $surface    = sanitize_hex_color(get_theme_mod('hayden_surface_color', '#FFFAF8')) ?: '#FFFAF8';
    $headings   = sanitize_hex_color(get_theme_mod('hayden_heading_color', '#ffffff')) ?: '#ffffff';
    $body_text  = sanitize_hex_color(get_theme_mod('hayden_body_color', '#111111')) ?: '#111111';
    $footer     = sanitize_hex_color(get_theme_mod('hayden_footer_color', '#020617')) ?: '#020617';

    $widget_bg     = sanitize_hex_color(get_theme_mod('hayden_widget_bg_color', '#000000')) ?: '#000000';
    $widget_title  = sanitize_hex_color(get_theme_mod('hayden_widget_title_color', '#f97316')) ?: '#f97316';
    $widget_text   = sanitize_hex_color(get_theme_mod('hayden_widget_text_color', '#ffffff')) ?: '#ffffff';
    $widget_link   = sanitize_hex_color(get_theme_mod('hayden_widget_link_color', '#f97316')) ?: '#f97316';

    $nav_link         = sanitize_hex_color(get_theme_mod('hayden_nav_link_color', '#111111')) ?: '#111111';
    $nav_link_hover   = sanitize_hex_color(get_theme_mod('hayden_nav_link_hover_color', '#f97316')) ?: '#f97316';
    $nav_sub_bg       = sanitize_hex_color(get_theme_mod('hayden_nav_sub_bg_color', '#020617')) ?: '#020617';
    $nav_sub_link     = sanitize_hex_color(get_theme_mod('hayden_nav_sub_link_color', '#f97316')) ?: '#f97316';
    $nav_sub_hover_bg = sanitize_hex_color(get_theme_mod('hayden_nav_sub_hover_bg_color', '#3b1d08')) ?: '#3b1d08';

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

    ?>
    <style id="hayden-theme-colors">
      :root {
        --color-primary: <?php echo esc_html($primary); ?>;
        --color-surface: <?php echo esc_html($surface); ?>;
        --color-surface-soft: <?php echo esc_html($widget_bg); ?>;
        --color-headings: <?php echo esc_html($headings); ?>;
        --color-body: <?php echo esc_html($body_text); ?>;
        --color-body-muted: <?php echo esc_html($body_text); ?>;
        --color-footer: <?php echo esc_html($footer); ?>;

        --color-widget-bg: <?php echo esc_html($widget_bg); ?>;
        --color-widget-heading: <?php echo esc_html($widget_title); ?>;
        --color-widget-text: <?php echo esc_html($widget_text); ?>;
        --color-widget-link: <?php echo esc_html($widget_link); ?>;

        --color-nav-link: <?php echo esc_html($nav_link); ?>;
        --color-nav-link-hover: <?php echo esc_html($nav_link_hover); ?>;
        --color-nav-sub-bg: <?php echo esc_html($nav_sub_bg); ?>;
        --color-nav-sub-link: <?php echo esc_html($nav_sub_link); ?>;
        --color-nav-sub-hover-bg: <?php echo esc_html($nav_sub_hover_bg); ?>;

        --color-nav-toggle: <?php echo esc_html($nav_toggle); ?>;

        --site-max-width: <?php echo $container_width; ?>px;
        --site-logo-max-height: <?php echo $logo_height; ?>px;
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
 * CUSTOMIZER PANEL STYLING + RANGE VALUE JS
 */
add_action('customize_controls_enqueue_scripts', function () {
    wp_enqueue_style(
        'hayden-customizer-style',
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Inline JS to keep the container width value in sync with the slider
    $js = <<<JS
(function(api) {
  function updateWidthValue(val) {
    var el = document.getElementById('hayden-container-width-value');
    if (el) {
      el.textContent = val + 'px';
    }
  }

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
})(wp.customize);
JS;

    wp_add_inline_script('customize-controls', $js);
});

/**
 * Primary colour in Customizer UI (live update)
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
 * Apply Customizer logo max height to the custom logo markup.
 */
add_filter('get_custom_logo', function ($html) {
    $height = absint(get_theme_mod('hayden_logo_max_height', 80));

    if (!$height || !$html) {
        return $html;
    }

    // Inject max-height + auto sizing on the <img> tag
    $html = preg_replace(
        '/<img([^>]+)>/',
        '<img$1 style="max-height:' . $height . 'px;height:auto;width:auto;">',
        $html,
        1
    );

    return $html;
});
