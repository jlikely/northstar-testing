<?php
/**
 * Template Name: Camps Season
 *
 * A single season's camp listing (e.g. /youth-soccer/rochester/camps/spring-summer/).
 * Location + season come from the "Camps Season Details" Carbon Fields box
 * (see inc/carbon-fields.php). Queries camp-session posts directly.
 *
 * Cards instead of a table, each opening a details modal (camp_type's shared
 * description — see the "Camp Type Description" term-meta box — plus this
 * session's own dates/ages/cost/venue). A Level filter (Recreational /
 * Competitive) is a real query-string link (?level=competitive), server-
 * rendered like every other filter on this site — no custom JavaScript.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$location_slug  = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
$season_slug    = carbon_get_post_meta( get_the_ID(), 'nsfc_season' );
$location_term  = $location_slug ? get_term_by( 'slug', $location_slug, 'program_location' ) : false;
$location_label = $location_term ? $location_term->name : '';
$note           = carbon_get_post_meta( get_the_ID(), 'nsfc_note' );

$level_filter = isset( $_GET['level'] ) && in_array( $_GET['level'], [ 'recreational', 'competitive' ], true )
    ? $_GET['level']
    : '';

$tax_query = [
    'relation' => 'AND',
    [
        'taxonomy' => 'season',
        'field'    => 'slug',
        'terms'    => $season_slug,
    ],
    [
        'taxonomy' => 'program_location',
        'field'    => 'slug',
        'terms'    => $location_slug,
    ],
];
if ( $level_filter ) {
    $tax_query[] = [
        'taxonomy' => 'program_level',
        'field'    => 'slug',
        'terms'    => $level_filter,
    ];
}

$camps = new WP_Query( [
    'post_type'      => 'camp-session',
    'posts_per_page' => -1,
    'orderby'        => 'meta_value',
    'meta_key'       => '_start_date',
    'order'          => 'ASC',
    'tax_query'      => $tax_query,
] );

$level_options = [
    ''             => 'All levels',
    'recreational' => 'Recreational',
    'competitive'  => 'Competitive',
];
$level_badge = [
    'recreational' => 'Recreational',
    'competitive'  => 'Competitive',
];
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

      <?php if ( $camps->have_posts() || $level_filter ) : ?>
        <nav aria-label="Level" class="mb-4">
          <div class="d-flex flex-wrap gap-2" role="tablist">
            <?php foreach ( $level_options as $slug => $label ) :
                  $is_active = ( $slug === $level_filter );
                  $url       = $slug ? add_query_arg( 'level', $slug ) : remove_query_arg( 'level' );
            ?>
              <a href="<?php echo esc_url( $url ); ?>"
                 class="btn btn-sm <?php echo $is_active ? 'btn-dark' : 'btn-outline-secondary'; ?>"
                 role="tab"
                 aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                <?php echo esc_html( $label ); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </nav>
      <?php endif; ?>

      <?php if ( $camps->have_posts() ) : ?>
        <div class="row g-3">
          <?php while ( $camps->have_posts() ) : $camps->the_post();
                $camp_id          = get_the_ID();
                $date_label       = carbon_get_post_meta( $camp_id, 'date_label' );
                $ages             = carbon_get_post_meta( $camp_id, 'ages' );
                $session_time     = carbon_get_post_meta( $camp_id, 'session_time' );
                $venue            = carbon_get_post_meta( $camp_id, 'venue' );
                $cost             = carbon_get_post_meta( $camp_id, 'cost' );
                $registration_url = carbon_get_post_meta( $camp_id, 'registration_url' );

                $type_terms = get_the_terms( $camp_id, 'camp_type' );
                $type_term  = ( $type_terms && ! is_wp_error( $type_terms ) ) ? $type_terms[0] : null;
                $intro      = $type_term ? carbon_get_term_meta( $type_term->term_id, 'intro' ) : '';
                $points     = $type_term ? carbon_get_term_meta( $type_term->term_id, 'points' ) : [];

                $level_terms = get_the_terms( $camp_id, 'program_level' );
                $level_slug  = ( $level_terms && ! is_wp_error( $level_terms ) ) ? $level_terms[0]->slug : '';
                $level_label = $level_badge[ $level_slug ] ?? '';

                $modal_id = 'camp-modal-' . $camp_id;
          ?>
          <div class="col-sm-6">
            <div class="card border h-100">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                  <?php if ( $level_label ) : ?>
                    <span class="badge bg-light text-muted border fw-normal"><?php echo esc_html( $level_label ); ?></span>
                  <?php endif; ?>
                  <span class="small fw-semibold text-end"><?php echo esc_html( $date_label ); ?></span>
                </div>

                <h2 class="h6 fw-semibold mb-1">
                  <button type="button"
                          class="btn btn-link p-0 text-start fw-semibold stretched-link"
                          style="font-size:inherit;line-height:inherit"
                          data-bs-toggle="modal"
                          data-bs-target="#<?php echo esc_attr( $modal_id ); ?>"
                          aria-label="<?php echo esc_attr( get_the_title() . ' — view details' ); ?>">
                    <?php the_title(); ?>
                  </button>
                </h2>

                <p class="small text-muted mb-3">
                  <?php echo esc_html( trim( implode( ' · ', array_filter( [ $ages, $session_time, $venue ] ) ) ) ); ?>
                </p>

                <div class="mt-auto">
                  <span class="small fw-semibold"><?php echo esc_html( $cost ); ?></span>
                </div>
              </div>
            </div>
          </div>

          <?php // ── Details modal for this camp ──────────────────────────────── ?>
          <div class="modal fade" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $modal_id ); ?>-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header align-items-start">
                  <div class="me-3">
                    <?php if ( $level_label ) : ?>
                      <span class="badge bg-light text-muted border fw-normal small mb-2"><?php echo esc_html( $level_label ); ?></span>
                    <?php endif; ?>
                    <h2 class="modal-title h5 fw-bold mb-1" id="<?php echo esc_attr( $modal_id ); ?>-title"><?php the_title(); ?></h2>
                    <p class="text-muted small mb-0">
                      <?php echo esc_html( trim( implode( ' · ', array_filter( [ $date_label, $session_time, $venue ] ) ) ) ); ?>
                    </p>
                  </div>
                  <button type="button" class="btn-close ms-auto flex-shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <?php if ( $intro || $points ) : ?>
                    <?php if ( $intro ) : ?>
                      <p class="mb-4"><?php echo esc_html( $intro ); ?></p>
                    <?php endif; ?>
                    <?php if ( $points ) : ?>
                      <ul class="list-unstyled mb-0">
                        <?php foreach ( $points as $point ) : ?>
                          <li class="d-flex gap-2 mb-3">
                            <span class="text-primary mt-1 flex-shrink-0">&rsaquo;</span>
                            <span><strong><?php echo esc_html( $point['title'] ); ?>:</strong> <span class="text-muted"><?php echo esc_html( $point['text'] ); ?></span></span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  <?php else : ?>
                    <p class="text-muted mb-0">Details coming soon.</p>
                  <?php endif; ?>
                </div>
                <div class="modal-footer justify-content-between">
                  <span class="text-muted small"><?php echo esc_html( $ages ); ?> &middot; <strong><?php echo esc_html( $cost ); ?></strong></span>
                  <?php if ( $registration_url ) : ?>
                    <a href="<?php echo esc_url( $registration_url ); ?>" class="btn btn-dark" target="_blank" rel="noopener noreferrer">Register Now</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php if ( $note ) : ?>
          <p class="small text-muted mt-3 mb-0"><?php echo wp_kses_post( $note ); ?></p>
        <?php endif; ?>
      <?php else : ?>
        <p class="mb-0">
          <?php if ( $note ) : ?>
            <?php echo wp_kses_post( $note ); ?>
          <?php else : ?>
            No <?php echo esc_html( get_the_title() ); ?> are currently scheduled. Check back soon.
          <?php endif; ?>
        </p>
      <?php endif; ?>

      <div class="border-top pt-4 mt-5">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <a href="<?php echo esc_url( get_permalink( wp_get_post_parent_id( get_the_ID() ) ) ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← Camps &amp; Clinics</a>
      </div>

    </div>
  </div>
</div>

<?php get_footer(); ?>
