<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * Editor CSS Variables Output
 * -------------------------------------------------------------------------
 *
 * Injects the same CSS variables into the block editor so:
 * - colours match frontend
 * - spacing/radius match frontend
 *
 * Uses build_css_vars() from vars.php.
 */

add_action('enqueue_block_editor_assets', function (): void {

    $vars = build_css_vars();

    if (empty($vars)) {
        return;
    }

    $css = vars_to_css($vars, ':root');

    // Add to a handle that reliably exists in the editor.
    // wp-block-library is present; wp-block-library-theme may or may not be.
    wp_add_inline_style('wp-block-library', $css);

    if (wp_style_is('wp-block-library-theme', 'enqueued') || wp_style_is('wp-block-library-theme', 'registered')) {
        wp_add_inline_style('wp-block-library-theme', $css);
    }

    // If Sage editor handle exists, also apply there (safe optional).
    if (wp_style_is('sage/editor', 'enqueued') || wp_style_is('sage/editor', 'registered')) {
        wp_add_inline_style('sage/editor', $css);
    }
}, 20);
