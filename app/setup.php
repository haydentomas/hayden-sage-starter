<?php

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * -------------------------------------------------------------------------
 * Theme Setup / Globals
 * -------------------------------------------------------------------------
 *
 * Keep this file focused on:
 * - theme supports + menus
 * - widget areas
 * - editor assets (Vite)
 * - mega menu admin fields + saving
 * - activation helpers (header/footer content pages)
 * - small safe filters (mimes, excerpts, etc.)
 * - single post per-post overrides (meta boxes)
 *
 * Keep template logic out of here.
 */

const HAYDEN_TEXT_DOMAIN = 'hayden';

/**
 * -------------------------------------------------------------------------
 * Helpers
 * -------------------------------------------------------------------------
 */

if (! function_exists(__NAMESPACE__ . '\\hayden_int_range')) {
    /**
     * Clamp an integer into a min/max range (inclusive).
     */
    function hayden_int_range($value, int $min, int $max, int $fallback): int
    {
        $value = (int) $value;

        if ($value < $min || $value > $max) {
            return $fallback;
        }

        return $value;
    }
}

if (! function_exists(__NAMESPACE__ . '\\hayden_get_or_create_page_by_slug')) {
    /**
     * Create or fetch a page by slug and store its ID in an option.
     * Adds a marker post meta so the theme can identify it later.
     */
    function hayden_get_or_create_page_by_slug(
        string $option_key,
        string $slug,
        string $title,
        string $marker_meta_key
    ): void {
        if (get_option($option_key)) {
            return;
        }

        $existing = get_page_by_path($slug);
        if ($existing instanceof \WP_Post) {
            update_option($option_key, (int) $existing->ID);
            return;
        }

        $page_id = wp_insert_post([
            'post_type'    => 'page',
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_content' => '',
        ]);

        if (! is_wp_error($page_id) && $page_id) {
            update_option($option_key, (int) $page_id);
            update_post_meta((int) $page_id, $marker_meta_key, '1');
        }
    }
}

/**
 * -------------------------------------------------------------------------
 * Editor assets (Vite)
 * Inject editor.css + editor.js into block editor only.
 * -------------------------------------------------------------------------
 */

add_filter('block_editor_settings_all', function (array $settings): array {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('" . esc_url($style) . "')",
    ];

    return $settings;
});

