<?php
defined( 'ABSPATH' ) || exit;

// Load Carbon Fields
add_action( 'after_setup_theme', function () {
    $autoload = get_template_directory() . '/../../../vendor/autoload.php';
    $child_autoload = get_stylesheet_directory() . '/../../../vendor/autoload.php';

    if ( file_exists( $child_autoload ) ) {
        require_once $child_autoload;
    } elseif ( file_exists( $autoload ) ) {
        require_once $autoload;
    }

    if ( class_exists( '\Carbon_Fields\Carbon_Fields' ) ) {
        \Carbon_Fields\Carbon_Fields::boot();
    }
} );

// Load inc/ files
require_once get_stylesheet_directory() . '/inc/cpt.php';
require_once get_stylesheet_directory() . '/inc/taxonomies.php';
require_once get_stylesheet_directory() . '/inc/carbon-fields.php';
require_once get_stylesheet_directory() . '/inc/location-data.php';
require_once get_stylesheet_directory() . '/inc/dates.php';
require_once get_stylesheet_directory() . '/inc/admin-filters.php';
require_once get_stylesheet_directory() . '/inc/admin-ui.php';
require_once get_stylesheet_directory() . '/inc/breadcrumbs.php';

// Enqueue parent theme styles + Bootstrap 5 (via CDN — all templates are built with Bootstrap utility classes)
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'nsfc-parent',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme( 'kadence' )->get( 'Version' )
    );
    wp_enqueue_style(
        'bootstrap5',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        [ 'nsfc-parent' ],
        '5.3.3'
    );
    wp_enqueue_style(
        'nsfc-child',
        get_stylesheet_uri(),
        [ 'nsfc-parent', 'bootstrap5' ],
        wp_get_theme()->get( 'Version' )
    );
    wp_enqueue_script(
        'bootstrap5-bundle',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.3',
        true
    );
} );

// Disable wpautop — all page content is hand-written Bootstrap HTML (no Gutenberg
// blocks), and wpautop mangles pre-formatted block-level markup by injecting stray
// <p>/</p> tags around it.
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

// Register nav menu locations
add_action( 'after_setup_theme', function () {
    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'nsfc' ),
        'footer'  => __( 'Footer Navigation', 'nsfc' ),
    ] );
} );

/**
 * The Financial assistance section's content, from Settings → Financial
 * Assistance (registered in inc/carbon-fields.php).
 *
 * **This is the single read point for that content** — every template goes
 * through here. It is currently one shared set for all four locations, which is
 * the deliberate starting point. If a location ever needs its own wording, take
 * a location slug here and resolve the override inside this function; no
 * template will need touching.
 *
 * Replaced the old `nsfc_financial_aid_steps` / `nsfc_financial_aid_note`
 * wp_options, which had no admin UI and stored the steps as a JSON string.
 */
function nsfc_financial_aid() {
    $steps = [];
    foreach ( (array) carbon_get_theme_option( 'nsfc_fa_steps' ) as $row ) {
        $step = trim( (string) ( $row['step'] ?? '' ) );
        if ( '' !== $step ) {
            $steps[] = $step;
        }
    }

    return [
        'heading' => carbon_get_theme_option( 'nsfc_fa_heading' ) ?: 'Financial assistance',
        'intro'   => carbon_get_theme_option( 'nsfc_fa_intro' ),
        'steps'   => $steps,
        'note'    => carbon_get_theme_option( 'nsfc_fa_note' ),
    ];
}
