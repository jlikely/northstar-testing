<?php
/**
 * Template Name: Location Chooser
 *
 * The /youth-soccer/ landing page — a card picker linking to every published
 * Location Hub page. Queries pages by template rather than a hardcoded list,
 * so publishing a new Location Hub page (see page-templates/location-hub.php)
 * makes it appear here automatically — no code changes needed. Order pages
 * with Page Attributes → Order in the editor sidebar.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$location_pages = get_posts( [
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => 'page-templates/location-hub.php',
    'posts_per_page' => -1,
    'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
] );
$finder_page = get_page_by_path( 'youth-soccer/rochester/find-your-program' );
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php
      if ( function_exists( 'yoast_breadcrumb' ) ) {
          yoast_breadcrumb( '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">', '</p></nav>' );
      }
      ?>

      <h1 class="display-6 fw-bold mb-2">Youth Soccer</h1>
      <p class="lead text-muted mb-5" style="max-width:520px">North Star FC offers programs for players of all ages and experience levels — from first-time players to competitive travel teams.</p>

      <h2 class="h5 fw-semibold mb-4">Choose your location</h2>
      <div class="row g-4 mb-5">
        <?php foreach ( $location_pages as $loc_page ) : ?>
          <div class="col-md-6">
            <div class="card border h-100">
              <div class="card-body d-flex flex-column">
                <h3 class="h6 fw-semibold mb-2">
                  <a href="<?php echo esc_url( get_permalink( $loc_page ) ); ?>" class="stretched-link"><?php echo esc_html( get_the_title( $loc_page ) ); ?></a>
                </h3>
                <p class="small text-muted mb-0"><?php echo esc_html( carbon_get_post_meta( $loc_page->ID, 'nsfc_short_desc' ) ); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ( $finder_page ) : ?>
        <p class="small text-muted">
          Not sure where to start? <a href="<?php echo esc_url( get_permalink( $finder_page ) ); ?>">Use our program finder →</a>
        </p>
      <?php endif; ?>

      <div class="border-top pt-4 mt-5">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← Home</a>
      </div>

    </div>
  </div>
</div>

<?php get_footer(); ?>
