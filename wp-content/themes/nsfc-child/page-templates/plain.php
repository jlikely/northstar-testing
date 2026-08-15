<?php
/**
 * Template Name: Plain Page
 *
 * Minimal page template — no Kadence entry-content wrapper (which adds a
 * boxed/shadowed "card" treatment around the whole page and its own title
 * bar). Just header, the page's own content, footer — matching the plain
 * white-background look of the site's fully custom templates
 * (season-landing.php, location-hub.php, single-program.php, etc.), which
 * never had this problem since they never go through Kadence's wrapper.
 *
 * Every plain content page (Home, hub pages, stub pages, static pages) uses
 * this template so styling is consistent site-wide while a real theme is
 * still being designed.
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    the_content();
endwhile;

get_footer();