add_action('admin_head', function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if (! $screen || ! method_exists($screen, 'is_block_editor') || ! $screen->is_block_editor()) {
        return;
    }

    $deps = json_decode((string) Vite::content('editor.deps.json'), true);
    $deps = is_array($deps) ? $deps : [];

    foreach ($deps as $dep) {
        $dep = is_string($dep) ? $dep : '';
        if ($dep && ! wp_script_is($dep, 'enqueued')) {
            wp_enqueue_script($dep);
        }
    }

    // Vite::toHtml() returns safe HTML tags; keep output restricted to editor only.
    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * -------------------------------------------------------------------------
 * theme.json — use generated build/assets/theme.json
 * -------------------------------------------------------------------------
 */

add_filter('theme_file_path', function (string $path, string $file): string {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * -------------------------------------------------------------------------
 * Theme setup
 * -------------------------------------------------------------------------
 */

add_action('after_setup_theme', function (): void {
    // Disable FSE block templates.
    remove_theme_support('block-templates');

    // Menus.
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', HAYDEN_TEXT_DOMAIN),
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

    // Allow a custom logo via Customizer → Site Identity.
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

add_action('widgets_init', function (): void {
    register_sidebar([
        'name'          => __('Primary Sidebar', HAYDEN_TEXT_DOMAIN),
        'id'            => 'sidebar-primary',
        'description'   => __('Main sidebar shown on blog posts and archives.', HAYDEN_TEXT_DOMAIN),
        'before_widget' => '<section id="%1$s" class="widget %2$s sidebar-card bg-surface-soft border rounded-2xl p-5 mb-6">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-sm font-semibold tracking-wide uppercase text-white mb-3">',
        'after_title'   => '</h2>',
    ]);

    $footer_base = [
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-sm font-semibold mb-3 uppercase tracking-wide">',
        'after_title'   => '</h3>',
    ];

    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name' => sprintf(__('Footer %d', HAYDEN_TEXT_DOMAIN), $i),
            'id'   => "sidebar-footer-{$i}",
        ] + $footer_base);
    }
});

/**
 * -------------------------------------------------------------------------
 * Mega menu fields (top-level items)
 * -------------------------------------------------------------------------
 */

add_action('wp_nav_menu_item_custom_fields', function (int $item_id, $item, int $depth): void {
    if ($depth !== 0) {
        return;
    }

    $is_mega = get_post_meta($item_id, '_menu_item_mega_parent', true) === '1';

    $columns = hayden_int_range(
        get_post_meta($item_id, '_menu_item_mega_columns', true),
        1,
        4,
        3
    );
    ?>
    <div class="field-mega-menu description description-wide" style="margin-top:10px;border-top:1px solid #ddd;padding-top:10px;">
        <strong><?php echo esc_html__('Mega menu', HAYDEN_TEXT_DOMAIN); ?></strong>

        <p>
            <label>
                <input
                    type="checkbox"
                    name="menu-item-mega-parent[<?php echo esc_attr($item_id); ?>]"
                    value="1"
                    <?php checked($is_mega); ?>
                />
                <?php echo esc_html__('Enable this item as a mega menu parent', HAYDEN_TEXT_DOMAIN); ?>
            </label>
        </p>

        <p>
            <label>
                <?php echo esc_html__('Columns:', HAYDEN_TEXT_DOMAIN); ?>&nbsp;
                <select name="menu-item-mega-columns[<?php echo esc_attr($item_id); ?>]">
                    <?php foreach ([1, 2, 3, 4] as $col) : ?>
                        <option value="<?php echo (int) $col; ?>" <?php selected($columns, $col); ?>>
                            <?php
                            echo (int) $col . ' ' . esc_html(_n('column', 'columns', $col, HAYDEN_TEXT_DOMAIN));
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>

        <p class="description">
            <?php echo esc_html__('Add child items under this parent for each column, and child items under those for the links inside each column.', HAYDEN_TEXT_DOMAIN); ?>
        </p>
    </div>
    <?php
}, 10, 3);

add_action('wp_update_nav_menu_item', function (int $menu_id, int $menu_item_db_id): void {
    if (! current_user_can('edit_theme_options')) {
        return;
    }

    if (
        ! isset($_POST['update-nav-menu-nonce']) ||
        ! wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['update-nav-menu-nonce'])),
            'update-nav_menu'
        )
    ) {
        return;
    }

    // Checkbox.
    $is_mega = isset($_POST['menu-item-mega-parent'][$menu_item_db_id]) ? '1' : '0';
    update_post_meta($menu_item_db_id, '_menu_item_mega_parent', $is_mega);

    // Columns.
    if (isset($_POST['menu-item-mega-columns'][$menu_item_db_id])) {
        $raw  = wp_unslash($_POST['menu-item-mega-columns'][$menu_item_db_id]);
        $cols = hayden_int_range($raw, 1, 4, 3);
        update_post_meta($menu_item_db_id, '_menu_item_mega_columns', $cols);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_mega_columns');
    }
}, 10, 2);

/**
 * -------------------------------------------------------------------------
 * Upload mimes (fonts)
 * -------------------------------------------------------------------------
 */

