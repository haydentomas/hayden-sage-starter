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

    // Global design tokens (colours/spacing/radius etc.)
    $vars     = build_css_vars();
    $vars_css = empty($vars) ? '' : vars_to_css($vars, ':root');

    // Custom uploaded fonts + :root font stacks (if set)
    $font_css = get_custom_font_css();

    // Nothing to output.
    if ($vars_css === '' && $font_css === '') {
        return;
    }

    $css = trim($vars_css . "\n" . $font_css);

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
