<?php
/**
 * Template Name: Season Landing
 *
 * Used for the season hub pages (3 competitive, 3 recreational, per location).
 * Reads Location / Level / Season from the "Season Landing Details" Carbon
 * Fields box in the Page editor sidebar (see inc/carbon-fields.php) to know
 * which programs to query — no theme file edits, no WP-CLI needed to set up
 * or edit a season page.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$page_location = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
$page_season   = carbon_get_post_meta( get_the_ID(), 'nsfc_season' );
$page_level    = carbon_get_post_meta( get_the_ID(), 'nsfc_level' );

// Season toggle siblings — all three season pages for the same location + level
$sibling_slugs = [
    'competitive'  => [
        'spring-summer' => 'Spring/Summer',
        'fall'          => 'Fall',
        'winter'        => 'Winter',
    ],
    'recreational' => [
        'spring-summer' => 'Spring/Summer',
        'fall'          => 'Fall',
        'winter'        => 'Winter',
    ],
];

// Map season slug → sibling page URL
// Pages must be published with slug pattern: youth-soccer/{location}/{level}/{season}
function nsfc_season_page_url( $location, $level, $season_slug ) {
    $page = get_page_by_path( "youth-soccer/{$location}/{$level}/{$season_slug}" );
    return $page ? get_permalink( $page->ID ) : '#';
}

// Query programs for this location + season + level
$programs = new WP_Query( [
    'post_type'      => 'program',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
    'tax_query'      => [
        'relation' => 'AND',
        [
            'taxonomy' => 'season',
            'field'    => 'slug',
            'terms'    => $page_season,
        ],
        [
            'taxonomy' => 'program_level',
            'field'    => 'slug',
            'terms'    => $page_level,
        ],
        [
            'taxonomy' => 'program_location',
            'field'    => 'slug',
            'terms'    => $page_location,
        ],
    ],
] );

// Build a unified card list: program CPT posts + this season page's own child
// pages (e.g. Kickstarters, Youth Classes — built as dedicated pages in Phase 7
// because they needed more than the generic program template). Child pages link
// directly to their real content instead of through a stub program post.
$cards = [];
while ( $programs->have_posts() ) {
    $programs->the_post();
    $cards[] = [
        'title'      => get_the_title(),
        'permalink'  => get_permalink(),
        'badge'      => carbon_get_post_meta( get_the_ID(), 'age_label' ),
        'excerpt'    => get_the_excerpt(),
        'date_range' => carbon_get_post_meta( get_the_ID(), 'date_range' ),
    ];
}
wp_reset_postdata();

$child_pages = get_posts( [
    'post_type'      => 'page',
    'post_parent'    => get_the_ID(),
    'posts_per_page' => -1,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );
foreach ( $child_pages as $child ) {
    $cards[] = [
        'title'      => get_the_title( $child ),
        'permalink'  => get_permalink( $child ),
        'badge'      => get_post_meta( $child->ID, '_nsfc_badge', true ),
        'excerpt'    => get_the_excerpt( $child ),
        'date_range' => '',
    ];
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php
      // Breadcrumb
      if ( function_exists( 'yoast_breadcrumb' ) ) {
          yoast_breadcrumb( '<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">', '</p></nav>' );
      }
      ?>

      <?php // Season toggle tab bar — only on season landing pages, never on detail pages ?>
      <?php if ( $page_level && isset( $sibling_slugs[ $page_level ] ) ) : ?>
      <nav aria-label="Seasons" class="mb-4">
        <div class="d-flex gap-2" role="tablist">
          <?php foreach ( $sibling_slugs[ $page_level ] as $slug => $label ) :
                $is_active = ( $slug === $page_season );
                $url       = nsfc_season_page_url( $page_location, $page_level, $slug );
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

      <?php // Page title ?>
      <h1 class="display-6 fw-bold mb-1"><?php the_title(); ?></h1>
      <?php if ( get_the_excerpt() ) : ?>
        <p class="text-muted mb-5"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
      <?php else : ?>
        <div class="mb-5"></div>
      <?php endif; ?>

      <?php // Program card grid — merged program CPT posts + this season's own child pages ?>
      <?php if ( $cards ) : ?>
      <div class="row g-3">
        <?php foreach ( $cards as $card ) : ?>
        <div class="col-sm-6">
          <div class="card border h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h2 class="h6 fw-semibold mb-0">
                  <a href="<?php echo esc_url( $card['permalink'] ); ?>" class="stretched-link">
                    <?php echo esc_html( $card['title'] ); ?>
                  </a>
                </h2>
                <?php if ( $card['badge'] ) : ?>
                  <span class="badge bg-light text-muted border ms-2 fw-normal"><?php echo esc_html( $card['badge'] ); ?></span>
                <?php endif; ?>
              </div>
              <?php if ( $card['excerpt'] ) : ?>
                <p class="small text-muted flex-grow-1 mb-3"><?php echo wp_kses_post( $card['excerpt'] ); ?></p>
              <?php endif; ?>
              <?php if ( $card['date_range'] ) : ?>
                <p class="small text-muted mb-0"><?php echo esc_html( $card['date_range'] ); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else : ?>
        <p class="text-muted">No programs listed for this season yet.</p>
      <?php endif; ?>

      <?php // Contact footer ?>
      <div class="border-top pt-4 mt-5">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <a href="<?php echo esc_url( get_home_url() ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← Home</a>
      </div>

    </div>
  </div>
</div>

<?php get_footer(); ?>
