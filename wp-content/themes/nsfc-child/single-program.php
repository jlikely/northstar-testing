<?php
/**
 * Single program detail page.
 * Follows the detail page template order defined in CLAUDE.md exactly.
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // Carbon Fields meta
    $age_label           = carbon_get_post_meta( get_the_ID(), 'age_label' );
    // Derived from the start/end date pickers, or the text override when the
    // real dates aren't pinned down yet — see inc/dates.php.
    $date_range          = nsfc_program_date_range( get_the_ID() );
    $tryout_required     = carbon_get_post_meta( get_the_ID(), 'tryout_required' );
    $format              = carbon_get_post_meta( get_the_ID(), 'format' );
    $venue               = carbon_get_post_meta( get_the_ID(), 'venue' );
    $cost_text           = carbon_get_post_meta( get_the_ID(), 'cost_text' );
    $notes               = carbon_get_post_meta( get_the_ID(), 'notes' );
    $show_financial_aid  = carbon_get_post_meta( get_the_ID(), 'show_financial_aid' );
    $external_link_label = carbon_get_post_meta( get_the_ID(), 'external_link_label' );
    $external_link_url   = carbon_get_post_meta( get_the_ID(), 'external_link_url' );

    // Registration
    $reg_window     = carbon_get_post_meta( get_the_ID(), 'registration_window' );
    $reg_note       = carbon_get_post_meta( get_the_ID(), 'registration_note' );
    $reg_url_boys   = carbon_get_post_meta( get_the_ID(), 'registration_url_boys' );
    $reg_url_girls  = carbon_get_post_meta( get_the_ID(), 'registration_url_girls' );
    $reg_url_team   = carbon_get_post_meta( get_the_ID(), 'registration_url_team' );
    $reg_url_indiv  = carbon_get_post_meta( get_the_ID(), 'registration_url_individual' );

    // Coaching
    $has_coaching        = carbon_get_post_meta( get_the_ID(), 'has_coaching' );
    $coaching_note       = carbon_get_post_meta( get_the_ID(), 'coaching_note' );
    $coaching_label      = carbon_get_post_meta( get_the_ID(), 'coaching_contact_label' );
    $coaching_href       = carbon_get_post_meta( get_the_ID(), 'coaching_contact_href' );

    // Schedule
    $practices = carbon_get_post_meta( get_the_ID(), 'schedule_practices' );
    $games     = carbon_get_post_meta( get_the_ID(), 'schedule_games' );

    // Pricing
    $costs      = carbon_get_post_meta( get_the_ID(), 'costs' );
    $cost_tiers = carbon_get_post_meta( get_the_ID(), 'cost_tiers' );

    // Sessions + practice nights
    $sessions        = carbon_get_post_meta( get_the_ID(), 'sessions' );
    $practice_nights = carbon_get_post_meta( get_the_ID(), 'practice_nights' );

    // Sub-programs — when present, these replace Key Details/Pricing/
    // Schedule/Sessions/Registration below with their own stacked sections.
    $sub_programs = carbon_get_post_meta( get_the_ID(), 'sub_programs' );

    // Taxonomy terms for subtitle + breadcrumb
    $season_terms   = get_the_terms( get_the_ID(), 'season' );
    $level_terms    = get_the_terms( get_the_ID(), 'program_level' );
    $location_terms = get_the_terms( get_the_ID(), 'program_location' );
    $season_slug    = ( $season_terms && ! is_wp_error( $season_terms ) ) ? $season_terms[0]->slug : '';
    $season_label   = ( $season_terms && ! is_wp_error( $season_terms ) ) ? $season_terms[0]->name : '';
    $level_slug     = ( $level_terms && ! is_wp_error( $level_terms ) ) ? $level_terms[0]->slug : '';
    $level_label    = ( $level_terms && ! is_wp_error( $level_terms ) ) ? $level_terms[0]->name : '';
    $location_slug  = ( $location_terms && ! is_wp_error( $location_terms ) ) ? $location_terms[0]->slug : '';
    $location_label = ( $location_terms && ! is_wp_error( $location_terms ) ) ? $location_terms[0]->name : '';
    $subtitle_parts = array_filter( [ $location_label, $level_label, $season_label ] );

    // Financial aid global text
    $financial_aid = nsfc_financial_aid();

    // Build the ancestor trail from taxonomy terms — program CPT posts aren't
    // hierarchical, so Yoast's default breadcrumb can't infer Youth Soccer >
    // {Location} > {Level} > {Season} on its own (it would just show Home > title).
    $crumbs = [ [ 'label' => 'Home', 'url' => home_url( '/' ) ] ];
    if ( $location_slug ) {
        $crumbs[] = [ 'label' => 'Youth Soccer', 'url' => home_url( '/youth-soccer/' ) ];
        $loc_page = get_page_by_path( "youth-soccer/{$location_slug}" );
        if ( $loc_page ) {
            $crumbs[] = [ 'label' => $location_label, 'url' => get_permalink( $loc_page ) ];
        }
        if ( $level_slug ) {
            $level_page = get_page_by_path( "youth-soccer/{$location_slug}/{$level_slug}" );
            if ( $level_page ) {
                $crumbs[] = [ 'label' => ucfirst( $level_slug ), 'url' => get_permalink( $level_page ) ];
            }
            if ( $season_slug ) {
                $season_page = get_page_by_path( "youth-soccer/{$location_slug}/{$level_slug}/{$season_slug}" );
                if ( $season_page ) {
                    $crumbs[] = [ 'label' => $season_label, 'url' => get_permalink( $season_page ) ];
                }
            }
        }
    }
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php // ── 1. Breadcrumb (built from taxonomy terms, not yoast_breadcrumb — see above) ── ?>
      <nav aria-label="Breadcrumb" class="mb-4">
        <p class="breadcrumb">
          <span>
            <?php foreach ( $crumbs as $crumb ) : ?>
              <span><a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a></span> »
            <?php endforeach; ?>
            <span class="breadcrumb_last" aria-current="page"><?php the_title(); ?></span>
          </span>
        </p>
      </nav>

      <?php // ── 2. Page header ──────────────────────────────────────────────── ?>
      <h1 class="display-6 fw-bold mb-1"><?php the_title(); ?></h1>
      <?php if ( $subtitle_parts ) : ?>
        <p class="text-muted mb-4"><?php echo esc_html( implode( ' · ', $subtitle_parts ) ); ?></p>
      <?php endif; ?>

      <?php // ── 3. Description / lead paragraph ─────────────────────────────── ?>
      <?php
      // Was `get_the_content()` until the block editor was removed from this
      // CPT — see inc/cpt.php. The old version also hid itself whenever the
      // program had a structured schedule, which made the editor canvas look
      // broken on those posts; a dedicated field has no reason to hide.
      $description = carbon_get_post_meta( get_the_ID(), 'description' );
      if ( $description ) :
      ?>
        <p class="lead text-muted mb-4" style="max-width:520px"><?php echo esc_html( $description ); ?></p>
      <?php endif; ?>

      <?php // ── 4. Key program details ──────────────────────────────────────── ?>
      <?php if ( ! $sub_programs && ( $age_label || $tryout_required || $format || $venue || $date_range || $cost_text ) ) : ?>
      <section class="mb-5">
        <?php if ( $age_label ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Ages</h2>
            <p class="mb-0"><?php echo esc_html( $age_label ); ?></p>
          </div>
        <?php endif; ?>
        <?php if ( $tryout_required ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Tryout required</h2>
            <p class="mb-0">Yes</p>
          </div>
        <?php endif; ?>
        <?php if ( $format ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Format</h2>
            <p class="mb-0"><?php echo esc_html( $format ); ?></p>
          </div>
        <?php endif; ?>
        <?php if ( $venue ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Location</h2>
            <p class="mb-0"><?php echo esc_html( $venue ); ?></p>
          </div>
        <?php endif; ?>
        <?php if ( $date_range ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Dates</h2>
            <p class="mb-0"><?php echo esc_html( $date_range ); ?></p>
          </div>
        <?php endif; ?>
        <?php if ( $cost_text ) : ?>
          <div class="mb-4">
            <h2 class="h6 fw-semibold mb-1">Cost</h2>
            <p class="mb-0"><?php echo esc_html( $cost_text ); ?></p>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <?php // ── 5. Pricing ──────────────────────────────────────────────────── ?>
      <?php if ( ! $sub_programs && $cost_tiers ) : ?>
      <section class="mb-5">
        <h2 class="h6 fw-semibold mb-3">Cost</h2>
        <div class="row g-3">
          <?php foreach ( $cost_tiers as $tier ) : ?>
          <div class="col-sm-6">
            <p class="small text-muted mb-2"><?php echo esc_html( $tier['tier_label'] ); ?></p>
            <div class="border rounded-3 overflow-hidden">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="fw-normal text-muted small border-0">Grade</th>
                    <th class="fw-normal text-muted small border-0 text-end">Cost</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ( $tier['tier_costs'] as $row ) : ?>
                  <tr>
                    <td class="small"><?php echo esc_html( $row['grade'] ); ?></td>
                    <td class="small text-end fw-medium"><?php echo esc_html( $row['cost'] ); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php elseif ( ! $sub_programs && $costs ) : ?>
      <section class="mb-5">
        <h2 class="h6 fw-semibold mb-3">Cost</h2>
        <div class="border rounded-3 overflow-hidden" style="max-width:320px">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th class="fw-normal text-muted small border-0">Grade</th>
                <th class="fw-normal text-muted small border-0 text-end">Cost</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ( $costs as $row ) : ?>
              <tr>
                <td class="small"><?php echo esc_html( $row['grade'] ); ?></td>
                <td class="small text-end fw-medium"><?php echo esc_html( $row['cost'] ); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
      <?php endif; ?>

      <?php // ── 6. Schedule ─────────────────────────────────────────────────── ?>
      <?php
      $has_practices = ! empty( $practices[0]['day'] );
      $has_games     = ! empty( $games[0]['day'] );
      if ( ! $sub_programs && ( $has_practices || $has_games ) ) :
      ?>
      <section class="mb-5">
        <h2 class="h6 fw-semibold mb-3">Schedule</h2>
        <?php if ( $has_practices ) :
              $p = $practices[0]; ?>
          <div class="mb-4">
            <h3 class="h6 fw-semibold mb-1">Practices</h3>
            <p class="mb-0"><?php echo esc_html( $p['day'] ); ?></p>
            <?php if ( $p['location'] ) : ?>
              <p class="text-muted mb-0"><?php echo esc_html( $p['location'] ); ?></p>
            <?php endif; ?>
            <?php if ( $p['dates'] ) : ?>
              <p class="text-muted mb-0"><?php echo esc_html( $p['dates'] ); ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <?php if ( $has_games ) :
              $g = $games[0]; ?>
          <div class="mb-4">
            <h3 class="h6 fw-semibold mb-1">Games</h3>
            <p class="mb-0"><?php echo esc_html( $g['day'] ); ?></p>
            <?php if ( $g['kickoff'] ) : ?>
              <p class="text-muted mb-0">Kickoff: <?php echo esc_html( $g['kickoff'] ); ?></p>
            <?php endif; ?>
            <?php if ( $g['location'] ) : ?>
              <p class="text-muted mb-0"><?php echo esc_html( $g['location'] ); ?></p>
            <?php endif; ?>
            <?php if ( $g['dates'] ) : ?>
              <p class="text-muted mb-0"><?php echo esc_html( $g['dates'] ); ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <?php // ── Practice nights (appended after structured schedule) ──────────── ?>
      <?php if ( ! $sub_programs && $practice_nights ) : ?>
      <section class="mb-5">
        <h3 class="h6 fw-semibold mb-2">Practice nights</h3>
        <div class="border rounded-3 overflow-hidden" style="max-width:420px">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th class="fw-normal text-muted small border-0">Grade</th>
                <th class="fw-normal text-muted small border-0">Nights</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ( $practice_nights as $row ) : ?>
              <tr>
                <td class="small"><?php echo esc_html( $row['grade'] ); ?></td>
                <td class="small"><?php echo esc_html( $row['nights'] ); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
      <?php endif; ?>

      <?php // ── 7. Sessions ─────────────────────────────────────────────────── ?>
      <?php if ( ! $sub_programs && $sessions ) : ?>
      <section class="mb-5">
        <h2 class="h6 fw-semibold mb-3">Sessions</h2>
        <div class="row g-3">
          <?php foreach ( $sessions as $s ) : ?>
          <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
              <h3 class="h6 fw-semibold mb-2"><?php echo esc_html( $s['session_label'] ); ?></h3>
              <?php // Rows saved before the date pickers existed have no start/end keys at all. ?>
              <?php $session_dates = nsfc_format_date_range( $s['session_start_date'] ?? '', $s['session_end_date'] ?? '' ); ?>
              <?php if ( $session_dates ) : ?>
                <p class="small text-muted mb-1"><?php echo esc_html( $session_dates ); ?></p>
              <?php endif; ?>
              <?php if ( $s['session_cost'] ) : ?>
                <p class="small fw-medium mb-0"><?php echo esc_html( $s['session_cost'] ); ?></p>
              <?php endif; ?>
              <?php if ( $s['session_note'] ) : ?>
                <p class="small text-muted mb-0"><?php echo esc_html( $s['session_note'] ); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <?php // ── Sub-programs (replaces Key Details/Pricing/Schedule/Sessions/Registration above and below) ── ?>
      <?php if ( $sub_programs ) : ?>
        <?php foreach ( $sub_programs as $i => $sp ) : ?>
        <section class="<?php echo $i === 0 ? '' : 'border-top pt-5 '; ?>mb-5">
          <h2 class="h5 fw-semibold mb-1"><?php echo esc_html( $sp['name'] ); ?></h2>
          <?php if ( $sp['age_label'] ) : ?>
            <p class="text-muted small mb-4"><?php echo esc_html( $sp['age_label'] ); ?></p>
          <?php endif; ?>
          <?php if ( $sp['description'] ) : ?>
            <p class="mb-4"><?php echo esc_html( $sp['description'] ); ?></p>
          <?php endif; ?>

          <?php if ( ! empty( $sp['details'] ) ) : ?>
            <?php foreach ( $sp['details'] as $detail ) : ?>
              <?php if ( $detail['label'] && $detail['detail_value'] ) : ?>
              <div class="mb-4">
                <h3 class="h6 fw-semibold mb-1"><?php echo esc_html( $detail['label'] ); ?></h3>
                <p class="mb-0"><?php echo esc_html( $detail['detail_value'] ); ?></p>
              </div>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ( ! empty( $sp['costs'] ) ) : ?>
            <h3 class="h6 fw-semibold mb-3">Cost</h3>
            <div class="border rounded-3 overflow-hidden mb-4" style="max-width:320px">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="fw-normal text-muted small border-0"></th>
                    <th class="fw-normal text-muted small border-0 text-end">Cost</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ( $sp['costs'] as $row ) : ?>
                  <tr>
                    <td class="small"><?php echo esc_html( $row['label'] ); ?></td>
                    <td class="small text-end fw-medium"><?php echo esc_html( $row['cost'] ); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>

          <?php if ( ! empty( $sp['sessions'] ) ) : ?>
            <h3 class="h6 fw-semibold mb-3">Sessions</h3>
            <div class="row g-3 mb-4">
              <?php foreach ( $sp['sessions'] as $session ) : ?>
              <div class="col-sm-6">
                <div class="border rounded-3 p-3 h-100">
                  <h4 class="h6 fw-semibold mb-3"><?php echo esc_html( $session['session_label'] ); ?></h4>
                  <?php if ( $session['venue'] ) : ?>
                    <p class="small text-muted mb-2"><?php echo esc_html( $session['venue'] ); ?></p>
                  <?php endif; ?>
                  <?php if ( ! empty( $session['schedule'] ) ) : ?>
                    <table class="table table-sm table-borderless mb-0"><tbody>
                      <?php foreach ( $session['schedule'] as $row ) : ?>
                      <tr>
                        <td class="small text-muted ps-0 pe-3 py-1"><?php echo esc_html( $row['day'] ); ?></td>
                        <td class="small ps-0 py-1"><?php echo esc_html( trim( $row['dates'] . ( $row['time'] ? ' · ' . $row['time'] : '' ) ) ); ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody></table>
                  <?php endif; ?>
                  <?php if ( $session['note'] ) : ?>
                    <p class="small text-muted mb-0"><?php echo esc_html( $session['note'] ); ?></p>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php
          $open_sessions = array_filter( $sp['sessions'] ?? [], fn( $s ) => ! empty( $s['registration_url'] ) );
          if ( $open_sessions ) :
          ?>
            <div class="dropdown mt-4">
              <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Register — <?php echo esc_html( $sp['name'] ); ?></button>
              <ul class="dropdown-menu">
                <?php foreach ( $open_sessions as $session ) : ?>
                <li><a class="dropdown-item" href="<?php echo esc_url( $session['registration_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $session['registration_label'] ?: $session['session_label'] ); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </section>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php // ── 8. Notes callout ────────────────────────────────────────────── ?>
      <?php if ( $notes ) : ?>
      <section class="mb-5">
        <div class="alert alert-light border">
          <p class="small mb-0"><?php echo wp_kses_post( $notes ); ?></p>
        </div>
      </section>
      <?php endif; ?>

      <?php // ── 9. Financial assistance ──────────────────────────────────────── ?>
      <?php if ( $show_financial_aid && ! empty( $financial_aid['steps'] ) ) : ?>
      <section class="border-top pt-4 mb-5">
        <h2 class="h6 fw-semibold mb-2">Financial assistance</h2>
        <p class="small text-muted mb-3">North Star FC believes every child should have the opportunity to play. Financial aid is available through PlayMetrics during registration.</p>
        <ol class="small text-muted ps-4 mb-2">
          <?php foreach ( $financial_aid['steps'] as $step ) : ?>
            <li class="mb-1"><?php echo esc_html( $step ); ?></li>
          <?php endforeach; ?>
        </ol>
        <?php if ( $financial_aid['note'] ) : ?>
          <p class="small text-muted mb-0"><?php echo esc_html( $financial_aid['note'] ); ?></p>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <?php // ── 10. Registration block (always last content section) ────────── ?>
      <?php
      // Every registration link that's actually filled in, in a fixed order.
      // This used to be an if/elseif chain keyed on Boys/Girls → Team/Individual
      // → External, which silently dropped anything outside the first matching
      // branch: a Team-only program showed no Coaches block and no Registration
      // Note at all, because both lived inside the Boys/Girls branch. Whatever
      // is entered now renders.
      $reg_buttons = array_filter( [
          'Register — Boys →'       => $reg_url_boys,
          'Register — Girls →'      => $reg_url_girls,
          'Register — Team →'       => $reg_url_team,
          'Register — Individual →' => $reg_url_indiv,
          ( $external_link_label ?: 'Register →' ) => $external_link_url,
      ] );

      $has_coaching_block = $has_coaching && ( $coaching_note || ( $coaching_href && $coaching_label ) );
      $has_any_reg        = ! $sub_programs && ( $reg_buttons || $reg_window || $reg_note || $has_coaching_block );
      if ( $has_any_reg ) :
      ?>
      <section class="border-top pt-4 mb-5">
        <h2 class="h5 fw-bold mb-4">Ready to register?</h2>

        <div class="row g-4">
          <div class="<?php echo $has_coaching_block ? 'col-md-6' : 'col-12'; ?>">
            <?php if ( $reg_window ) : ?>
              <p class="small text-muted mb-1"><?php echo esc_html( $reg_window ); ?></p>
            <?php endif; ?>
            <?php if ( $reg_note ) : ?>
              <p class="small text-muted mb-3"><?php echo esc_html( $reg_note ); ?></p>
            <?php endif; ?>
            <?php if ( $reg_buttons ) : ?>
            <div class="d-flex flex-column gap-2">
              <?php foreach ( $reg_buttons as $label => $url ) : ?>
                <a href="<?php echo esc_url( $url ); ?>" class="btn btn-dark" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>

          <?php if ( $has_coaching_block ) : ?>
          <div class="col-md-6">
            <h3 class="h6 fw-semibold mb-2">Coaches</h3>
            <?php if ( $coaching_note ) : ?>
              <p class="small text-muted mb-4"><?php echo esc_html( $coaching_note ); ?></p>
            <?php endif; ?>
            <?php if ( $coaching_href && $coaching_label ) : ?>
              <a href="<?php echo esc_url( $coaching_href ); ?>" class="small text-primary"><?php echo esc_html( $coaching_label ); ?> →</a>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </section>
      <?php endif; ?>

      <?php // ── 11. Contact footer + back link ──────────────────────────────── ?>
      <div class="border-top pt-4">
        <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
        <a href="<?php echo esc_url( wp_get_referer() ?: get_home_url() ); ?>" class="btn btn-link btn-sm text-muted p-0 ms-3">← Back</a>
      </div>

    </div>
  </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
