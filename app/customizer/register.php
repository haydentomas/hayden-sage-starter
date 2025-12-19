<?php

namespace App\Customizer;

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

    $add_color('hayden_nav_link_color',         'Nav Parent Link Colour',         '#111111');
    $add_color('hayden_nav_link_hover_color',   'Nav Link Hover/Active Colour',   '#f97316');
    $add_color('hayden_nav_sub_bg_color',       'Dropdown Background Colour',     '#020617');
    $add_color('hayden_nav_sub_link_color',     'Dropdown Link Colour',           '#f97316');
    $add_color('hayden_nav_sub_hover_bg_color', 'Dropdown Link Hover Background', '#3b1d08');

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
            'sanitize_callback' => __NAMESPACE__ . '\\sanitize_font_scale',
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
        'sanitize_callback' => __NAMESPACE__ . '\\sanitize_columns',
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
 * Body class: nav style.
 */
add_filter('body_class', function (array $classes): array {
    $style = get_theme_mod('hayden_nav_link_style', 'basic');
    if (!in_array($style, ['basic', 'pill', 'underline'], true)) {
        $style = 'basic';
    }
    $classes[] = 'nav-style-' . $style;
    return $classes;
});

/**
 * Apply Customizer logo max height to the custom logo markup.
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
