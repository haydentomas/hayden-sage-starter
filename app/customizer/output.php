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

    $vars = build_css_vars();

    if (empty($vars)) {
        return;
    }

    $css = vars_to_css($vars, ':root');

    echo '<style id="hayden-theme-vars">' . $css . '</style>';

}, 50);
