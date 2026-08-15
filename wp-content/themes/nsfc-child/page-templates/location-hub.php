<?php
/**
 * Template Name: Location Hub
 *
 * Used for each location's top-level landing page (Rochester, Austin, Albert
 * Lea, Winona, and any future location). Fully self-serve: label comes from
 * the page title, slug/intro/short description come from the "Location
 * Details" Carbon Fields box in the Page editor sidebar (see
 * inc/carbon-fields.php) — no theme file edits, no WP-CLI needed to add or
 * edit a location.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$location_slug = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
$location      = $location_slug ? [
    'label' => get_the_title(),
    'intro' => carbon_get_post_meta( get_the_ID(), 'nsfc_intro' ),
] : null;
$offerings     = carbon_get_post_meta( get_the_ID(), 'nsfc_offerings' );
$programs      = array_filter(
    nsfc_location_programs(),
    fn( $program ) => in_array( $program['key'], $offerings, true )
);

function nsfc_resolve_location_href( $program, $location_slug ) {
    if ( isset( $program['href'] ) ) {
        return $program['href'];
    }
    return str_replace( '{location}', $location_slug, $program['hrefTemplate'] );
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php
      if ( function_exists( 'yoast_breadcrumb' ) ) {
          yoast_breadcrumb( '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">', '</p></nav>' );
      }
      ?>

      <?php if ( ! $location ) : ?>
        <h1 class="h4 fw-bold mb-3">Location not found</h1>
        <a href="/youth-soccer/" class="text-primary">← Back to Youth Soccer</a>
      <?php else : ?>

        <h1 class="display-6 fw-bold mb-2"><?php echo esc_html( $location['label'] ); ?></h1>
        <p class="lead text-muted mb-5" style="max-width:520px"><?php echo esc_html( $location['intro'] ); ?></p>

        <?php
        // Opportunity Finder — only shown once this location has its own
        // Find Your Program page built (currently just Rochester).
        $finder_page = get_page_by_path( "youth-soccer/{$location_slug}/find-your-program" );
        if ( $finder_page ) :
        ?>
        <section class="bg-light border rounded-3 p-4 p-md-5 mb-5">
          <h2 class="h5 fw-bold mb-1">Not sure where to start?</h2>
          <p class="text-muted small mb-3">Answer a few quick questions and we'll point you to the right program.</p>
          <a href="<?php echo esc_url( get_permalink( $finder_page ) ); ?>" class="btn btn-dark btn-sm">Find your program →</a>
        </section>
        <?php endif; ?>

        <section class="mb-5">
          <h2 class="h5 fw-semibold mb-4">Programs in <?php echo esc_html( $location['label'] ); ?></h2>
          <div class="row g-3">
            <?php foreach ( $programs as $program ) : ?>
              <div class="col-md-6">
                <div class="card border h-100">
                  <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h3 class="h6 fw-semibold mb-0">
                        <a href="<?php echo esc_url( nsfc_resolve_location_href( $program, $location_slug ) ); ?>" class="stretched-link"><?php echo esc_html( $program['label'] ); ?></a>
                      </h3>
                      <span class="badge bg-light text-muted border ms-2 fw-normal"><?php echo esc_html( $program['badge'] ); ?></span>
                    </div>
                    <p class="small text-muted mb-0"><?php echo esc_html( $program['desc'] ); ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="border-top pt-4">
          <h2 class="h6 fw-semibold mb-3">Already registered?</h2>
          <div class="row g-3">
            <?php foreach ( nsfc_location_registered_links() as $link ) : ?>
              <div class="col-sm-6 col-md-3">
                <div class="card border h-100">
                  <div class="card-body d-flex flex-column">
                    <h3 class="h6 fw-semibold mb-0">
                      <a href="<?php echo esc_url( $link['href'] ); ?>" class="stretched-link"><?php echo esc_html( $link['label'] ); ?></a>
                    </h3>
                    <p class="small text-muted mb-0"><?php echo esc_html( $link['desc'] ); ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

      <?php endif; ?>

    </div>
  </div>
</div>

<?php get_footer(); ?>
