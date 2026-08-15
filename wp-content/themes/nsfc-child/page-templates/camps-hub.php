<?php
/**
 * Template Name: Camps Hub
 *
 * Per-location Camps & Clinics landing page (e.g. /youth-soccer/rochester/camps/).
 * Location + intro come from the "Camps Hub Details" Carbon Fields box (see
 * inc/carbon-fields.php). Season cards link to this page's own child pages —
 * each child must use the "Camps Season" template with a matching slug
 * (spring-summer / fall / winter).
 */
defined( 'ABSPATH' ) || exit;

get_header();

$location_slug  = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
$location_term  = $location_slug ? get_term_by( 'slug', $location_slug, 'program_location' ) : false;
$location_label = $location_term ? $location_term->name : '';
$intro          = carbon_get_post_meta( get_the_ID(), 'nsfc_intro' );

$seasons = [
    'spring-summer' => 'Spring/Summer',
    'fall'          => 'Fall',
    'winter'        => 'Winter',
];
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php
      if ( function_exists( 'yoast_breadcrumb' ) ) {
          yoast_breadcrumb( '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">', '</p></nav>' );
      }
      ?>

      <h1 class="display-6 fw-bold mb-1"><?php the_title(); ?></h1>
      <?php if ( $location_label ) : ?>
        <p class="text-muted mb-4"><?php echo esc_html( $location_label ); ?></p>
      <?php endif; ?>
      <?php if ( $intro ) : ?>
        <p class="mb-4"><?php echo esc_html( $intro ); ?></p>
      <?php endif; ?>

      <div class="row g-3">
        <?php foreach ( $seasons as $season_slug => $season_label ) : ?>
          <div class="col-sm-4">
            <div class="card border h-100">
              <div class="card-body d-flex flex-column">
                <h2 class="h6 fw-semibold mb-0">
                  <a href="<?php echo esc_url( trailingslashit( get_permalink() ) . $season_slug . '/' ); ?>" class="stretched-link"><?php echo esc_html( $season_label ); ?></a>
                </h2>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="border-top pt-4 mt-5">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <?php if ( $location_slug ) :
          $loc_page = get_page_by_path( "youth-soccer/{$location_slug}" );
          if ( $loc_page ) : ?>
            <a href="<?php echo esc_url( get_permalink( $loc_page ) ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← <?php echo esc_html( $location_label ); ?></a>
          <?php endif;
        endif; ?>
      </div>

    </div>
  </div>
</div>

<?php get_footer(); ?>
