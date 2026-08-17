<?php
defined( 'ABSPATH' ) || exit;

// Hooked to admin_print_footer_scripts, NOT admin_enqueue_scripts, and that is
// load-bearing. Carbon Fields enqueues carbon-fields-core.css and
// carbon-fields-metaboxes.css from `admin_print_footer_scripts` at priority 9
// (Loader::enqueue_assets), i.e. in the footer. Anything enqueued the normal way
// prints in the head and therefore loses to Carbon Fields at equal specificity —
// which it is, since both style .cf-complex__group-head as a single class.
// Running at priority 10 on the same hook puts this stylesheet immediately after
// theirs, so the overrides win without !important on every rule.
add_action( 'admin_print_footer_scripts', 'nsfc_admin_styles', 10 );
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
function nsfc_admin_styles() {
    // admin_print_footer_scripts passes no argument, so the screen comes from
    // the global the admin sets up rather than from a parameter.
    global $hook_suffix;

    $screens = [ 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

    // Carbon Fields theme-options pages (Settings → Financial Assistance) get a
    // generated hook suffix like
    // settings_page_crb_carbon_fields_container_financial_assistance — matched
    // by prefix so adding another options page doesn't need this list edited.
    $is_options_page = false !== strpos( $hook_suffix, 'crb_carbon_fields_container' );

    if ( ! $is_options_page && ! in_array( $hook_suffix, $screens, true ) ) {
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
