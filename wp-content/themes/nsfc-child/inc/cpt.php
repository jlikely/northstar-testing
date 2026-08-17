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
        // No 'editor': a program's copy is all structured fields, and the block
        // canvas was a trap — single-program.php wrapped get_the_content() in a
        // <p class="lead"> without running block filters, so any real block
        // produced a <p> inside a <p>, and the whole thing was discarded on any
        // program with a structured schedule. The two posts that used it were
        // moved to the `description` field (2026-08-16).
        // No 'thumbnail': no template in this theme renders a featured image.
        // 'excerpt' stays — it's the program's card description on the season
        // landing pages, and 14 of 16 programs rely on it.
        'supports'          => [ 'title', 'excerpt', 'custom-fields' ],
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
        // No 'editor' or 'excerpt': a camp session is never rendered as a
        // single — it appears as a card and a modal on camps-season.php, both
        // built entirely from Carbon Fields meta plus its camp_type term's
        // description. Nothing in the theme reads a camp session's content or
        // excerpt, and no camp session had either. Both boxes were removed
        // (2026-08-16) so the editor only shows fields that do something.
        'supports'          => [ 'title', 'custom-fields' ],
        'rewrite'           => [ 'slug' => 'camps', 'with_front' => false ],
    ] );
}
