<?php
/**
 * Fallback template — required by WordPress for all themes.
 * Kadence handles most page rendering; this covers any edge case.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container py-5">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <h1 class="display-6 fw-bold mb-4"><?php the_title(); ?></h1>
    <div class="entry-content"><?php the_content(); ?></div>
  <?php endwhile; else : ?>
    <h1 class="display-6 fw-bold mb-4">Nothing here yet.</h1>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
