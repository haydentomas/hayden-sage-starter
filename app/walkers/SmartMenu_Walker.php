<?php

namespace App\Walkers;

use Walker_Nav_Menu;

class SmartMenu_Walker extends Walker_Nav_Menu
{
    /**
     * ID of the mega parent currently being rendered (top-level item).
     */
    protected ?int $currentMegaParent = null;

    /**
     * Are we currently inside a mega menu branch?
     */
    protected bool $inMega = false;

    /**
     * Start level (submenu wrapper).
     */
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = str_repeat("\t", $depth);

        // Depth 0 sub for a mega parent – main panel wrapper
        if ($depth === 0 && $this->inMega && $this->currentMegaParent) {
            $cols = (int) get_post_meta($this->currentMegaParent, '_menu_item_mega_columns', true);
            if ($cols < 1 || $cols > 4) {
                $cols = 3;
            }

            $output .= "\n{$indent}<ul class=\"sm-sub sm-sub--mega\" data-mega-cols=\"{$cols}\">\n";
            return;
        }

        // Depth 1 inside mega → links list within a column
        if ($depth === 1 && $this->inMega) {
            $output .= "\n{$indent}<ul class=\"mega-col-links\">\n";
            return;
        }

        // Normal SmartMenus submenu
        $output .= "\n{$indent}<ul class=\"sm-sub\">\n";
    }

    /**
     * End submenu level.
     */
    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    /**
     * Start element.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $classes   = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'sm-nav-item';

        $has_children = in_array('menu-item-has-children', $classes, true);

        // Is this a top-level mega parent?
        $isMegaParent = ($depth === 0 && get_post_meta($item->ID, '_menu_item_mega_parent', true) === '1');

        if ($isMegaParent) {
            $classes[] = 'sm-nav-item--has-mega';
            $this->currentMegaParent = (int) $item->ID;
            $this->inMega = true;
        }

        // Depth 1 inside mega → treat as column wrapper
        if ($this->inMega && $depth === 1) {
            $classes[] = 'mega-col';
        }

        $class_names = join(' ', array_filter($classes));
        $class_attr  = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= "<li{$class_attr}>";

        // Build link / label
        $link_class = 'sm-nav-link';

        if ($has_children && ! $isMegaParent) {
            // Normal dropdown with split button
            $link_class .= ' sm-nav-link--split';
        }

        // For mega parent we still want the label to look like a normal nav link
        if ($isMegaParent) {
            $link_class .= ' sm-nav-link--split sm-has-sub';
        }

        $atts = [
            'title'  => $item->attr_title ?: '',
            'target' => $item->target ?: '',
            'rel'    => $item->xfn ?: '',
            'href'   => $isMegaParent ? '#' : ($item->url ?: ''),
            'class'  => $link_class,
        ];

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if ($value === '') {
                continue;
            }
            $value       = ($attr === 'href') ? esc_url($value) : esc_attr($value);
            $attributes .= " {$attr}=\"{$value}\"";
        }

        $title = apply_filters('the_title', $item->title, $item->ID);

        // -------- Output for different depths / contexts -------- //

        // TOP LEVEL
        if ($depth === 0) {
            // Mega parent – label acts as toggle
            if ($isMegaParent) {
                $output .= "<a{$attributes}>{$title}</a>";
                // No extra button; SmartMenus will use the .sm-sub-toggler link
            } else {
                // Normal top-level item
                $output .= "<a{$attributes}>{$title}</a>";

                if ($has_children) {
                    $output .= '<button class="sm-nav-link sm-nav-link--split sm-sub-toggler sm-has-sub" aria-label="Toggle sub menu"></button>';
                }
            }

            return;
        }

        // DEPTH 1 inside mega = column heading
        if ($this->inMega && $depth === 1) {
            $output .= '<div class="mega-col-inner">';
            $output .= '<div class="mega-col-title">' . esc_html($title) . '</div>';
            // Children (depth 2) will be rendered inside <ul class="mega-col-links">
            return;
        }

        // DEPTH >= 1 normal dropdown OR depth 2 links inside mega
        $output .= "<a{$attributes}>{$title}</a>";
    }

    /**
     * End element.
     */
    public function end_el(&$output, $item, $depth = 0, $args = [])
    {
        // Close column wrapper when leaving depth 1 inside a mega
        if ($this->inMega && $depth === 1) {
            $output .= "</div></li>\n"; // .mega-col-inner + <li>
        } else {
            $output .= "</li>\n";
        }

        // Leaving a top-level item → reset mega flags
        if ($depth === 0 && $this->currentMegaParent === (int) $item->ID) {
            $this->currentMegaParent = null;
            $this->inMega = false;
        }
    }
}
