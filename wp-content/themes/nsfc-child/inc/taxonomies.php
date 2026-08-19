<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'nsfc_register_taxonomies' );

function nsfc_register_taxonomies() {

    $post_types = [ 'program', 'camp-session' ];

    // All four get `show_in_menu => false` and a top-level menu item registered
    // by hand in inc/admin-ui.php. Core adds a taxonomy submenu under EVERY post
    // type it's attached to (wp-admin/menu.php), so Locations appeared three
    // times (Programs, Camp Sessions, Venues) and Seasons and Levels twice each.
    // They now sit alongside Venues and Age Groups as what they actually are:
    // the field-management lists that Programs and Camp Sessions draw from,
    // rather than properties of either one.
    //
    // None of the four is publicly queryable and none has a rewrite. They were
    // registered `public => true` with rewrite slugs, which gave every term a
    // real front-end archive: /location/rochester/ returned 200 and rendered a
    // Kadence archive titled "Rochester", competing with the actual location hub
    // at /youth-soccer/rochester/. No template ever existed for them and nothing
    // in the theme links to them — they were an accident of registration. It
    // also put a "View" row action on every term pointing at that page.
    //
    // show_ui is set explicitly because it defaults to `public`, and query_var
    // is kept so the admin list-table filters (edit.php?post_type=x&taxonomy=y)
    // still work.
    //
    // All four taxonomies are registered hierarchical so the block editor
    // renders a checkbox list (HierarchicalTermSelector) rather than the
    // free-text token field it uses for flat taxonomies. None of them
    // actually nest — this is purely about the editing UI, and about not
    // letting a typo silently create a new term, which is how `austin` /
    // `albert-lea` / `winona` originally got slug-style display names.
    // Changing this flag does not touch stored terms or assignments.

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
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        // Checkboxes only — no inline "Add New" form. See
        // nsfc_taxonomy_checkbox_meta_box() in inc/admin-ui.php.
        'meta_box_cb'  => 'nsfc_taxonomy_checkbox_meta_box',
        'rewrite'            => false,
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
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        // Checkboxes only — no inline "Add New" form. See
        // nsfc_taxonomy_checkbox_meta_box() in inc/admin-ui.php.
        'meta_box_cb'  => 'nsfc_taxonomy_checkbox_meta_box',
        'rewrite'            => false,
    ] );

    // Location — also attached to `venue`, so each location owns its venues and
    // program_location stays the single source of truth for which locations exist.
    //
    register_taxonomy( 'program_location', array_merge( $post_types, [ 'venue' ] ), [
        'labels' => [
            'name'          => 'Locations',
            'singular_name' => 'Location',
            'search_items'  => 'Search Locations',
            'all_items'     => 'All Locations',
            'edit_item'     => 'Edit Location',
            'add_new_item'  => 'Add New Location',
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'query_var'          => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_in_menu' => false,
        // Checkboxes only — no inline "Add New" form. See
        // nsfc_taxonomy_checkbox_meta_box() in inc/admin-ui.php.
        'meta_box_cb'  => 'nsfc_taxonomy_checkbox_meta_box',
        'rewrite'            => false,
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
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        // Checkboxes only — no inline "Add New" form. See
        // nsfc_taxonomy_checkbox_meta_box() in inc/admin-ui.php.
        'meta_box_cb'  => 'nsfc_taxonomy_checkbox_meta_box',
        'rewrite'            => false,
    ] );
}

// Seed default taxonomy terms on activation
function nsfc_seed_taxonomy_terms() {
    $seasons   = [ 'spring-summer' => 'Spring/Summer', 'fall' => 'Fall', 'winter' => 'Winter' ];
    $levels    = [ 'competitive' => 'Competitive', 'recreational' => 'Recreational' ];
    $locations = [
        'rochester'  => 'Rochester',
        'austin'     => 'Austin',
        'albert-lea' => 'Albert Lea',
        'winona'     => 'Winona',
    ];
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
