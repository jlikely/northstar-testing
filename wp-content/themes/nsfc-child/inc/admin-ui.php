<?php
defined( 'ABSPATH' ) || exit;

add_action( 'admin_enqueue_scripts', 'nsfc_admin_styles' );
add_filter( 'rest_prepare_program', 'nsfc_hide_inline_term_creation' );
add_filter( 'rest_prepare_camp-session', 'nsfc_hide_inline_term_creation' );

/**
 * The four fixed vocabularies, used by the term-screen tweaks below.
 */
const NSFC_FIXED_TAXONOMIES = [ 'season', 'program_level', 'program_location', 'camp_type' ];

foreach ( NSFC_FIXED_TAXONOMIES as $nsfc_taxonomy ) {
    add_filter( "manage_edit-{$nsfc_taxonomy}_columns", 'nsfc_hide_term_description_column' );
}
unset( $nsfc_taxonomy );

/**
 * Load assets/admin.css on the screens that render Carbon Fields boxes.
 *
 * Post/page editors carry the post_meta containers; the term screens carry the
 * Camp Type Description term_meta container. Nothing else in wp-admin needs it,
 * so it isn't loaded globally.
 *
 * Versioned by the file's own mtime rather than the theme version: this is an
 * admin-only stylesheet that gets tweaked far more often than style.css gets a
 * version bump, and stale-cached admin CSS looks exactly like an edit that
 * didn't apply.
 */
function nsfc_admin_styles( $hook ) {
    $screens = [ 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

    if ( ! in_array( $hook, $screens, true ) ) {
        return;
    }

    $path = get_stylesheet_directory() . '/assets/admin.css';

    wp_enqueue_style(
        'nsfc-admin',
        get_stylesheet_directory_uri() . '/assets/admin.css',
        [],
        file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' )
    );
}

/**
 * Drop the "Add New Season / Level / Location / Camp Type" form from the
 * taxonomy panels in the Program and Camp Session editors. The checkboxes
 * themselves are untouched — only the inline create form goes away.
 *
 * All four are fixed vocabularies. Terms belong on their own admin screens
 * (Programs → Locations, Camp Sessions → Camp Types), which are the only place
 * that offers a Slug field, and where adding a location is step 1 of 4 in
 * documentation/adding-a-location.md. Created inline from a post instead, you
 * get an auto-slugged term that immediately shows up in every location
 * dropdown and admin filter on the site with no Location Hub page behind it.
 *
 * Core gates that form on the post's `wp:action-create-{taxonomy}` REST link,
 * added by WP_REST_Posts_Controller::get_available_actions() for anyone who
 * can `edit_terms`. Removing the link rather than the capability leaves the
 * term admin screens fully usable.
 */
function nsfc_hide_inline_term_creation( $response ) {
    foreach ( NSFC_FIXED_TAXONOMIES as $taxonomy ) {
        $response->remove_link( 'https://api.w.org/action-create-' . $taxonomy );
    }

    return $response;
}

/**
 * Drop the Description column from the Season / Level / Location / Camp Type
 * term list tables.
 *
 * The Description *field* is hidden in assets/admin.css (nothing in the theme
 * reads a term description), so the column would only ever be empty. Filtering
 * the column out here rather than hiding it in CSS also removes it from Screen
 * Options, where a CSS-hidden column would still be offered as a toggle.
 */
function nsfc_hide_term_description_column( $columns ) {
    unset( $columns['description'] );

    return $columns;
}
