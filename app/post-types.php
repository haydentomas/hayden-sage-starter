<?php

namespace App;

/**
 * Register Project custom post type.
 */
add_action('init', function () {
    // CPT: Projects
    register_post_type('project', [
        'labels' => [
            'name'          => __('Projects', 'hayden'),
            'singular_name' => __('Project', 'hayden'),
        ],
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-portfolio',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite'      => ['slug' => 'project'],
    ]);

    // Taxonomy: Technology Stack
    register_taxonomy('project_tech', ['project'], [
        'labels' => [
            'name'          => __('Technology Stack', 'hayden'),
            'singular_name' => __('Technology', 'hayden'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite'      => ['slug' => 'tech'],
    ]);
});
