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
 * its season/level/location, not from a page parent.
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
