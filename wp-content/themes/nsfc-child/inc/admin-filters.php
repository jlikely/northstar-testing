<?php
defined( 'ABSPATH' ) || exit;

add_action( 'restrict_manage_posts', 'nsfc_admin_location_filter' );
add_action( 'pre_get_posts', 'nsfc_admin_location_filter_query' );

/**
 * Post types that get the admin Location filter. All three carry the
 * `program_location` taxonomy (see nsfc_register_taxonomies()).
 *
 * `venue` was added 2026-08-18, when venues became a CPT — a list of venues is
 * exactly the place you want to narrow to one location, and it was the only one
 * of the three missing the filter.
 */
function nsfc_admin_filter_post_types() {
    return [ 'program', 'camp-session', 'venue' ];
}

/**
 * Which taxonomies get a dropdown above the list tables, in display order.
 *
 * A taxonomy is only rendered for post types it's actually attached to, so the
 * Venues list shows Location alone while Programs and Camp Sessions show
 * Location and Level.
 *
 * Season and Camp Type are deliberately not here yet — the query side
 * (nsfc_admin_location_filter_query) already handles all four, so adding either
 * is one entry.
 */
function nsfc_admin_filter_taxonomies() {
    return [ 'program_location', 'program_level' ];
}

/**
 * Render the filter dropdowns above the Programs / Camp Sessions / Venues lists.
 *
 * WP core's WP_Posts_List_Table::extra_tablenav() only auto-builds filters for
 * category, date, and post format — never custom taxonomies — so without this
 * there is no way to narrow these lists at all.
 */
function nsfc_admin_location_filter( $post_type ) {
    if ( ! in_array( $post_type, nsfc_admin_filter_post_types(), true ) ) {
        return;
    }

    foreach ( nsfc_admin_filter_taxonomies() as $taxonomy ) {
        if ( ! is_object_in_taxonomy( $post_type, $taxonomy ) ) {
            continue;
        }

        $tax = get_taxonomy( $taxonomy );
        $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
        if ( is_wp_error( $terms ) || ! $terms ) {
            continue;
        }

        $selected = isset( $_GET[ $taxonomy ] ) ? sanitize_key( wp_unslash( $_GET[ $taxonomy ] ) ) : '';

        printf(
            '<label class="screen-reader-text" for="nsfc-filter-%1$s">%2$s</label>',
            esc_attr( $taxonomy ),
            esc_html( $tax->labels->all_items )
        );
        printf( '<select name="%1$s" id="nsfc-filter-%1$s">', esc_attr( $taxonomy ) );
        printf(
            '<option value=""%s>%s</option>',
            selected( $selected, '', false ),
            esc_html( $tax->labels->all_items )
        );
        foreach ( $terms as $term ) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr( $term->slug ),
                selected( $selected, $term->slug, false ),
                esc_html( $term->name )
            );
        }
        echo '</select>';
    }
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

    $post_type = $query->get( 'post_type' );
    if ( ! $post_type || ! is_string( $post_type ) ) {
        return;
    }

    // Every one of our taxonomies, not just location. The four are registered
    // non-public (they had accidental front-end archives), and WordPress forces
    // `query_var` to false for non-public taxonomies — see WP_Taxonomy, "Force
    // 'query_var' to false". So `edit.php?post_type=program&season=fall` is not
    // parsed by core at all, and the "Used by" links on the term screens would
    // silently return an unfiltered list. Handled explicitly here instead.
    $tax_query = [];

    foreach ( NSFC_FIXED_TAXONOMIES as $taxonomy ) {
        if ( ! is_object_in_taxonomy( $post_type, $taxonomy ) ) {
            continue;
        }

        $slug = isset( $_GET[ $taxonomy ] ) ? sanitize_key( wp_unslash( $_GET[ $taxonomy ] ) ) : '';
        if ( '' === $slug ) {
            continue;
        }

        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $slug,
        ];
    }

    if ( ! $tax_query ) {
        return;
    }

    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = 'AND';
    }

    $query->set( 'tax_query', $tax_query );
}