add_filter('upload_mimes', function (array $mimes): array {
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

add_action('customize_register', function ($wp_customize): void {
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

add_action('after_switch_theme', function (): void {
    hayden_get_or_create_page_by_slug(
        'hayden_footer_page_id',
        'site-footer',
        __('Footer', HAYDEN_TEXT_DOMAIN),
        '_hayden_is_footer_content'
    );

    hayden_get_or_create_page_by_slug(
        'hayden_header_page_id',
        'site-header',
        __('Header', HAYDEN_TEXT_DOMAIN),
        '_hayden_is_header_content'
    );
});

/**
 * -------------------------------------------------------------------------
 * Prevent viewing header/footer content pages directly (redirect to home)
 * -------------------------------------------------------------------------
 */

add_action('template_redirect', function (): void {
    $footer_page_id = (int) get_option('hayden_footer_page_id', 0);
    $header_page_id = (int) get_option('hayden_header_page_id', 0);

    if (($footer_page_id && is_page($footer_page_id)) || ($header_page_id && is_page($header_page_id))) {
        wp_safe_redirect(home_url('/'), 302);
        exit;
    }
});

/**
 * -------------------------------------------------------------------------
 * Block editor: load default fonts + ensure vars exist for editor styles
 * -------------------------------------------------------------------------
 */

add_action('enqueue_block_editor_assets', function (): void {
    wp_enqueue_style(
        'hayden-editor-fonts',
        get_theme_file_uri('resources/css/fonts.css'),
        [],
        wp_get_theme()->get('Version')
    );

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

    wp_add_inline_style('wp-block-library', $defaults);
});

/**
 * -------------------------------------------------------------------------
 * Excerpts
 * -------------------------------------------------------------------------
 */

add_filter('excerpt_length', function (): int {
    return 32;
}, 999);

add_filter('excerpt_more', function (): string {
    return '…';
});

/**
 * -------------------------------------------------------------------------
 * Single post overrides (meta boxes) — Sidebar + Featured Image
 * Values stored: global|show|hide
 * -------------------------------------------------------------------------
 */

add_action('add_meta_boxes', function (): void {
    // Sidebar override
    add_meta_box(
        'hayden_sidebar_override',
        __('Single Post: Sidebar', HAYDEN_TEXT_DOMAIN),
        function (\WP_Post $post): void {
            $value = get_post_meta($post->ID, '_hayden_single_sidebar', true) ?: 'global';
            wp_nonce_field('hayden_single_overrides_save', 'hayden_single_overrides_nonce');

            $choices = [
                'global' => __('Use theme default', HAYDEN_TEXT_DOMAIN),
                'show'   => __('Force show sidebar', HAYDEN_TEXT_DOMAIN),
                'hide'   => __('Force hide sidebar', HAYDEN_TEXT_DOMAIN),
            ];
            ?>
            <fieldset>
                <?php foreach ($choices as $key => $label) : ?>
                    <label style="display:block;margin:6px 0;">
                        <input
                            type="radio"
                            name="hayden_single_sidebar"
                            value="<?php echo esc_attr($key); ?>"
                            <?php checked($value, $key); ?>
                        />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <?php
        },
        'post',
        'side'
    );

    // Featured image override
    add_meta_box(
        'hayden_featured_override',
        __('Single Post: Featured Image', HAYDEN_TEXT_DOMAIN),
        function (\WP_Post $post): void {
            $value = get_post_meta($post->ID, '_hayden_single_featured', true) ?: 'global';
            wp_nonce_field('hayden_single_overrides_save', 'hayden_single_overrides_nonce');

            $choices = [
                'global' => __('Use theme default', HAYDEN_TEXT_DOMAIN),
                'show'   => __('Force show featured image', HAYDEN_TEXT_DOMAIN),
                'hide'   => __('Force hide featured image', HAYDEN_TEXT_DOMAIN),
            ];
            ?>
            <fieldset>
                <?php foreach ($choices as $key => $label) : ?>
                    <label style="display:block;margin:6px 0;">
                        <input
                            type="radio"
                            name="hayden_single_featured"
                            value="<?php echo esc_attr($key); ?>"
                            <?php checked($value, $key); ?>
                        />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <?php
        },
        'post',
        'side'
    );
});

add_action('save_post', function (int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (
        ! isset($_POST['hayden_single_overrides_nonce']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hayden_single_overrides_nonce'])), 'hayden_single_overrides_save')
    ) {
        return;
    }

    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    $allowed = ['global', 'show', 'hide'];

    // Sidebar
    $sidebar = isset($_POST['hayden_single_sidebar'])
        ? sanitize_text_field(wp_unslash($_POST['hayden_single_sidebar']))
        : 'global';
    if (! in_array($sidebar, $allowed, true)) {
        $sidebar = 'global';
    }

    if ($sidebar === 'global') {
        delete_post_meta($post_id, '_hayden_single_sidebar');
    } else {
        update_post_meta($post_id, '_hayden_single_sidebar', $sidebar);
    }

    // Featured image
    $featured = isset($_POST['hayden_single_featured'])
        ? sanitize_text_field(wp_unslash($_POST['hayden_single_featured']))
        : 'global';
    if (! in_array($featured, $allowed, true)) {
        $featured = 'global';
    }

    if ($featured === 'global') {
        delete_post_meta($post_id, '_hayden_single_featured');
    } else {
        update_post_meta($post_id, '_hayden_single_featured', $featured);
    }
});




add_filter('body_class', function (array $classes): array {
  if ((bool) get_theme_mod('hayden_spacing_apply_smart', 1)) {
    $classes[] = 'has-hayden-spacing-smart';
  }
  if ((bool) get_theme_mod('hayden_spacing_apply_core', 0)) {
    $classes[] = 'has-hayden-spacing-core';
  }
  return $classes;
});
