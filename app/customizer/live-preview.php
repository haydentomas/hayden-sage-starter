<?php

namespace App\Customizer;

/**
 * Customizer controls styling + controls-frame JS
 */
add_action('customize_controls_enqueue_scripts', function () {

    wp_enqueue_style(
        'hayden-customizer-style',
        get_theme_file_uri('resources/css/customizer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    $primary = get_theme_mod('hayden_primary_color', '#f97316');
    wp_add_inline_style('hayden-customizer-style', sprintf(':root { --color-primary: %s; }', esc_attr($primary)));

    $controls_css = <<<CSS
#customize-controls .customize-control .customize-inside-control-row { color:#fff !important; }
#customize-controls .customize-control .description { color:#fafafa !important; }
CSS;
    wp_add_inline_style('hayden-customizer-style', $controls_css);

    $js = <<<JS
(function(api) {
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

  api('hayden_primary_color', function(setting) {
    function applyPrimary(val) {
      if (!val) val = '#f97316';
      var styleEl = document.getElementById('hayden-customizer-primary');
      if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'hayden-customizer-primary';
        document.head.appendChild(styleEl);
      }
      styleEl.textContent = ':root { --color-primary: ' + val + '; }';
    }
    applyPrimary(setting.get());
    setting.bind(applyPrimary);
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-controls', $js);
});

add_action('customize_controls_print_styles', function () {
    $primary = sanitize_hex_color(get_theme_mod('hayden_primary_color', '#f97316')) ?: '#f97316';
    ?>
    <style id="hayden-customizer-primary">
      :root { --color-primary: <?php echo esc_html($primary); ?>; }
    </style>
    <?php
});

/**
 * Live preview script – updates CSS vars in the PREVIEW frame
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('customize-preview');

    $js = <<<JS
(function(api) {
  if (!api) return;

  var colorMap = {
    hayden_primary_color: '--color-primary',
    hayden_surface_color: '--color-surface',
    hayden_heading_color: '--color-headings',
    hayden_body_color: '--color-body',
    hayden_body_muted_color: '--color-body-muted',

    hayden_footer_color: '--color-footer',
    hayden_footer_text_color: '--color-footer-text',

    hayden_widget_bg_color: '--color-widget-bg',
    hayden_widget_title_color: '--color-widget-heading',
    hayden_widget_text_color: '--color-widget-text',
    hayden_widget_link_color: '--color-widget-link',

    hayden_footer_widget_title_color: '--color-footer-widget-heading',
    hayden_footer_widget_text_color: '--color-footer-widget-text',
    hayden_footer_widget_link_color: '--color-footer-widget-link',

    hayden_card_bg: '--card-bg',
    hayden_card_heading: '--card-heading',
    hayden_card_text: '--card-text',
    hayden_card_text_muted: '--card-text-muted',

    hayden_nav_link_color: '--color-nav-link',
    hayden_nav_link_hover_color: '--color-nav-link-hover',
    hayden_nav_sub_bg_color: '--color-nav-sub-bg',
    hayden_nav_sub_link_color: '--color-nav-sub-link',
    hayden_nav_sub_hover_bg_color: '--color-nav-sub-hover-bg'
  };

  function setCssVar(name, value) {
    if (!name) return;
    document.documentElement.style.setProperty(name, value);
  }

  function updateNavToggleFromSurface(hex) {
    if (!hex) return;
    hex = ('' + hex).replace('#', '');
    if (hex.length !== 6) return;

    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);

    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    var toggleColor = brightness > 150 ? '#111111' : '#ffffff';
    setCssVar('--color-nav-toggle', toggleColor);
  }

  function applyRadiusPreset(style) {
    var presets = {
      sharp: { sm:'0rem', md:'0rem', lg:'0rem', xl:'0rem', '2xl':'0rem', full:'0rem' },
      soft:  { sm:'0.125rem', md:'0.375rem', lg:'0.5rem', xl:'0.75rem', '2xl':'1rem', full:'9999px' },
      round: { sm:'0.375rem', md:'0.5rem', lg:'0.75rem', xl:'1rem', '2xl':'1.25rem', full:'9999px' }
    };

    var p = presets[style] || presets.soft;

    setCssVar('--radius-sm', p.sm);
    setCssVar('--radius-md', p.md);
    setCssVar('--radius-lg', p.lg);
    setCssVar('--radius-xl', p.xl);
    setCssVar('--radius-2xl', p['2xl']);
    setCssVar('--radius-full', p.full);
    setCssVar('--radius-pill', p.full);
  }

  function applySpacingPreset(choice) {
    var presets = {
      compact:     { mobile:'1.75rem', desktop:'3rem' },
      comfortable: { mobile:'2.5rem',  desktop:'4rem' },
      spacious:    { mobile:'3.5rem',  desktop:'6rem' }
    };
    var p = presets[choice] || presets.comfortable;
    setCssVar('--section-space-mobile', p.mobile);
    setCssVar('--section-space-desktop', p.desktop);
  }

  Object.keys(colorMap).forEach(function(settingId) {
    var cssVar = colorMap[settingId];

    api(settingId, function(setting) {
      var initial = setting.get();
      if (initial) {
        setCssVar(cssVar, initial);

        if (settingId === 'hayden_surface_color') updateNavToggleFromSurface(initial);
        if (settingId === 'hayden_widget_bg_color') setCssVar('--color-surface-soft', initial);
      }

      setting.bind(function(newVal) {
        if (!newVal) return;
        setCssVar(cssVar, newVal);

        if (settingId === 'hayden_surface_color') updateNavToggleFromSurface(newVal);
        if (settingId === 'hayden_widget_bg_color') setCssVar('--color-surface-soft', newVal);
      });
    });
  });

  api('hayden_radius_style', function(setting) {
    applyRadiusPreset(setting.get());
    setting.bind(applyRadiusPreset);
  });

  api('hayden_spacing_scale', function(setting) {
    applySpacingPreset(setting.get());
    setting.bind(applySpacingPreset);
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-preview', $js);
});
