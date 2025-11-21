<?php

namespace App\Walkers;

use Walker_Nav_Menu;

class SmartMenu_Walker extends Walker_Nav_Menu
{
    /**
     * ID of the mega parent currently being processed.
     * When not null, children are buffered into $megaBuffer
     * instead of being output as normal dropdowns.
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

        $indent = str_repeat("\t", $depth);
        $output .= "\n{$indent}<ul class=\"sm-sub\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        if ($this->currentMegaParent !== null) {
            return;
        }

        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    /**
     * Start element.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url   = $item->url ?: '#';

        $classes      = empty($item->classes) ? [] : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes, true);

        // Is this item marked as a mega parent (top-level only)?
        $is_mega_parent = ($depth === 0 && get_post_meta($item->ID, '_menu_item_mega_parent', true) === '1');

        // ---------- MEGA SUBTREE HANDLING ----------

        // If we're inside a mega parent (depth > 0), we do NOT output normal
        // SmartMenus markup. Instead, we push into $megaBuffer.
        if ($this->currentMegaParent !== null && $depth > 0) {
            // Depth 1 under mega: column wrapper + title + links list
            if ($depth === 1) {
                $this->megaBuffer .= '<div class="mega-col">';

                // Column title can be clickable if we want
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
                // We don't render a link for the column item itself beyond the title.
                return;
            }

            // Depth 2+ under mega: simple list items inside the column
            if ($depth >= 2) {
                $this->megaBuffer .= '<li class="sm-sub-item">';

                $atts = [
                    'href'  => $url,
                    'title' => $item->attr_title ?: '',
                    'target'=> $item->target ?: '',
                    'rel'   => $item->xfn ?: '',
                    'class' => 'sm-sub-link',
                ];

                $this->megaBuffer .= $this->build_link($title, $atts);

                // Children at depth >= 3 could be handled here if needed,
                // but for now we assume 2 levels inside mega is enough.
                return;
            }
        }

        // ---------- NORMAL (NON-MEGA) FLOW ----------

        // Build <li> classes
        if ($depth === 0) {
            $li_classes = ['sm-nav-item'];
            if ($is_mega_parent) {
                $li_classes[] = 'sm-nav-item--has-mega';
            }
        } else {
            $li_classes = ['sm-sub-item'];
        }

        $li_class_attr = ' class="' . esc_attr(implode(' ', $li_classes)) . '"';
        $output       .= "<li{$li_class_attr}>";

        // Common attributes (title, target, rel)
        $atts = [
            'title'  => $item->attr_title ?: '',
            'target' => $item->target ?: '',
            'rel'    => $item->xfn ?: '',
        ];

        /**
         * MEGA PARENT (top level only)
         * ----------------------------
         * <li class="sm-nav-item sm-nav-item--has-mega">
         *   <a class="sm-nav-link sm-sub-toggler" href="#">Mega</a>
         *   <div class="sm-sub sm-sub--mega" data-mega-cols="X">
         *     <div class="mega-grid mega-cols-X">
         *       <!-- columns from depth-1 children -->
         *     </div>
         *   </div>
         * </li>
         */
        if ($is_mega_parent && $depth === 0) {
            $this->currentMegaParent = (int) $item->ID;
            $this->megaBuffer        = '';

            $cols = (int) get_post_meta($item->ID, '_menu_item_mega_columns', true);
            if ($cols < 1 || $cols > 4) {
                $cols = 3;
            }
            $this->megaColumns = $cols;

            // Mega parent link acts as pure toggle (no real destination)
            $link_atts = $atts;
            $link_atts['href']  = '#';
            $link_atts['class'] = 'sm-nav-link sm-sub-toggler';

            $output .= $this->build_link($title, $link_atts);

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
                $link_atts = $atts;
                $link_atts['href']  = $url;
                $link_atts['class'] = 'sm-nav-link sm-nav-link--split';

                $output .= $this->build_link($title, $link_atts);

                $output .= '<button class="sm-nav-link sm-nav-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>';
            } else {
                // Simple single link
                $link_atts = $atts;
                $link_atts['href']  = $url;
                $link_atts['class'] = 'sm-nav-link';

                $output .= $this->build_link($title, $link_atts);
            }

            return;
        }

        /**
         * SUBMENU ITEMS (non-mega)
         */
        if ($has_children) {
            // Split sub-parent
            $link_atts = $atts;
            $link_atts['href']  = $url;
            $link_atts['class'] = 'sm-sub-link sm-sub-link--split';

            $output .= $this->build_link($title, $link_atts);

            $output .= '<button class="sm-sub-link sm-sub-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>';
        } else {
            // Simple sub-link
            $link_atts = $atts;
            $link_atts['href']  = $url;
            $link_atts['class'] = 'sm-sub-link';

            $output .= $this->build_link($title, $link_atts);
        }
    }

    /**
     * End element.
     */
    public function end_el(&$output, $item, $depth = 0, $args = [])
    {
        // Inside mega subtree:
        if ($this->currentMegaParent !== null && $depth > 0) {
            if ($depth === 1) {
                // Closing a column (we opened <div class="mega-col"><ul...>)
                $this->megaBuffer .= '</ul></div>'; // close .mega-col-links and .mega-col
            } elseif ($depth >= 2) {
                // Close a link item
                $this->megaBuffer .= '</li>';
            }

            return;
        }

        // Closing the mega parent: inject buffered columns and close wrappers
        if ($this->currentMegaParent !== null && $depth === 0 && $this->currentMegaParent === (int) $item->ID) {
            $output .= $this->megaBuffer . '</div></div></li>'; // close .mega-grid, .sm-sub--mega, and <li>

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
     * Helper to build an <a> tag from attributes.
     */
    protected function build_link(string $title, array $atts): string
    {
        $attributes = '';

        foreach ($atts as $attr => $value) {
            if ($value === '') {
                continue;
            }

            if ($attr === 'href') {
                $value = esc_url($value);
            } else {
                $value = esc_attr($value);
            }

            $attributes .= " {$attr}=\"{$value}\"";
        }

        return "<a{$attributes}>{$title}</a>";
    }
}
