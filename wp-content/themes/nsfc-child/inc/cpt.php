<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'nsfc_register_cpts' );

function nsfc_register_cpts() {

    // Programs CPT
    register_post_type( 'program', [
        'labels' => [
            'name'               => 'Programs',
            'singular_name'      => 'Program',
            'add_new_item'       => 'Add New Program',
            'edit_item'          => 'Edit Program',
            'view_item'          => 'View Program',
            'search_items'       => 'Search Programs',
            'not_found'          => 'No programs found.',
            'not_found_in_trash' => 'No programs found in trash.',
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'has_archive'       => false,
        'hierarchical'      => false,
        'menu_icon'         => 'dashicons-clipboard',
        'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'rewrite'           => [ 'slug' => 'program', 'with_front' => false ],
    ] );

    // Camp Sessions CPT
    register_post_type( 'camp-session', [
        'labels' => [
            'name'               => 'Camp Sessions',
            'singular_name'      => 'Camp Session',
            'add_new_item'       => 'Add New Camp Session',
            'edit_item'          => 'Edit Camp Session',
            'view_item'          => 'View Camp Session',
            'search_items'       => 'Search Camp Sessions',
            'not_found'          => 'No camp sessions found.',
            'not_found_in_trash' => 'No camp sessions found in trash.',
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_icon'         => 'dashicons-calendar-alt',
        'supports'          => [ 'title', 'editor', 'excerpt', 'custom-fields' ],
        'rewrite'           => [ 'slug' => 'camps', 'with_front' => false ],
    ] );
}
