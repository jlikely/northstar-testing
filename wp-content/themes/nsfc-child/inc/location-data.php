<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shared content for location hub pages (page-templates/location-hub.php).
 * Per-location content (label, intro, short description, slug) lives on each
 * Page itself via the "Location Details" Carbon Fields box — see
 * inc/carbon-fields.php — not here. This file only holds content that is
 * genuinely identical across every location.
 */

/**
 * The possible program cards a location hub can show. Not every location
 * offers all 4 — each has a `key` matched against the "Program offerings"
 * checkboxes in the Location Details Carbon Fields box (see
 * inc/carbon-fields.php), so location-hub.php only renders the ones this
 * location actually has. Card copy itself (label/badge/description) is
 * shared, since it doesn't vary by location — only whether the card shows.
 * hrefTemplate contains the literal token "{location}", substituted at
 * render time.
 */
function nsfc_location_programs() {
    return [
        [ 'key' => 'recreational', 'label' => 'Recreational Soccer', 'badge' => 'Ages 3–15', 'desc' => 'Low-pressure leagues focused on fun, development, and teamwork.', 'hrefTemplate' => '/youth-soccer/{location}/recreational/' ],
        [ 'key' => 'competitive',  'label' => 'Competitive Soccer',  'badge' => 'Ages 9–19', 'desc' => 'Travel teams with structured training and league play.', 'hrefTemplate' => '/youth-soccer/{location}/competitive/' ],
        [ 'key' => 'tryouts',      'label' => 'Tryouts',             'badge' => 'U11–U18',   'desc' => 'Open to all U11–U18 players. One tryout covers all North Star FC locations and teams.', 'href' => '/tryouts/' ],
        [ 'key' => 'camps',        'label' => 'Camps & Clinics',     'badge' => 'All ages',  'desc' => 'Short-term, skill-focused sessions with no season commitment.', 'hrefTemplate' => '/youth-soccer/{location}/camps/' ],
    ];
}

/**
 * "Already registered?" shortcuts — identical across every location (not
 * location-specific), so no substitution needed.
 * NOTE: prototype links "Contact us" to /club#contact — our Club page has no
 * real content yet (Phase 6.9 stub), so this uses mailto: instead until Club
 * gets built out.
 */
function nsfc_location_registered_links() {
    return [
        [ 'label' => 'PlayMetrics', 'desc' => 'Register, make payments, and manage your account.', 'href' => 'https://northstarfc.com/resources/playmetrics-help/' ],
        [ 'label' => 'Uniforms',    'desc' => 'Order or replace team uniforms.',                     'href' => 'https://northstarfc.com/resources/uniforms/' ],
        [ 'label' => 'Field maps',  'desc' => 'Field locations and directions for games and practices.', 'href' => '/field-maps/' ],
        [ 'label' => 'Contact us',  'desc' => 'Get in touch with the North Star FC staff.',           'href' => 'mailto:info@northstarfc.com' ],
    ];
}

/**
 * A venue's display details, from a venue post ID.
 *
 * Venue fields are stored once on the venue and referenced everywhere else, so
 * every template reads through here rather than assembling its own string —
 * same reasoning as nsfc_financial_aid(). Returns null for an empty or dangling
 * reference so callers can simply skip it.
 *
 * @return array{name:string,address:string,map_url:string}|null
 */
function nsfc_venue( $venue_id ) {
    $venue_id = absint( $venue_id );
    if ( ! $venue_id ) {
        return null;
    }

    $venue = get_post( $venue_id );
    if ( ! $venue || 'venue' !== $venue->post_type || 'publish' !== $venue->post_status ) {
        return null;
    }

    return [
        'name'    => get_the_title( $venue_id ),
        'address' => (string) carbon_get_post_meta( $venue_id, 'nsfc_venue_address' ),
        'map_url' => (string) carbon_get_post_meta( $venue_id, 'nsfc_venue_map_url' ),
    ];
}

/**
 * Just the venue name — for the many places that show it inline in a list.
 */
function nsfc_venue_name( $venue_id ) {
    $venue = nsfc_venue( $venue_id );

    return $venue ? $venue['name'] : '';
}

/**
 * An age group's label, from an age-group post ID.
 *
 * Age fields store an ID and every template reads through here, the same
 * arrangement as nsfc_venue_name(). Returns '' for an empty or dangling
 * reference so callers can just skip it.
 */
function nsfc_age_label( $age_id ) {
    $age_id = absint( $age_id );
    if ( ! $age_id ) {
        return '';
    }

    $age = get_post( $age_id );
    if ( ! $age || 'age-group' !== $age->post_type || 'publish' !== $age->post_status ) {
        return '';
    }

    return get_the_title( $age_id );
}
