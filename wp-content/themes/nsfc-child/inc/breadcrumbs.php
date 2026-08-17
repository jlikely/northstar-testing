<?php
defined( 'ABSPATH' ) || exit;

/**
 * Native breadcrumb trail — replaces yoast_breadcrumb().
 *
 * Breadcrumbs are information architecture, not SEO, so they shouldn't depend
 * on whichever SEO plugin happens to be installed. This also retires the
 * domain-migration hazard Yoast introduced: it cached absolute permalinks in
 * its own tables, so every breadcrumb kept pointing at the old host after the
 * site moved (see CLAUDE.md, 2026-08-15).
 *
 * Output is byte-identical to what Yoast produced, so the swap changes nothing
 * visually and `.breadcrumb` / `.breadcrumb_last` keep working:
 *
 *   <nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb"><span>
 *     <span><a href="…">Home</a></span> » … »
 *     <span class="breadcrumb_last" aria-current="page">Title</span>
 *   </span></p></nav>
 *
 * The trail is the page's own ancestor chain, so it follows Page Attributes →
 * Parent automatically — no configuration and nothing to keep in sync.
 *
 * Program CPT singles do NOT use this: single-program.php builds its trail from
 * taxonomy terms instead, because a program's place in the hierarchy comes from
 * its season/level/location, not from a page parent. See
 * nsfc_program_referring_season_page() below for the one exception.
 */
function nsfc_breadcrumb() {
    $crumbs = [ [ 'label' => 'Home', 'url' => home_url( '/' ) ] ];

    if ( is_page() ) {
        $id = get_the_ID();

        // get_post_ancestors() returns nearest-first; the trail reads root-first.
        foreach ( array_reverse( get_post_ancestors( $id ) ) as $ancestor_id ) {
            $crumbs[] = [
                'label' => get_the_title( $ancestor_id ),
                'url'   => get_permalink( $ancestor_id ),
            ];
        }

        $current = get_the_title( $id );
    } elseif ( is_post_type_archive() ) {
        $obj     = get_queried_object();
        $current = $obj && isset( $obj->labels->name ) ? $obj->labels->name : get_the_archive_title();
    } else {
        $current = get_the_title();
    }

    // Titles from get_the_title() have already been through the `the_title`
    // filters (wptexturize converts "&" to "&#038;"), so they are display-ready.
    // Running esc_html() over them again would double-encode to "&amp;#038;" —
    // which is also why the pre-Yoast markup showed "Camps &#038; Clinics".
    echo '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb"><span>';

    foreach ( $crumbs as $crumb ) {
        printf( '<span><a href="%s">%s</a></span> » ', esc_url( $crumb['url'] ), $crumb['label'] );
    }

    printf( '<span class="breadcrumb_last" aria-current="page">%s</span>', $current );

    echo '</span></p></nav>';
}

/**
 * The Season Landing page a visitor reached this program through, from the
 * `?from={page_id}` that season landing cards append to their links.
 *
 * Exists because a program is deliberately single-sourced across every season
 * it runs in (see CLAUDE.md). Its own taxonomy therefore can't say which season
 * page you were on, and picking the first term sends people "back" to a page
 * they never visited — post 210 is tagged Winter, Spring/Summer and Fall.
 *
 * Returns null unless the ID names a published Season Landing page whose
 * location/level/season this program is actually tagged with. That last check
 * matters: `from` comes from the URL, so without it any page ID would rewrite
 * the trail into a hierarchy the program isn't part of.
 */
function nsfc_program_referring_season_page( $program_id ) {
    $from_id = isset( $_GET['from'] ) ? absint( $_GET['from'] ) : 0;
    if ( ! $from_id ) {
        return null;
    }

    $page = get_post( $from_id );
    if (
        ! $page
        || 'page' !== $page->post_type
        || 'publish' !== $page->post_status
        || 'page-templates/season-landing.php' !== get_page_template_slug( $from_id )
    ) {
        return null;
    }

    $expected = [
        'program_location' => get_post_meta( $from_id, '_nsfc_location', true ),
        'program_level'    => get_post_meta( $from_id, '_nsfc_level', true ),
        'season'           => get_post_meta( $from_id, '_nsfc_season', true ),
    ];

    foreach ( $expected as $taxonomy => $slug ) {
        if ( ! $slug || ! has_term( $slug, $taxonomy, $program_id ) ) {
            return null;
        }
    }

    return $page;
}

/**
 * A Season Landing page's location / level / season as display labels, for the
 * subtitle under a program title — e.g. ['Rochester', 'Recreational', 'Fall'].
 */
function nsfc_season_page_labels( $page_id ) {
    $labels = [];

    foreach ( [ 'program_location' => '_nsfc_location', 'program_level' => '_nsfc_level', 'season' => '_nsfc_season' ] as $taxonomy => $meta_key ) {
        $slug = get_post_meta( $page_id, $meta_key, true );
        $term = $slug ? get_term_by( 'slug', $slug, $taxonomy ) : null;
        if ( $term && ! is_wp_error( $term ) ) {
            $labels[] = $term->name;
        }
    }

    return $labels;
}
