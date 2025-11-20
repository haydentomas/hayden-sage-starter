<?php

namespace App\Walkers;

use Walker_Nav_Menu;

class SmartMenu_Walker extends Walker_Nav_Menu
{
    public function start_lvl( &$output, $depth = 0, $args = [] )
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "\n{$indent}<ul class=\"sm-sub\" aria-hidden=\"true\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = [] )
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 )
    {
        $classes      = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_children = ! empty( $args->walker->has_children );
        $is_top_level = ( $depth === 0 );

        // Base li classes
        $classes[] = 'sm-nav-item';
        if ( $has_children && $is_top_level ) {
            $classes[] = 'menu-item-has-children';
        }

        $class_attr = ' class="' . esc_attr( implode( ' ', array_filter( array_unique( $classes ) ) ) ) . '"';
        $output    .= "<li{$class_attr}>";

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        // Link classes
        $link_classes = 'sm-nav-link';
        if ( $has_children && $is_top_level ) {
            $link_classes .= ' sm-nav-link--split sm-has-sub';
        }

        $atts = [
            'title'  => $item->attr_title ?: '',
            'target' => $item->target ?: '',
            'rel'    => $item->xfn ?: '',
            'href'   => $item->url ?: '',
            'class'  => $link_classes,
        ];

        $attr_str = '';
        foreach ( $atts as $attr => $value ) {
            if ( $value !== '' ) {
                $value    = ( $attr === 'href' ) ? esc_url( $value ) : esc_attr( $value );
                $attr_str .= " {$attr}=\"{$value}\"";
            }
        }

        $item_output = "<a{$attr_str}>{$title}</a>";

        // Split button toggler for top-level items with children
        if ( $has_children && $is_top_level ) {
            $item_output .= '<button class="sm-nav-link sm-nav-link--split sm-sub-toggler sm-has-sub" aria-label="Toggle sub menu"></button>';
        }

        $output .= $item_output;
    }

    public function end_el( &$output, $item, $depth = 0, $args = [] )
    {
        $output .= "</li>\n";
    }
}
