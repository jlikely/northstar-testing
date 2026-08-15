<?php
defined( 'ABSPATH' ) || exit;

add_action( 'admin_enqueue_scripts', 'nsfc_admin_styles' );

/**
 * Load assets/admin.css on the screens that render Carbon Fields boxes.
 *
 * Post/page editors carry the post_meta containers; the term screens carry the
 * Camp Type Description term_meta container. Nothing else in wp-admin needs it,
 * so it isn't loaded globally.
 */
function nsfc_admin_styles( $hook ) {
    $screens = [ 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

    if ( ! in_array( $hook, $screens, true ) ) {
        return;
    }

    wp_enqueue_style(
        'nsfc-admin',
        get_stylesheet_directory_uri() . '/assets/admin.css',
        [],
        wp_get_theme()->get( 'Version' )
    );
}
