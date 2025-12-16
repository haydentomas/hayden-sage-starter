<?php

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * -------------------------------------------------------------------------
 * Editor assets (Vite) — inject editor.css + editor.js into block editor only
 * -------------------------------------------------------------------------
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

add_action('admin_head', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || ! method_exists($screen, 'is_block_editor') || ! $screen->is_block_editor()) {
        return;
    }

    $deps = json_decode(Vite::content('editor.deps.json'), true) ?: [];
    foreach ($deps as $dep) {
        if ($dep && ! wp_script_is($dep, 'enqueued')) {
            wp_enqueue_script($dep);
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * -------------------------------------------------------------------------
 * theme.json — use generated build/assets/theme.json
 * -------------------------------------------------------------------------
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * -------------------------------------------------------------------------
 * Theme setup
 * -------------------------------------------------------------------------
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

    // Allow a custom logo via Customizer → Site Identity
    add_theme_support('custom-logo', [
        'height'               => 80,
        'width'                => 240,
        'flex-width'           => true,
        'flex-height'          => true,
        'unlink-homepage-logo' => true,
    ]);
}, 20);

/**
 * -------------------------------------------------------------------------
 * Sidebars
 * -------------------------------------------------------------------------
 */
add_action('widgets_init', function () {
    // Primary (blog/sidebar)
    register_sidebar([
        'name'          => __('Primary Sidebar', 'hayden'),
        'id'            => 'sidebar-primary',
        'description'   => __('Main sidebar shown on blog posts and archives.', 'hayden'),
        'before_widget' => '<section id="%1$s" class="widget %2$s sidebar-card bg-surface-soft border border-white/5 rounded-2xl p-5 mb-6">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-sm font-semibold tracking-wide uppercase text-white mb-3">',
        'after_title'   => '</h2>',
    ]);

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

/**
 * -------------------------------------------------------------------------
 * Mega menu fields (top-level items)
 * -------------------------------------------------------------------------
 */
add_action('wp_nav_menu_item_custom_fields', function ($item_id, $item, $depth) {
    if ($depth !== 0) {
        return;
    }

    $is_mega = get_post_meta($item_id, '_menu_item_mega_parent', true) === '1';

    $columns = (int) get_post_meta($item_id, '_menu_item_mega_columns', true);
    if ($columns < 1 || $columns > 4) {
        $columns = 3;
    }
    ?>
    <div class="field-mega-menu description description-wide" style="margin-top:10px;border-top:1px solid #ddd;padding-top:10px;">
        <strong>Mega menu</strong>

        <p>
            <label>
                <input
                    type="checkbox"
                    name="menu-item-mega-parent[<?php echo esc_attr($item_id); ?>]"
                    value="1"
                    <?php checked($is_mega); ?>
                />
                Enable this item as a mega menu parent
            </label>
        </p>

        <p>
            <label>Columns:&nbsp;
                <select name="menu-item-mega-columns[<?php echo esc_attr($item_id); ?>]">
                    <?php foreach ([1, 2, 3, 4] as $col) : ?>
                        <option value="<?php echo (int) $col; ?>" <?php selected($columns, $col); ?>>
                            <?php echo (int) $col; ?> column<?php echo $col > 1 ? 's' : ''; ?>
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
}, 10, 3);

add_action('wp_update_nav_menu_item', function ($menu_id, $menu_item_db_id) {
    $is_mega = isset($_POST['menu-item-mega-parent'][$menu_item_db_id]) ? '1' : '0';
    update_post_meta($menu_item_db_id, '_menu_item_mega_parent', $is_mega);

    if (isset($_POST['menu-item-mega-columns'][$menu_item_db_id])) {
        $cols = (int) $_POST['menu-item-mega-columns'][$menu_item_db_id];
        if ($cols < 1 || $cols > 4) {
            $cols = 3;
        }
        update_post_meta($menu_item_db_id, '_menu_item_mega_columns', $cols);
    }
}, 10, 2);

/**
 * -------------------------------------------------------------------------
 * Upload mimes (fonts)
 * -------------------------------------------------------------------------
 */
add_filter('upload_mimes', function ($mimes) {
    $mimes['woff']  = 'font/woff';
    $mimes['woff2'] = 'font/woff2';
    $mimes['ttf']   = 'font/ttf';
    $mimes['otf']   = 'font/otf';
    return $mimes;
});

/**
 * -------------------------------------------------------------------------
 * Customizer cleanup (remove stray "Theme Colors" sections)
 * -------------------------------------------------------------------------
 */
add_action('customize_register', function ($wp_customize) {
    foreach ($wp_customize->sections() as $section) {
        if (in_array($section->title, ['Theme Colors', 'Theme colours', 'Colors', 'Colours'], true)) {
            if ($section->id === 'hayden_color_section') {
                continue;
            }
            $wp_customize->remove_section($section->id);
        }
    }
}, 999);

/**
 * -------------------------------------------------------------------------
 * Auto-create special block pages on theme activation (Footer + Header)
 * -------------------------------------------------------------------------
 */
add_action('after_switch_theme', function () {

    // ----- Footer page -----
    if (! get_option('hayden_footer_page_id')) {
        $slug  = 'site-footer';
        $title = 'Footer';

        $existing = get_page_by_path($slug);
        if ($existing instanceof \WP_Post) {
            update_option('hayden_footer_page_id', (int) $existing->ID);
        } else {
            $page_id = wp_insert_post([
                'post_type'    => 'page',
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_content' => '',
            ]);

            if (! is_wp_error($page_id) && $page_id) {
                update_option('hayden_footer_page_id', (int) $page_id);
                update_post_meta((int) $page_id, '_hayden_is_footer_content', '1');
            }
        }
    }

    // ----- Header page -----
    if (! get_option('hayden_header_page_id')) {
        $slug  = 'site-header';
        $title = 'Header';

        $existing = get_page_by_path($slug);
        if ($existing instanceof \WP_Post) {
            update_option('hayden_header_page_id', (int) $existing->ID);
        } else {
            $page_id = wp_insert_post([
                'post_type'    => 'page',
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_content' => '',
            ]);

            if (! is_wp_error($page_id) && $page_id) {
                update_option('hayden_header_page_id', (int) $page_id);
                update_post_meta((int) $page_id, '_hayden_is_header_content', '1');
            }
        }
    }
});

/**
 * -------------------------------------------------------------------------
 * Prevent viewing header/footer content pages directly (redirect to home)
 * -------------------------------------------------------------------------
 */
add_action('template_redirect', function () {
    $footer_page_id = (int) get_option('hayden_footer_page_id', 0);
    $header_page_id = (int) get_option('hayden_header_page_id', 0);

    if (($footer_page_id && is_page($footer_page_id)) || ($header_page_id && is_page($header_page_id))) {
        wp_safe_redirect(home_url('/'), 302);
        exit;
    }
});


add_action('enqueue_block_editor_assets', function () {

    // 1) Load your default theme fonts into the editor iframe
    wp_enqueue_style(
        'hayden-editor-fonts',
        get_theme_file_uri('resources/css/fonts.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // 2) Ensure default font vars exist even when no uploads are set
    $defaults = "
      :root{
        --font-serif:'Merriweather',Georgia,'Times New Roman',serif;
        --font-sans:'Cabin',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Ubuntu,Cantarell,'Helvetica Neue',sans-serif;
      }
      .editor-styles-wrapper{font-family:var(--font-serif);}
      .editor-styles-wrapper h1,
      .editor-styles-wrapper h2,
      .editor-styles-wrapper h3,
      .editor-styles-wrapper h4,
      .editor-styles-wrapper h5,
      .editor-styles-wrapper h6{font-family:var(--font-sans);}
    ";

    // Put it onto a handle that exists in the editor
    wp_add_inline_style('wp-block-library', $defaults);
});
