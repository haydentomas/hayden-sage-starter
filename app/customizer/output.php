<?php

namespace App\Customizer;

function build_radius_vars(string $radius_style): array
{
    $radius_presets = [
        'sharp' => ['sm'=>'0rem','md'=>'0rem','lg'=>'0rem','xl'=>'0rem','2xl'=>'0rem','full'=>'0rem'],
        'soft'  => ['sm'=>'0.125rem','md'=>'0.375rem','lg'=>'0.5rem','xl'=>'0.75rem','2xl'=>'1rem','full'=>'9999px'],
        'round' => ['sm'=>'0.375rem','md'=>'0.5rem','lg'=>'0.75rem','xl'=>'1rem','2xl'=>'1.25rem','full'=>'9999px'],
    ];

    return $radius_presets[$radius_style] ?? $radius_presets['soft'];
}

function build_spacing_vars(string $choice): array
{
    $spacing_presets = [
        'compact'     => ['mobile' => '1.75rem', 'desktop' => '3rem'],
        'comfortable' => ['mobile' => '2.5rem',  'desktop' => '4rem'],
        'spacious'    => ['mobile' => '3.5rem',  'desktop' => '6rem'],
    ];

    return $spacing_presets[$choice] ?? $spacing_presets['comfortable'];
}

function compute_nav_toggle_contrast(string $surface, string $fallback = '#111111'): string
{
    $surface_hex = ltrim($surface, '#');
    if (strlen($surface_hex) !== 6) {
        return $fallback;
    }

    $rr = hexdec(substr($surface_hex, 0, 2));
    $gg = hexdec(substr($surface_hex, 2, 2));
    $bb = hexdec(substr($surface_hex, 4, 2));

    $brightness = ($rr * 299 + $gg * 587 + $bb * 114) / 1000;

    return $brightness > 150 ? '#111111' : '#ffffff';
}

add_action('wp_head', function () {

    $radius_style = get_theme_mod('hayden_radius_style', 'soft');
    $rad          = build_radius_vars($radius_style);

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

    $nav_toggle = compute_nav_toggle_contrast($surface, '#111111');

    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');
    $spacing        = build_spacing_vars($spacing_choice);

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

        /* Tailwind v4 radius tokens */
        --radius-sm: <?php echo esc_html($rad['sm']); ?>;
        --radius-md: <?php echo esc_html($rad['md']); ?>;
        --radius-lg: <?php echo esc_html($rad['lg']); ?>;
        --radius-xl: <?php echo esc_html($rad['xl']); ?>;
        --radius-2xl: <?php echo esc_html($rad['2xl']); ?>;
        --radius-full: <?php echo esc_html($rad['full']); ?>;

        /* Legacy alias */
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
 * Custom fonts in frontend + admin
 */
add_action('wp_head', function () {
    $css = get_custom_font_css();
    if ($css) {
        echo '<style id="hayden-custom-fonts">' . $css . '</style>';
    }
}, 50);

add_action('admin_head', function () {
    $css = get_custom_font_css();
    if ($css) {
        echo '<style id="hayden-custom-fonts-admin">' . $css . '</style>';
    }
}, 50);

/**
 * Editor: inject vars + fonts + spacing + Tailwind radius vars
 * (kept as-is for now; we'll dedupe in Phase 2)
 */
add_action('enqueue_block_editor_assets', function () {

    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    $surface = sanitize_hex_color(get_theme_mod('hayden_surface_color', '#FFFAF8')) ?: '#FFFAF8';

    $card_bg         = sanitize_hex_color(get_theme_mod('hayden_card_bg', '#000000')) ?: '#000000';
    $card_heading    = sanitize_hex_color(get_theme_mod('hayden_card_heading', '#f97316')) ?: '#f97316';
    $card_text       = sanitize_hex_color(get_theme_mod('hayden_card_text', '#ffffff')) ?: '#ffffff';
    $card_text_muted = sanitize_hex_color(get_theme_mod('hayden_card_text_muted', '#e5e5e5')) ?: '#e5e5e5';

    $spacing_choice = get_theme_mod('hayden_spacing_scale', 'comfortable');
    $spacing        = build_spacing_vars($spacing_choice);

    $radius_style = get_theme_mod('hayden_radius_style', 'soft');
    $rad          = build_radius_vars($radius_style);

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

    $font_css = get_custom_font_css();
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
