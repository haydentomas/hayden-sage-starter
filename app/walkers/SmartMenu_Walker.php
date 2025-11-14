<?php

namespace App\Walkers;

use Walker_Nav_Menu;

class SmartMenu_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $indent  = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sm-sub\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {

        $classes   = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'sm-nav-item';

        $has_children = in_array('menu-item-has-children', $classes, true);
        $class_names  = join(' ', array_filter($classes));
        $class_attr   = ' class="' . esc_attr($class_names) . '"';

        $output .= "<li{$class_attr}>";

        $link_class = 'sm-nav-link';
        if ($has_children) {
            $link_class .= ' sm-nav-link--split';
        }

        $atts = [
            'title'  => $item->attr_title ?: '',
            'target' => $item->target ?: '',
            'rel'    => $item->xfn ?: '',
            'href'   => $item->url ?: '',
            'class'  => $link_class,
        ];

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (! empty($value)) {
                $value       = $attr === 'href' ? esc_url($value) : esc_attr($value);
                $attributes .= " {$attr}=\"{$value}\"";
            }
        }

        $title       = apply_filters('the_title', $item->title, $item->ID);
        $item_output = "<a{$attributes}>{$title}</a>";

        if ($has_children) {
            $item_output .= '<button class="sm-nav-link sm-nav-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>';
        }

        $output .= $item_output;
    }

    public function end_el( &$output, $item, $depth = 0, $args = [] ) {
        $output .= "</li>\n";
    }
}
