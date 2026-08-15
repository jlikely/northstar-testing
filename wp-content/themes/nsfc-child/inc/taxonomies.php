<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'nsfc_register_taxonomies' );

function nsfc_register_taxonomies() {

    $post_types = [ 'program', 'camp-session' ];

    // Season
    register_taxonomy( 'season', $post_types, [
        'labels' => [
            'name'          => 'Seasons',
            'singular_name' => 'Season',
            'search_items'  => 'Search Seasons',
            'all_items'     => 'All Seasons',
            'edit_item'     => 'Edit Season',
            'add_new_item'  => 'Add New Season',
        ],
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'season' ],
    ] );

    // Program Level (competitive / recreational)
    register_taxonomy( 'program_level', $post_types, [
        'labels' => [
            'name'          => 'Levels',
            'singular_name' => 'Level',
            'search_items'  => 'Search Levels',
            'all_items'     => 'All Levels',
            'edit_item'     => 'Edit Level',
            'add_new_item'  => 'Add New Level',
        ],
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'level' ],
    ] );

    // Location
    register_taxonomy( 'program_location', $post_types, [
        'labels' => [
            'name'          => 'Locations',
            'singular_name' => 'Location',
            'search_items'  => 'Search Locations',
            'all_items'     => 'All Locations',
            'edit_item'     => 'Edit Location',
            'add_new_item'  => 'Add New Location',
        ],
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'location' ],
    ] );

    // Camp Type — camp-session only. Was a fixed Select field; converted to
    // a taxonomy so each type's description (see inc/carbon-fields.php's
    // term-meta box) is admin-editable and shared across every session of
    // that type, and so adding a brand-new camp type is "add a term," not a
    // code change.
    register_taxonomy( 'camp_type', [ 'camp-session' ], [
        'labels' => [
            'name'          => 'Camp Types',
            'singular_name' => 'Camp Type',
            'search_items'  => 'Search Camp Types',
            'all_items'     => 'All Camp Types',
            'edit_item'     => 'Edit Camp Type',
            'add_new_item'  => 'Add New Camp Type',
        ],
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'camp-type' ],
    ] );
}

// Seed default taxonomy terms on activation
function nsfc_seed_taxonomy_terms() {
    $seasons   = [ 'spring-summer' => 'Spring/Summer', 'fall' => 'Fall', 'winter' => 'Winter' ];
    $levels    = [ 'competitive' => 'Competitive', 'recreational' => 'Recreational' ];
    $locations = [ 'rochester' => 'Rochester' ];
    $camp_types = [
        'recreational'      => 'Recreational',
        'technical'         => 'Technical',
        'evening-technical' => 'Evening Technical',
        'midfielder'        => 'Midfielder',
        'defender'          => 'Defender',
        'forward'           => 'Forward',
        'goalkeeper'        => 'Goalkeeper',
        'world-cup'         => 'World Cup',
        'evening-world-cup' => 'Evening World Cup',
        'santos-futsal'     => 'Santos Futsal',
        'futsal'            => 'Futsal',
        'evening-finishing' => 'Evening Finishing',
        'fall-preseason'    => 'Fall Preseason',
    ];

    foreach ( $seasons as $slug => $name ) {
        if ( ! term_exists( $slug, 'season' ) ) {
            wp_insert_term( $name, 'season', [ 'slug' => $slug ] );
        }
    }
    foreach ( $levels as $slug => $name ) {
        if ( ! term_exists( $slug, 'program_level' ) ) {
            wp_insert_term( $name, 'program_level', [ 'slug' => $slug ] );
        }
    }
    foreach ( $locations as $slug => $name ) {
        if ( ! term_exists( $slug, 'program_location' ) ) {
            wp_insert_term( $name, 'program_location', [ 'slug' => $slug ] );
        }
    }
    foreach ( $camp_types as $slug => $name ) {
        if ( ! term_exists( $slug, 'camp_type' ) ) {
            wp_insert_term( $name, 'camp_type', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', 'nsfc_seed_taxonomy_terms', 20 );
