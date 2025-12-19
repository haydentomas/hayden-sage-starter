<?php

namespace App\Walkers;

use Walker_Nav_Menu;

class SmartMenu_Walker extends Walker_Nav_Menu
{
    /**
     * ID of the mega parent currently being processed.
     * When not null, children are buffered into $megaBuffer instead of output.
     */
    protected ?int $currentMegaParent = null;

    /**
     * Buffer for the current mega menu's inner HTML (columns + links).
     */
    protected string $megaBuffer = '';

    /**
     * Number of columns for the current mega parent.
     */
    protected int $megaColumns = 0;

    /**
     * Start submenu level.
     *
     * For normal menus we output <ul class="sm-sub">.
     * For mega menus we skip, because we build our own structure.
     */
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        if ($this->currentMegaParent !== null) {
            // Inside a mega subtree: no normal <ul> wrappers.
            return;
        }

        $indent = str_repeat("\t", (int) $depth);
        $output .= "\n{$indent}<ul class=\"sm-sub\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        if ($this->currentMegaParent !== null) {
            return;
        }

        $indent = str_repeat("\t", (int) $depth);
        $output .= "{$indent}</ul>\n";
    }

    /**
     * Start element.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $depth = (int) $depth;

        // Title can be filtered by WP/plugins.
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url   = $item->url ?: '#';

        // Prefer WP's has_children when provided, else fall back to class check.
        $has_children = false;
        if (is_object($args) && property_exists($args, 'has_children')) {
            $has_children = (bool) $args->has_children;
        } else {
            $classes      = empty($item->classes) ? [] : (array) $item->classes;
            $has_children = in_array('menu-item-has-children', $classes, true);
        }

        // Is this item marked as a mega parent (top-level only)?
        $is_mega_parent = ($depth === 0 && get_post_meta($item->ID, '_menu_item_mega_parent', true) === '1');

        /**
         * ----------------------------
         * MEGA SUBTREE HANDLING
         * ----------------------------
         *
         * When inside a mega parent (depth > 0), we buffer into $megaBuffer.
         */
        if ($this->currentMegaParent !== null && $depth > 0) {

            // Depth 1 under mega: column wrapper + title + links list
            if ($depth === 1) {
                $this->megaBuffer .= '<div class="mega-col">';

                if (! empty($url) && $url !== '#') {
                    $this->megaBuffer .= sprintf(
                        '<div class="mega-col-title"><a href="%s">%s</a></div>',
                        esc_url($url),
                        esc_html($title)
                    );
                } else {
                    $this->megaBuffer .= sprintf(
                        '<div class="mega-col-title">%s</div>',
                        esc_html($title)
                    );
                }

                $this->megaBuffer .= '<ul class="mega-col-links">';
                return;
            }

            // Depth 2+ under mega: list items inside the column
            if ($depth >= 2) {
                $this->megaBuffer .= '<li class="sm-sub-item">';

                $atts = [
                    'href'   => $url,
                    'title'  => $item->attr_title ?: '',
                    'target' => $item->target ?: '',
                    'rel'    => $item->xfn ?: '',
                    'class'  => 'sm-sub-link',
                ];

                $this->megaBuffer .= $this->build_link($title, $atts, $item, $args, $depth);

                // We assume 2 levels inside mega is enough for now.
                return;
            }
        }

        /**
         * ----------------------------
         * NORMAL (NON-MEGA) FLOW
         * ----------------------------
         */

        // Build <li> classes
        if ($depth === 0) {
            $li_classes = ['sm-nav-item'];
            if ($is_mega_parent) {
                $li_classes[] = 'sm-nav-item--has-mega';
            }
        } else {
            $li_classes = ['sm-sub-item'];
        }

        $output .= '<li class="' . esc_attr(implode(' ', $li_classes)) . '">';

        // Common attributes
        $atts = [
            'title'  => $item->attr_title ?: '',
            'target' => $item->target ?: '',
            'rel'    => $item->xfn ?: '',
        ];

        /**
         * MEGA PARENT (top-level only)
         */
        if ($is_mega_parent && $depth === 0) {
            $this->currentMegaParent = (int) $item->ID;
            $this->megaBuffer        = '';

            $cols = (int) get_post_meta($item->ID, '_menu_item_mega_columns', true);
            if ($cols < 1 || $cols > 4) {
                $cols = 3;
            }
            $this->megaColumns = $cols;

            // Mega parent link acts as toggle only (no destination)
            $link_atts          = $atts;
            $link_atts['href']  = '#';
            $link_atts['class'] = 'sm-nav-link sm-sub-toggler';

            $output .= $this->build_link($title, $link_atts, $item, $args, $depth);

            // Open mega wrapper; children will fill $megaBuffer
            $output .= sprintf(
                '<div class="sm-sub sm-sub--mega" data-mega-cols="%1$d"><div class="mega-grid mega-cols-%1$d">',
                $cols
            );

            return;
        }

        /**
         * TOP-LEVEL ITEMS (non-mega)
         */
        if ($depth === 0) {
            if ($has_children) {
                // Split parent
                $link_atts          = $atts;
                $link_atts['href']  = $url;
                $link_atts['class'] = 'sm-nav-link sm-nav-link--split';

                $output .= $this->build_link($title, $link_atts, $item, $args, $depth);

                $output .= '<button class="sm-nav-link sm-nav-link--split sm-sub-toggler" aria-label="' .
                    esc_attr__('Toggle sub menu', 'hayden') .
                    '"></button>';
            } else {
                // Simple single link
                $link_atts          = $atts;
                $link_atts['href']  = $url;
                $link_atts['class'] = 'sm-nav-link';

                $output .= $this->build_link($title, $link_atts, $item, $args, $depth);
            }

            return;
        }

        /**
         * SUBMENU ITEMS (non-mega)
         */
        if ($has_children) {
            // Split sub-parent
            $link_atts          = $atts;
            $link_atts['href']  = $url;
            $link_atts['class'] = 'sm-sub-link sm-sub-link--split';

            $output .= $this->build_link($title, $link_atts, $item, $args, $depth);

            $output .= '<button class="sm-sub-link sm-sub-link--split sm-sub-toggler" aria-label="' .
                esc_attr__('Toggle sub menu', 'hayden') .
                '"></button>';
        } else {
            // Simple sub-link
            $link_atts          = $atts;
            $link_atts['href']  = $url;
            $link_atts['class'] = 'sm-sub-link';

            $output .= $this->build_link($title, $link_atts, $item, $args, $depth);
        }
    }

    /**
     * End element.
     */
    public function end_el(&$output, $item, $depth = 0, $args = [])
    {
        $depth = (int) $depth;

        // Inside mega subtree:
        if ($this->currentMegaParent !== null && $depth > 0) {
            if ($depth === 1) {
                // Closing a column
                $this->megaBuffer .= '</ul></div>';
            } elseif ($depth >= 2) {
                // Close a link item
                $this->megaBuffer .= '</li>';
            }
            return;
        }

        // Closing the mega parent: inject buffered columns and close wrappers
        if ($this->currentMegaParent !== null && $depth === 0 && $this->currentMegaParent === (int) $item->ID) {
            $output .= $this->megaBuffer . '</div></div></li>';

            // Reset mega state
            $this->currentMegaParent = null;
            $this->megaBuffer        = '';
            $this->megaColumns       = 0;

            return;
        }

        // Normal items
        $output .= "</li>\n";
    }

    /**
     * Build an <a> tag from attributes.
     *
     * Adds WP compatibility filters and rel=noopener when target=_blank.
     */
    protected function build_link(string $title, array $atts, $item = null, $args = null, int $depth = 0): string
    {
        // Ensure rel is safe for external targets.
        if (! empty($atts['target']) && $atts['target'] === '_blank') {
            $rel = trim((string) ($atts['rel'] ?? ''));
            if ($rel === '') {
                $rel = 'noopener noreferrer';
            } elseif (! str_contains($rel, 'noopener')) {
                $rel .= ' noopener noreferrer';
            }
            $atts['rel'] = trim($rel);
        }

        /**
         * Allow plugins to adjust link attributes.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            $attr = (string) $attr;

            if ($attr === 'href') {
                $value = esc_url($value);
            } else {
                $value = esc_attr((string) $value);
            }

            $attributes .= " {$attr}=\"{$value}\"";
        }

        $safe_title = esc_html($title);

        $link_html = "<a{$attributes}>{$safe_title}</a>";

        /**
         * Allow plugins to filter the final <a> output.
         */
        return apply_filters('walker_nav_menu_start_el', $link_html, $item, $depth, $args);
    }
}
