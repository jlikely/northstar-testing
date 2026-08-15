<?php
defined( 'ABSPATH' ) || exit;

add_action( 'restrict_manage_posts', 'nsfc_admin_location_filter' );
add_action( 'pre_get_posts', 'nsfc_admin_location_filter_query' );

/**
 * Post types that get the admin Location filter. Both carry the
 * `program_location` taxonomy (see nsfc_register_taxonomies()).
 */
function nsfc_admin_filter_post_types() {
    return [ 'program', 'camp-session' ];
}

/**
 * Render a Location dropdown above the Programs / Camp Sessions list tables.
 *
 * WP core's WP_Posts_List_Table::extra_tablenav() only auto-builds filters for
 * category, date, and post format — never custom taxonomies — so without this
 * there is no way to narrow either list by location.
 *
 * Options come from nsfc_location_term_options(), the same helper every
 * location dropdown in the theme uses, so adding a `program_location` term
 * makes it appear here with no code change.
 */
function nsfc_admin_location_filter( $post_type ) {
    if ( ! in_array( $post_type, nsfc_admin_filter_post_types(), true ) ) {
        return;
    }

    $options = nsfc_location_term_options();
    if ( empty( $options ) ) {
        return;
    }

    $selected = isset( $_GET['program_location'] ) ? sanitize_key( $_GET['program_location'] ) : '';

    echo '<label class="screen-reader-text" for="nsfc-filter-location">' .
        esc_html__( 'Filter by location', 'nsfc' ) . '</label>';
    echo '<select name="program_location" id="nsfc-filter-location">';
    printf(
        '<option value=""%s>%s</option>',
        selected( $selected, '', false ),
        esc_html__( 'All locations', 'nsfc' )
    );
    foreach ( $options as $slug => $label ) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr( $slug ),
            selected( $selected, $slug, false ),
            esc_html( $label )
        );
    }
    echo '</select>';
}

/**
 * Apply the Location filter to the admin list query.
 *
 * Handled explicitly rather than relying on WP's implicit $_GET → query-var
 * behaviour for taxonomy names, so the behaviour is predictable and stays
 * scoped to the two list tables above.
 */
function nsfc_admin_location_filter_query( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! in_array( $query->get( 'post_type' ), nsfc_admin_filter_post_types(), true ) ) {
        return;
    }

    $location = isset( $_GET['program_location'] ) ? sanitize_key( $_GET['program_location'] ) : '';
    if ( '' === $location ) {
        return;
    }

    $query->set( 'tax_query', [ [
        'taxonomy' => 'program_location',
        'field'    => 'slug',
        'terms'    => $location,
    ] ] );
}
