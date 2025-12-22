<?php

namespace App\Customizer;

/**
 * -------------------------------------------------------------------------
 * Live Preview (Customizer preview frame)
 * -------------------------------------------------------------------------
 *
 * Updates CSS variables instantly in the preview without refresh.
 * Uses a PHP-exported payload to avoid duplicating preset maps in JS.
 */

add_action('customize_preview_init', function (): void {
    wp_enqueue_script('customize-preview');

    $payload = live_preview_payload();

    $js_payload = wp_json_encode($payload);
    if (! $js_payload) {
        $js_payload = '{}';
    }

    $js = <<<JS
(function(api){
  if (!api) return;

  var payload = {$js_payload} || {};
  var radiusPresets  = payload.radius  || {};
  var spacingPresets = payload.spacing || {};

  // Map setting IDs -> CSS var names
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

  function setVar(name, value){
    if (!name || value === undefined || value === null) return;
    document.documentElement.style.setProperty(name, value);
  }

  function updateNavToggleFromSurface(hex){
    if (!hex) return;
    hex = ('' + hex).replace('#','');
    if (hex.length !== 6) return;

    var r = parseInt(hex.substring(0,2), 16);
    var g = parseInt(hex.substring(2,4), 16);
    var b = parseInt(hex.substring(4,6), 16);
    if (isNaN(r) || isNaN(g) || isNaN(b)) return;

    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    var toggleColor = brightness > 150 ? '#111111' : '#ffffff';
    setVar('--color-nav-toggle', toggleColor);
  }

  function applyRadius(style){
    var p = radiusPresets[style] || radiusPresets.soft;
    if (!p) return;

    setVar('--radius-sm',  p.sm);
    setVar('--radius-md',  p.md);
    setVar('--radius-lg',  p.lg);
    setVar('--radius-xl',  p.xl);
    setVar('--radius-2xl', p['2xl']);
    setVar('--radius-full', p.full);

    // legacy alias
    setVar('--radius-pill', p.full);
  }

  function applySpacing(choice){
    var p = spacingPresets[choice] || spacingPresets.comfortable;
    if (!p) return;

    setVar('--section-space-mobile',  p.mobile);
    setVar('--section-space-desktop', p.desktop);
  }

  // Bind all colour settings
  Object.keys(colorMap).forEach(function(settingId){
    var cssVar = colorMap[settingId];

    api(settingId, function(setting){
      var initial = setting.get();
      if (initial) {
        setVar(cssVar, initial);

        if (settingId === 'hayden_surface_color') {
          updateNavToggleFromSurface(initial);
        }

        if (settingId === 'hayden_widget_bg_color') {
          // keep surface-soft in sync with widget bg if your CSS uses it
          setVar('--color-surface-soft', initial);
        }
      }

      setting.bind(function(newVal){
        if (!newVal) return;
        setVar(cssVar, newVal);

        if (settingId === 'hayden_surface_color') {
          updateNavToggleFromSurface(newVal);
        }

        if (settingId === 'hayden_widget_bg_color') {
          setVar('--color-surface-soft', newVal);
        }
      });
    });
  });

  // Radius live preview
  api('hayden_radius_style', function(setting){
    applyRadius(setting.get());
    setting.bind(applyRadius);
  });

  // Spacing live preview
  api('hayden_spacing_scale', function(setting){
    applySpacing(setting.get());
    setting.bind(applySpacing);
  });

})(wp.customize);
JS;

    wp_add_inline_script('customize-preview', $js);
});
