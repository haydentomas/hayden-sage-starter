/**
 * Live-preview for colour settings.
 * Runs only in the Customizer preview iframe.
 */
(function (wp) {
  if (!wp || !wp.customize) return;

  /**
   * Map of Customizer setting IDs -> CSS custom properties
   */
  const colorMap = {
    // Global theme colours
    hayden_primary_color: '--color-primary',
    hayden_surface_color: '--color-surface',
    hayden_heading_color: '--color-headings',
    hayden_body_color: '--color-body',
    hayden_body_muted_color: '--color-body-muted',

    // Footer colours
    hayden_footer_color: '--color-footer',
    hayden_footer_text_color: '--color-footer-text',
    hayden_footer_border_color: '--color-footer-border',

    // Widget colours
    hayden_widget_bg_color: '--color-widget-bg',
    hayden_widget_title_color: '--color-widget-heading',
    hayden_widget_text_color: '--color-widget-text',
    hayden_widget_link_color: '--color-widget-link',

    // Footer widget colours
    hayden_footer_widget_title_color: '--color-footer-widget-heading',
    hayden_footer_widget_text_color: '--color-footer-widget-text',
    hayden_footer_widget_link_color: '--color-footer-widget-link',

    // Card colours
    hayden_card_bg: '--card-bg',
    hayden_card_heading: '--card-heading',
    hayden_card_text: '--card-text',
    hayden_card_text_muted: '--card-text-muted',

    // Nav colours
    hayden_nav_link_color: '--color-nav-link',
    hayden_nav_link_hover_color: '--color-nav-link-hover',
    hayden_nav_sub_bg_color: '--color-nav-sub-bg',
    hayden_nav_sub_link_color: '--color-nav-sub-link',
    hayden_nav_sub_hover_bg_color: '--color-nav-sub-hover-bg',
  };

  /**
   * Helper to update a CSS variable on :root
   */
  function setCssVar(name, value) {
    if (!name || !value) return;
    document.documentElement.style.setProperty(name, value);
  }

  /**
   * Recalculate nav toggle contrast from surface colour, to mirror the PHP logic.
   */
  function updateNavToggleFromSurface(hex) {
    if (!hex) return;

    hex = hex.replace('#', '');
    if (hex.length !== 6) return;

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    const toggleColor = brightness > 150 ? '#111111' : '#ffffff';

    setCssVar('--color-nav-toggle', toggleColor);
  }

  /**
   * Wire up all mapped colour settings.
   */
  Object.keys(colorMap).forEach((settingId) => {
    const varName = colorMap[settingId];

    wp.customize(settingId, function (value) {
      value.bind(function (newVal) {
        setCssVar(varName, newVal);

        // Keep nav toggle contrast in sync when background changes
        if (settingId === 'hayden_surface_color') {
          updateNavToggleFromSurface(newVal);
        }
      });
    });
  });
})(window.wp);
