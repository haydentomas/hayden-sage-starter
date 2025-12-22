<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'hayden'));
});





namespace App;

add_filter('wp_nav_menu_items', function ($items, $args) {
    // Only affect primary nav
    if (($args->theme_location ?? '') !== 'primary_navigation') {
        return $items;
    }

    // Only when the special header layout is active
    $layout = get_theme_mod('hayden_header_layout', 'default');
    if ($layout !== 'nav-center-cta') {
        return $items;
    }

    // Get CTA settings
    $label = get_theme_mod('hayden_header_cta_label', __('Start a Project', 'hayden'));
    $url   = get_theme_mod('hayden_header_cta_url', home_url('/start-a-project'));

    // Append a CTA item that is **mobile-only** (hidden on lg+)
    $items .= sprintf(
        '<li class="menu-item menu-item-cta lg:hidden mt-4">
            <a href="%1$s"
               class="block w-full text-center rounded-full px-4 py-2
                      font-semibold bg-primary text-white hover:bg-primary/90">
                %2$s
            </a>
         </li>',
        esc_url($url),
        esc_html($label)
    );

    return $items;
}, 10, 2);
