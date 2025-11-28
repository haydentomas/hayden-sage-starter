<?php

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 */
add_filter('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    $dependencies = json_decode(Vite::content('editor.deps.json'));

    foreach ($dependencies as $dependency) {
        if (! wp_script_is($dependency)) {
            wp_enqueue_script($dependency);
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Register the initial theme setup.
 */
add_action('after_setup_theme', function () {
    // Disable FSE block templates.
    remove_theme_support('block-templates');

    // Menus.
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    // Disable default block patterns.
    remove_theme_support('core-block-patterns');

    // Core supports.
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');

    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    // Customizer selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');
}, 20);


// Allow a custom logo via Customizer → Site Identity
add_theme_support('custom-logo', [
    'height'               => 80,
    'width'                => 240,
    'flex-width'           => true,
    'flex-height'          => true,
    'unlink-homepage-logo' => true,
]);


/**
 * Register the theme sidebars.
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ];

 // Primary (blog/sidebar)
register_sidebar([
    'name'          => __('Primary Sidebar', 'hayden'),
    'id'            => 'sidebar-primary',
    'description'   => __('Main sidebar shown on blog posts and archives.', 'hayden'),
    'before_widget' => '<section id="%1$s" class="widget %2$s sidebar-card bg-surface-soft border border-white/5 rounded-2xl p-5 mb-6">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title text-sm font-semibold tracking-wide uppercase text-white mb-3">',
    'after_title'   => '</h2>',
] + $config);


    // Footer columns 1–4
    $footer_base = [
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-sm font-semibold mb-3 uppercase tracking-wide">',
        'after_title'   => '</h3>',
    ];

    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name' => sprintf(__('Footer %d', 'hayden'), $i),
            'id'   => "sidebar-footer-{$i}",
        ] + $footer_base);
    }
});





namespace App;

use Walker_Nav_Menu;

/**
 * 1) Extra fields in Appearance → Menus
 * -------------------------------------------------------------- */
add_action('wp_nav_menu_item_custom_fields', function ($item_id, $item, $depth, $args) {
    // Only show on top-level items
    if ($depth !== 0) {
        return;
    }

    $is_mega  = get_post_meta($item_id, '_menu_item_mega_parent', true) === '1';
    $columns  = (int) get_post_meta($item_id, '_menu_item_mega_columns', true);
    if ($columns < 1 || $columns > 4) {
        $columns = 3;
    }
    ?>
    <div class="field-mega-menu description description-wide" style="margin-top: 10px; border-top: 1px solid #ddd; padding-top: 10px;">
        <strong>Mega menu</strong>

        <p>
            <label>
                <input type="checkbox"
                       name="menu-item-mega-parent[<?php echo esc_attr($item_id); ?>]"
                       value="1" <?php checked($is_mega); ?> />
                Enable this item as a mega menu parent
            </label>
        </p>

        <p>
            <label>Columns:&nbsp;
                <select name="menu-item-mega-columns[<?php echo esc_attr($item_id); ?>]">
                    <?php foreach ([1, 2, 3, 4] as $col) : ?>
                        <option value="<?php echo $col; ?>" <?php selected($columns, $col); ?>>
                            <?php echo $col; ?> column<?php echo $col > 1 ? 's' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>

        <p class="description">
            Add child items under this parent for each column, and child items
            under those for the links inside each column.
        </p>
    </div>
    <?php
}, 10, 4);

/**
 * 2) Save the extra fields when the menu is saved
 * -------------------------------------------------------------- */
add_action('wp_update_nav_menu_item', function ($menu_id, $menu_item_db_id, $args) {
    // Mega parent toggle
    $is_mega = isset($_POST['menu-item-mega-parent'][$menu_item_db_id]) ? '1' : '0';
    update_post_meta($menu_item_db_id, '_menu_item_mega_parent', $is_mega);

    // Column count
    if (isset($_POST['menu-item-mega-columns'][$menu_item_db_id])) {
        $cols = (int) $_POST['menu-item-mega-columns'][$menu_item_db_id];
        if ($cols < 1 || $cols > 4) {
            $cols = 3;
        }
        update_post_meta($menu_item_db_id, '_menu_item_mega_columns', $cols);
    }
}, 10, 3);




add_action('customize_register', function ($wp_customize) {

    // Section
    $wp_customize->add_section('bbi_colors', [
        'title'    => __('Theme Colors', 'sage'),
        'priority' => 30,
    ]);

    // Setting
    $wp_customize->add_setting('color_surface', [
        'default'           => '#FFFAF8',
        'transport'         => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);

    // Control
// Control
$wp_customize->add_control(
    new \WP_Customize_Color_Control(
        $wp_customize,
        'color_surface',
        [
            'label'   => __('Background Surface Color', 'sage'),
            'section' => 'bbi_colors',
            'settings'=> 'color_surface',
        ]
    )
);
});



add_action('wp_head', function () {
    $color_surface = get_theme_mod('color_surface', '#FFFAF8');

    echo "<style>
        :root {
            --color-surface: {$color_surface};
        }
    </style>";
});


add_filter('upload_mimes', function ($mimes) {

    $mimes['woff']  = 'font/woff';
    $mimes['woff2'] = 'font/woff2';
    $mimes['ttf']   = 'font/ttf';
    $mimes['otf']   = 'font/otf';

    return $mimes;
});



add_action('customize_preview_init', function () {
    wp_enqueue_script(
        'hayden-customizer-preview',
        get_theme_file_uri('resources/scripts/customizer-preview.js'),
        ['customize-preview'],
        wp_get_theme()->get('Version'),
        true
    );
});
