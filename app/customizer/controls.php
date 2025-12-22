<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * Customizer Controls UI (left panel) styling + small JS helpers
 * -------------------------------------------------------------------------
 *
 * This affects ONLY the Customizer controls frame (not the preview).
 * Keeps the Customizer branded (dark UI + primary hover colour).
 */

add_action('customize_controls_enqueue_scripts', function (): void {

    $handle = 'hayden-customizer-style';

    // 1) Enqueue customizer stylesheet (controls frame)
    wp_enqueue_style(
        $handle,
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // 2) Make primary colour available as a CSS var in the controls frame
    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';

    // Attach inline styles to OUR handle (guaranteed enqueued above)
    wp_add_inline_style($handle, ':root { --color-primary: ' . esc_html($primary) . '; }');

    // 3) Container width label helper (controls frame only)
    $js = <<<JS
(function(api) {
  if (!api) return;

  function updateWidthValue(val) {
    var el = document.getElementById('hayden-container-width-value');
    if (el) el.textContent = val + 'px';
  }

  api('hayden_container_width', function(setting) {
    updateWidthValue(setting.get());
    setting.bind(function(newVal) { updateWidthValue(newVal); });
  });

  document.addEventListener('input', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('hayden-container-width-range')) {
      updateWidthValue(e.target.value);
    }
  });

  // Live update primary colour variable in controls frame UI
  api('hayden_primary_color', function(setting) {
    function applyPrimary(val) {
      if (!val) val = '#f97316';
      document.documentElement.style.setProperty('--color-primary', val);
    }
    applyPrimary(setting.get());
    setting.bind(applyPrimary);
  });

})(wp.customize);
JS;

    // Put JS on a core Customizer script handle that exists
    wp_add_inline_script('customize-controls', $js);
});
