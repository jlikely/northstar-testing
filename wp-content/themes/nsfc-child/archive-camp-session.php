<?php
/**
 * Camp Sessions archive — static table per season.
 * Grouped by season taxonomy term, displayed as three TablePress-style tables.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$seasons = [
    'spring-summer' => 'Spring/Summer Camps',
    'fall'          => 'Fall Camps',
    'winter'        => 'Winter Camps',
];
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <?php
      if ( function_exists( 'yoast_breadcrumb' ) ) {
          yoast_breadcrumb( '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">', '</p></nav>' );
      }
      ?>

      <h1 class="display-6 fw-bold mb-1">Camps &amp; Clinics</h1>
      <p class="text-muted mb-5">Rochester · All seasons</p>

      <?php foreach ( $seasons as $season_slug => $season_label ) :
            $camps = new WP_Query( [
                'post_type'      => 'camp-session',
                'posts_per_page' => -1,
                'orderby'        => 'meta_value',
                'meta_key'       => '_start_date',
                'order'          => 'ASC',
                'tax_query'      => [ [
                    'taxonomy' => 'season',
                    'field'    => 'slug',
                    'terms'    => $season_slug,
                ] ],
            ] );

            if ( ! $camps->have_posts() ) continue;
      ?>
      <section class="mb-5">
        <h2 class="h5 fw-semibold mb-3"><?php echo esc_html( $season_label ); ?></h2>
        <div class="border rounded-3 overflow-hidden">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th class="fw-normal text-muted small border-0">Camp</th>
                <th class="fw-normal text-muted small border-0">Dates</th>
                <th class="fw-normal text-muted small border-0">Ages</th>
                <th class="fw-normal text-muted small border-0">Time</th>
                <th class="fw-normal text-muted small border-0 text-end">Cost</th>
                <th class="fw-normal text-muted small border-0"></th>
              </tr>
            </thead>
            <tbody>
              <?php while ( $camps->have_posts() ) : $camps->the_post();
                    $date_label      = carbon_get_post_meta( get_the_ID(), 'date_label' );
                    $ages            = carbon_get_post_meta( get_the_ID(), 'ages' );
                    $session_time    = carbon_get_post_meta( get_the_ID(), 'session_time' );
                    $cost            = carbon_get_post_meta( get_the_ID(), 'cost' );
                    $registration_url = carbon_get_post_meta( get_the_ID(), 'registration_url' );
              ?>
              <tr>
                <td class="small fw-medium"><?php the_title(); ?></td>
                <td class="small"><?php echo esc_html( $date_label ); ?></td>
                <td class="small"><?php echo esc_html( $ages ); ?></td>
                <td class="small"><?php echo esc_html( $session_time ); ?></td>
                <td class="small text-end"><?php echo esc_html( $cost ); ?></td>
                <td class="small text-end">
                  <?php if ( $registration_url ) : ?>
                    <a href="<?php echo esc_url( $registration_url ); ?>" class="btn btn-dark btn-sm" target="_blank" rel="noopener noreferrer">Register →</a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; wp_reset_postdata(); ?>
            </tbody>
          </table>
        </div>
      </section>
      <?php endforeach; ?>

      <div class="border-top pt-4">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <a href="<?php echo esc_url( get_home_url() ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← Home</a>
      </div>

    </div>
  </div>
</div>

<?php get_footer(); ?>
