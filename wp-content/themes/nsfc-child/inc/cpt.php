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
        // No 'excerpt' either, as of 2026-08-16 — the card blurb moved to the
        // `card_description` field so every field that matters lives in one
        // ordered box. Leaving the sidebar Excerpt panel in place would have
        // meant two fields for one job, with only one of them wired up.
        'supports'          => [ 'title', 'custom-fields' ],
        'rewrite'           => [ 'slug' => 'program', 'with_front' => false ],
    ] );

    // Venues CPT — the central list of places, so a venue is picked rather than
    // typed. Before this, venue was free text in four separate fields and 55
    // entries had already produced 8 different strings for ~5 real places
    // ("RCTC Field House" vs "RCTC Field House, 851 College Pkwy SE, Rochester").
    //
    // A CPT rather than a taxonomy, unlike program_location and camp_type: a
    // venue has to be selectable on individual repeater ROWS (a session's
    // meeting times, a sub-program's sessions), and a taxonomy can only attach
    // to a whole post.
    //
    // Not public — venues have no page of their own; they're referenced from
    // programs and camp sessions.
    register_post_type( 'venue', [
        'labels' => [
            'name'               => 'Venues',
            'singular_name'      => 'Venue',
            'add_new_item'       => 'Add New Venue',
            'edit_item'          => 'Edit Venue',
            'search_items'       => 'Search Venues',
            'not_found'          => 'No venues found.',
            'not_found_in_trash' => 'No venues found in trash.',
        ],
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_in_rest'      => false,
        'has_archive'       => false,
        'hierarchical'      => false,
        'menu_icon'         => 'dashicons-location',
        'menu_position'     => 26,
        'supports'          => [ 'title' ],
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
