<?php
/**
 * Template Name: Level Hub
 *
 * Per-location Competitive / Recreational landing page, one step above the
 * season pages (e.g. /youth-soccer/rochester/competitive/). Location, intro,
 * footer prompt, and the three season cards' copy come from the "Level Hub
 * Details" Carbon Fields box (see inc/carbon-fields.php). Season cards link to
 * this page's own child pages — each child must use the "Season Landing"
 * template with a matching slug (spring-summer / fall / winter).
 *
 * Unlike camps-hub.php, which queries real camp-session posts, a level hub has
 * no posts to read at this depth: its card copy is typed per page.
 *
 * Markup here deliberately reproduces the hand-typed HTML this template
 * replaced on posts 7 and 8 — col-lg-9 (not camps-hub.php's col-lg-8), the
 * season label "Spring / Summer" with spaces, and classed intro paragraphs.
 * Don't normalize these to match camps-hub.php.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$location_slug  = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
$location_term  = $location_slug ? get_term_by( 'slug', $location_slug, 'program_location' ) : false;
$location_label = $location_term ? $location_term->name : '';
$intro          = carbon_get_post_meta( get_the_ID(), 'nsfc_intro' );
$footer_prompt  = carbon_get_post_meta( get_the_ID(), 'nsfc_footer_prompt' );

$seasons = [
    'spring-summer' => [
        'label'       => 'Spring / Summer',
        'date_range'  => carbon_get_post_meta( get_the_ID(), 'nsfc_spring_summer_date_range' ),
        'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_spring_summer_description' ),
    ],
    'fall' => [
        'label'       => 'Fall',
        'date_range'  => carbon_get_post_meta( get_the_ID(), 'nsfc_fall_date_range' ),
        'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_fall_description' ),
    ],
    'winter' => [
        'label'       => 'Winter',
        'date_range'  => carbon_get_post_meta( get_the_ID(), 'nsfc_winter_date_range' ),
        'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_winter_description' ),
    ],
];

// One textarea holds every intro paragraph, separated by blank lines. The first
// gets `lead`, the last gets `mb-5` — this reproduces both Competitive's single
// paragraph and Recreational's three from the same field.
$intro_paragraphs = array_values( array_filter( array_map( 'trim', preg_split( '/\R{2,}/', (string) $intro ) ) ) );
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <?php
      nsfc_breadcrumb();
      ?>

      <h1 class="display-6 fw-bold mb-1"><?php the_title(); ?></h1>
      <?php if ( $location_label ) : ?>
        <p class="text-muted mb-4"><?php echo esc_html( $location_label ); ?></p>
      <?php endif; ?>

      <?php
      $last_paragraph = count( $intro_paragraphs ) - 1;
      foreach ( $intro_paragraphs as $i => $paragraph ) {
          $classes = array_filter( [
              0 === $i ? 'lead' : '',
              'text-muted',
              $i === $last_paragraph ? 'mb-5' : 'mb-2',
          ] );
          printf(
              '<p class="%s" style="max-width:600px">%s</p>',
              esc_attr( implode( ' ', $classes ) ),
              esc_html( $paragraph )
          );
      }
      ?>

      <div class="row g-3 mb-5">
        <?php foreach ( $seasons as $season_slug => $season ) : ?>
          <div class="col-sm-4">
            <div class="card border h-100">
              <div class="card-body d-flex flex-column">
                <h2 class="h6 fw-semibold mb-0">
                  <a href="<?php echo esc_url( trailingslashit( get_permalink() ) . $season_slug . '/' ); ?>" class="stretched-link"><?php echo esc_html( $season['label'] ); ?></a>
                </h2>
                <?php if ( $season['date_range'] ) : ?>
                  <p class="small text-muted mb-1"><?php echo esc_html( $season['date_range'] ); ?></p>
                <?php endif; ?>
                <?php if ( $season['description'] ) : ?>
                  <p class="small text-muted flex-grow-1 mb-0"><?php echo esc_html( $season['description'] ); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="border-top pt-4">
        <?php if ( $footer_prompt ) : ?>
          <p class="small text-muted mb-2"><?php echo esc_html( $footer_prompt ); ?></p>
        <?php endif; ?>
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
