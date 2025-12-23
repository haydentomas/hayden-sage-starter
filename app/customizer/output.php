<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * Frontend CSS Variable Output
 * -------------------------------------------------------------------------
 *
 * Outputs ONE <style> block with all theme CSS variables.
 * Uses build_css_vars() from vars.php.
 */

add_action('wp_head', function (): void {

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

    // Single style block keeps the output predictable and easy to debug.
    echo '<style id="hayden-theme-vars">' . $css . '</style>';

}, 50);
