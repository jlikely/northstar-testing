<?php
defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'nsfc_register_program_fields' );
add_action( 'carbon_fields_register_fields', 'nsfc_register_location_fields' );
add_action( 'carbon_fields_register_fields', 'nsfc_register_season_landing_fields' );
add_action( 'carbon_fields_register_fields', 'nsfc_register_camps_fields' );
add_action( 'carbon_fields_register_fields', 'nsfc_register_camp_type_fields' );
add_action( 'carbon_fields_register_fields', 'nsfc_register_level_hub_fields' );

/**
 * Every published `program_location` term, as a select-field options array.
 * Used anywhere a page needs to pick "which location" — keeps those dropdowns
 * in sync with Programs → Locations automatically as locations are added.
 */
function nsfc_location_term_options() {
    $terms   = get_terms( [ 'taxonomy' => 'program_location', 'hide_empty' => false ] );
    $options = [];
    foreach ( $terms as $term ) {
        $options[ $term->slug ] = $term->name;
    }
    return $options;
}

function nsfc_register_program_fields() {

    // ── Programs ────────────────────────────────────────────────────────────
    Container::make( 'post_meta', 'Program Details' )
        ->where( 'post_type', '=', 'program' )
        ->add_fields( [

            // Fields are in the order they appear on the program page. The two
            // selects below (Structure, Pricing) exist so the either/or choices
            // in this box are stated rather than inferred: filling Sub-programs
            // used to silently switch off Key details, Pricing, Sessions and
            // Registration, and the three cost fields were mutually exclusive
            // with only help text saying so. Carbon Fields' conditional logic
            // now hides whatever doesn't apply.
            Field::make( 'separator', 'sep_key_details', 'Key details' ),
            // Two summaries, deliberately separate, because they appear in
            // different places and read differently. Named for where they show
            // up rather than for what they are.
            Field::make( 'textarea', 'description', 'Intro — on this program\'s page' )
                ->set_help_text( 'The paragraph under the title when someone opens this program. Plain text, no formatting.' ),
            // Was the WordPress Excerpt, which lived in a sidebar panel outside
            // this box and was easy to miss entirely — and which silently
            // emptied itself on any program whose excerpt had been auto-derived
            // from the block editor content this box replaced.
            Field::make( 'textarea', 'card_description', 'Card description — on listing pages' )
                ->set_help_text( 'One sentence shown on this program\'s card on the season listing pages. It does NOT appear on the program\'s own page, so it can repeat the intro.' ),
            Field::make( 'select', 'program_structure', 'What kind of program is this?' )
                ->add_options( [
                    'single'       => 'One program',
                    'sub_programs' => 'Several named sub-programs',
                ] )
                ->set_default_value( 'single' )
                ->set_help_text( 'Choose "Several named sub-programs" only when one page covers 2+ separately-run offerings that each need their own age range, cost and Register button (e.g. Kickstarters = Lil Dribblers + Junior Kickers). Everything below changes to match.' ),

            // Age and dates stay visible for sub-program programs too, unlike
            // the rest of Key details. Every listing card shows title / age /
            // description / dates, and a sub-program page has no single age or
            // date of its own to fall back on — each sub-program carries its
            // own. So on those, these two feed the card and nothing else.
            Field::make( 'text', 'age_label', 'Age range' )
                ->set_help_text( 'e.g. "Ages 9–12" or "Grades 3–8". Shown as the badge on this program\'s listing card. On a sub-programs page, give the range across all of them ("Ages 3–5") — the page itself still shows each sub-program\'s own.' ),
            Field::make( 'date', 'start_date', 'Start date' )
                ->set_help_text( 'Optional. Leave both dates blank and the listing card works out the span from the sessions below. Fill them in to override that — worth doing on a program tagged to several seasons, where one span reads oddly on some of its season pages.' ),
            Field::make( 'date', 'end_date', 'End date' ),
            Field::make( 'checkbox', 'tryout_required', 'Tryout required' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'format', 'Format' )
                ->set_help_text( 'e.g. "11v11" or "Small-sided"' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            // Was "Venue / Location", which rendered on the page as "Location"
            // and read as if it meant the city — the job of the program_location
            // taxonomy. This is the pitch or building.
            Field::make( 'text', 'venue', 'Venue' )
                ->set_help_text( 'Where it meets — e.g. "Watson Soccer Complex". The city is set separately, in the Location box.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),

            Field::make( 'separator', 'sep_pricing', 'Pricing' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'select', 'pricing_style', 'How is this priced?' )
                ->add_options( [
                    'none'   => 'Not shown here',
                    'single' => 'One price for the whole program',
                    'grade'  => 'A different price per grade',
                    'tiers'  => 'Early-bird and general pricing',
                ] )
                ->set_default_value( 'none' )
                ->set_help_text( 'Pick one. Sessions priced individually are entered on the session itself, further down — leave this on "Not shown here" for those.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'cost_text', 'Price' )
                ->set_help_text( 'e.g. "$185 per player"' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'pricing_style', 'value' => 'single' ],
                ] ),
            Field::make( 'complex', 'costs', 'Price by grade' )
                ->set_help_text( 'One row per grade.' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'pricing_style', 'value' => 'grade' ],
                ] )
                ->add_fields( [
                    Field::make( 'text', 'grade', 'Grade / Label' ),
                    Field::make( 'text', 'cost', 'Cost' ),
                ] ),
            Field::make( 'complex', 'cost_tiers', 'Pricing tiers' )
                ->set_help_text( 'Rendered as columns side by side — typically one row for Early Bird and one for General.' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'pricing_style', 'value' => 'tiers' ],
                ] )
                ->add_fields( [
                    Field::make( 'text', 'tier_label', 'Tier name' )
                        ->set_help_text( 'e.g. "Early Bird — through Aug 1"' ),
                    Field::make( 'complex', 'tier_costs', 'Costs' )
                        ->add_fields( [
                            Field::make( 'text', 'grade', 'Grade' ),
                            Field::make( 'text', 'cost', 'Cost' ),
                        ] ),
                ] ),

            // Sessions — one model for every way a program can be scheduled.
            // Replaced four overlapping field groups (2026-08-16): Practices and
            // Games (each a repeater capped at one row), Practice Nights by
            // Grade, and the old Sessions. Nothing used more than one of them —
            // they were the same shape truncated at different depths, added one
            // per offering as the site grew.
            //
            // A program that runs continuously is ONE session with several
            // meeting-time rows ("Practices", "Games", or a grade name). A
            // program sold in blocks is several sessions, each with its own
            // dates and cost. Both are the same fields.
            Field::make( 'separator', 'sep_sessions', 'Sessions' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'complex', 'sessions', 'Sessions' )
                ->set_help_text( 'One row per separately-priced block ("Session I", "Session II"). A program that just runs straight through a season needs a single row — leave its Name blank and the page shows a "Schedule" heading instead of "Sessions".' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] )
                ->add_fields( [
                    Field::make( 'text', 'session_label', 'Name' )
                        ->set_help_text( 'e.g. "Winter Session I — October". Leave blank on a program with only one session.' ),
                    Field::make( 'date', 'session_start_date', 'Start date' ),
                    Field::make( 'date', 'session_end_date', 'End date' ),
                    Field::make( 'text', 'session_cost', 'Cost' )
                        ->set_help_text( 'Only when this session is priced separately from the others. A single price for the whole program belongs in Pricing above.' ),
                    Field::make( 'complex', 'session_times', 'Meeting times' )
                        ->set_help_text( 'When and where this session actually meets. One row per distinct pattern — "Practices" and "Games", or one row per grade when the night varies by grade.' )
                        ->add_fields( [
                            Field::make( 'text', 'label', 'Label' )
                                ->set_help_text( 'e.g. "Practices", "Games", "3rd–4th grade"' ),
                            Field::make( 'text', 'day', 'Day(s)' )
                                ->set_help_text( 'e.g. "Saturdays" or "Mon / Wed"' ),
                            Field::make( 'text', 'time', 'Time' ),
                            Field::make( 'text', 'venue', 'Venue' )
                                ->set_help_text( 'Only when this differs from the program venue in Key details — e.g. games at a different complex from practices.' ),
                            Field::make( 'date', 'start_date', 'Start date' )
                                ->set_help_text( 'Only when this pattern runs for a shorter span than the session — e.g. games starting a week after practices.' ),
                            Field::make( 'date', 'end_date', 'End date' ),
                        ] ),
                    Field::make( 'text', 'session_note', 'Note' ),
                ] ),

            // Sub-programs — for a program that's really 2+ named offerings
            // bundled together (e.g. "Lil Dribblers" + "Junior Kickers" under
            // "Kickstarters"), each with its own age range, cost, weekly
            // schedule, and Register button. single-program.php renders these
            // stacked sections INSTEAD OF Key details / Pricing / Sessions /
            // Registration. That used to be implicit — filling this in silently
            // switched the others off — and is now driven by the Structure
            // select at the top of the box, which hides them outright.
            Field::make( 'separator', 'sep_sub_programs', 'Sub-programs' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'sub_programs' ] ] ),
            Field::make( 'complex', 'sub_programs', 'Sub-programs' )
                ->set_help_text( 'One row per named offering, each with its own age range, cost, schedule and Register button.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'sub_programs' ] ] )
                ->add_fields( [
                    Field::make( 'text', 'name', 'Name' ),
                    Field::make( 'textarea', 'description', 'Description' ),
                    Field::make( 'text', 'age_label', 'Age range' ),
                    Field::make( 'complex', 'details', 'Extra details' )
                        ->set_help_text( 'Optional. One row per detail — e.g. "Staff ratio" / "8:1", "Class length" / "45 minutes".' )
                        ->add_fields( [
                            Field::make( 'text', 'label', 'Label' ),
                            Field::make( 'text', 'detail_value', 'Value' ),
                        ] ),
                    Field::make( 'complex', 'costs', 'Cost' )
                        ->add_fields( [
                            Field::make( 'text', 'label', 'Label' )
                                ->set_help_text( 'e.g. "3-week class"' ),
                            Field::make( 'text', 'cost', 'Cost' ),
                        ] ),
                    Field::make( 'complex', 'sessions', 'Sessions' )
                        ->set_help_text( 'Each session (e.g. "Winter Session IV — February") can meet on more than one day per week — add one schedule row per day. Fill in a Registration URL once it\'s open; leave it blank for a not-yet-open future session (e.g. "Schedule posted September 1") — it\'ll be left out of the Register dropdown until a link is added.' )
                        ->add_fields( [
                            Field::make( 'text', 'session_label', 'Session name' ),
                            Field::make( 'text', 'venue', 'Venue (optional)' )
                                ->set_help_text( 'Only needed if this session meets somewhere different from other sessions of the same sub-program (e.g. a winter indoor venue vs. a summer outdoor one).' ),
                            // One row per weekday this session meets on. The day
                            // name and the list of dates are both derived from
                            // the two pickers (see nsfc_weekly_schedule_label),
                            // so they can't disagree — the free-text pair this
                            // replaced could say "Mondays" above a list of
                            // Tuesdays. A class meeting twice a week is two rows.
                            Field::make( 'complex', 'schedule', 'Weekly schedule' )
                                ->set_help_text( 'One row per weekday. The site works out the day name and every class date from the two dates below — you don\'t list them.' )
                                ->add_fields( [
                                    Field::make( 'date', 'start_date', 'First class' )
                                        ->set_help_text( 'The weekday is taken from this date.' ),
                                    Field::make( 'date', 'end_date', 'Last class' )
                                        ->set_help_text( 'Classes repeat weekly from the first date up to and including this one.' ),
                                    Field::make( 'text', 'time', 'Time' )
                                        ->set_help_text( 'e.g. "4:30pm", or "4:30pm & 5:30pm" when the same day runs two classes.' ),
                                ] ),
                            Field::make( 'text', 'note', 'Note (optional)' )
                                ->set_help_text( 'Shown under the schedule — or in place of it if the Weekly schedule above is left empty (e.g. "Schedule posted September 1").' ),
                            Field::make( 'text', 'registration_label', 'Registration dropdown label' )
                                ->set_help_text( 'Optional. e.g. "September (opens 8/1)". Defaults to the Session name above if left blank.' ),
                            Field::make( 'text', 'registration_url', 'Registration URL' )
                                ->set_help_text( 'Leave blank if not open yet — this session just won\'t appear in the Register dropdown until a link is added.' ),
                        ] ),
                ] ),

            // Notes and Financial assistance render on every program, including
            // sub-program ones, so neither is conditional.
            // Named "Notes" until 2026-08-16, which read like a general-purpose
            // description and drew copy that belonged in the Intro or the Card
            // description. It is one specific highlighted box in one place.
            Field::make( 'separator', 'sep_notes', 'Callout box' ),
            Field::make( 'textarea', 'notes', 'Callout text' )
                ->set_help_text( 'A single bordered, highlighted box near the bottom of the program page, just above Financial assistance. For a caveat someone must not miss — e.g. "Practice location is not guaranteed." Not a description of the program.' ),

            Field::make( 'separator', 'sep_financial_aid', 'Financial assistance' ),
            Field::make( 'checkbox', 'show_financial_aid', 'Show the Financial assistance section' )
                ->set_help_text( 'The wording is the same on every program and is set site-wide, not here — this only decides whether the section appears.' ),

            // Registration is last in this box because it is last on the page —
            // see "Registration is always last" in CLAUDE.md. Sub-programs carry
            // their own per-session Register dropdown, so this whole group is
            // hidden for them.
            Field::make( 'separator', 'sep_registration', 'Registration' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_window', 'Intro' )
                ->set_help_text( 'First line above the Register buttons — e.g. "Opens June 1".' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_note', 'Second line' )
                ->set_help_text( 'Optional, shown under the Intro above.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_url_boys', 'Register — Boys (URL)' )
                ->set_help_text( 'Every URL filled in below becomes its own button, in this order. Leave blank the ones that don\'t apply.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_url_girls', 'Register — Girls (URL)' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_url_team', 'Register — Team (URL)' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'registration_url_individual', 'Register — Individual (URL)' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'external_link_label', 'Register — Other (button label)' )
                ->set_help_text( 'For a program that registers somewhere none of the four above describes. Defaults to "Register".' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'external_link_url', 'Register — Other (URL)' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),

            Field::make( 'separator', 'sep_coaching', 'Coaches' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'checkbox', 'has_coaching', 'Show a Coaches section' )
                ->set_help_text( 'Appears beside the Register buttons.' )
                ->set_conditional_logic( [ [ 'field' => 'program_structure', 'value' => 'single' ] ] ),
            Field::make( 'text', 'coaching_note', 'Note' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'has_coaching', 'value' => true ],
                ] ),
            Field::make( 'text', 'coaching_contact_label', 'Contact link text' )
                ->set_help_text( 'e.g. "Email our coaching director"' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'has_coaching', 'value' => true ],
                ] ),
            Field::make( 'text', 'coaching_contact_href', 'Contact link URL' )
                ->set_help_text( 'Must start with mailto: for an email address, or https:// for a page. A bare word won\'t work.' )
                ->set_conditional_logic( [
                    [ 'field' => 'program_structure', 'value' => 'single' ],
                    [ 'field' => 'has_coaching', 'value' => true ],
                ] ),

        ] );

    // ── Camp Sessions ────────────────────────────────────────────────────────
    Container::make( 'post_meta', 'Camp Session Details' )
        ->where( 'post_type', '=', 'camp-session' )
        ->add_fields( [
            Field::make( 'text', 'date_label', 'Date Label' )
                ->set_help_text( 'e.g. "Jun 8–11"' ),
            Field::make( 'date', 'start_date', 'Start Date' ),
            Field::make( 'date', 'end_date', 'End Date' ),
            Field::make( 'text', 'venue', 'Venue' ),
            Field::make( 'text', 'ages', 'Ages' ),
            Field::make( 'text', 'session_time', 'Time' ),
            Field::make( 'text', 'cost', 'Cost' ),
            Field::make( 'text', 'registration_url', 'Registration URL' ),
        ] );
}

function nsfc_register_location_fields() {

    // ── Location Hub pages ──────────────────────────────────────────────────
    // Lets an admin fully manage a location (Rochester, Austin, etc.) from the
    // Page editor — no theme file edits, no WP-CLI. The page's own title is used
    // as the location label, so it isn't duplicated here.
    Container::make( 'post_meta', 'Location Details' )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'page-templates/location-hub.php' )
        ->add_fields( [
            Field::make( 'select', 'nsfc_location', 'Location' )
                ->set_help_text( 'Must have a matching term under Programs → Locations first — add it there before it will appear here. Used to build this location\'s URLs.' )
                ->add_options( 'nsfc_location_term_options' )
                ->set_required( true ),
            Field::make( 'textarea', 'nsfc_intro', 'Intro paragraph' )
                ->set_help_text( 'Shown at the top of this location\'s hub page, under the title.' )
                ->set_required( true ),
            Field::make( 'text', 'nsfc_short_desc', 'Short description' )
                ->set_help_text( 'One sentence shown on this location\'s card on the /youth-soccer/ location picker.' )
                ->set_required( true ),
            Field::make( 'set', 'nsfc_offerings', 'Program offerings' )
                ->set_help_text( 'Which program cards to show on this location\'s hub page. Not every location offers all 4 — uncheck any this location doesn\'t have.' )
                ->add_options( [
                    'recreational' => 'Recreational Soccer',
                    'competitive'  => 'Competitive Soccer',
                    'tryouts'      => 'Tryouts',
                    'camps'        => 'Camps & Clinics',
                ] )
                ->set_default_value( [ 'recreational', 'competitive', 'tryouts', 'camps' ] ),
        ] );
}

function nsfc_register_season_landing_fields() {

    // ── Season Landing pages ────────────────────────────────────────────────
    // Each Competitive/Recreational season page (e.g.
    // /youth-soccer/rochester/competitive/fall/) needs to know which
    // location + level + season to query programs for. Previously set via
    // WP-CLI (`ddev wp post meta update {id} _nsfc_location rochester`, etc.)
    // — now editable directly on the page. Field names match the meta keys
    // those WP-CLI commands already wrote, so no data migration was needed
    // when this was added.
    Container::make( 'post_meta', 'Season Landing Details' )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'page-templates/season-landing.php' )
        ->add_fields( [
            Field::make( 'select', 'nsfc_location', 'Location' )
                ->add_options( 'nsfc_location_term_options' )
                ->set_required( true ),
            Field::make( 'select', 'nsfc_level', 'Level' )
                ->add_options( [
                    'competitive'  => 'Competitive',
                    'recreational' => 'Recreational',
                ] )
                ->set_required( true ),
            Field::make( 'select', 'nsfc_season', 'Season' )
                ->add_options( [
                    'spring-summer' => 'Spring/Summer',
                    'fall'          => 'Fall',
                    'winter'        => 'Winter',
                ] )
                ->set_required( true ),
        ] );
}

function nsfc_register_camps_fields() {

    // ── Camps Hub pages ──────────────────────────────────────────────────────
    Container::make( 'post_meta', 'Camps Hub Details' )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'page-templates/camps-hub.php' )
        ->add_fields( [
            Field::make( 'select', 'nsfc_location', 'Location' )
                ->add_options( 'nsfc_location_term_options' )
                ->set_required( true ),
            Field::make( 'textarea', 'nsfc_intro', 'Intro paragraph' )
                ->set_help_text( 'Shown at the top of this location\'s Camps & Clinics page, under the title.' ),
        ] );

    // ── Camps Season pages ──────────────────────────────────────────────────
    // Each season's camps listing (e.g. .../camps/spring-summer/) queries
    // camp-session posts filtered by these two fields.
    Container::make( 'post_meta', 'Camps Season Details' )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'page-templates/camps-season.php' )
        ->add_fields( [
            Field::make( 'select', 'nsfc_location', 'Location' )
                ->add_options( 'nsfc_location_term_options' )
                ->set_required( true ),
            Field::make( 'select', 'nsfc_season', 'Season' )
                ->add_options( [
                    'spring-summer' => 'Spring/Summer',
                    'fall'          => 'Fall',
                    'winter'        => 'Winter',
                ] )
                ->set_required( true ),
            Field::make( 'textarea', 'nsfc_note', 'Note' )
                ->set_help_text( 'Optional. Shown below the camp listing table — or as the entire message when no camps are currently scheduled for this season/location.' ),
        ] );
}

function nsfc_register_level_hub_fields() {

    // ── Level Hub pages ─────────────────────────────────────────────────────
    // Competitive / Recreational landing pages, one step above the season
    // pages (e.g. /youth-soccer/rochester/competitive/). Unlike Camps Season
    // pages, there are no posts to query at this depth — the three season
    // cards' copy is typed here, per page.
    //
    // Flat fields rather than a complex/repeater: seasons are a fixed set of
    // three, matching the camps-hub.php / camps-season.php convention.
    //
    // No Level field. The level is implicit in the page title and hierarchy,
    // and the season card links derive from get_permalink() — nothing would
    // read it. The child Season Landing pages carry their own Level field in
    // the separate box below, which is the one season-landing.php's tax_query
    // actually uses.
    Container::make( 'post_meta', 'Level Hub Details' )
        ->where( 'post_type', '=', 'page' )
        ->where( 'post_template', '=', 'page-templates/level-hub.php' )
        ->add_fields( [
            Field::make( 'select', 'nsfc_location', 'Location' )
                ->add_options( 'nsfc_location_term_options' )
                ->set_required( true ),
            Field::make( 'textarea', 'nsfc_intro', 'Intro paragraph(s)' )
                ->set_help_text( 'Shown under the title. Leave a blank line between paragraphs for multiple. Leave blank to omit entirely.' ),
            Field::make( 'text', 'nsfc_footer_prompt', 'Footer prompt' )
                ->set_help_text( 'Optional line shown above the Contact us button, e.g. "Not sure competitive is the right fit?". Leave blank to omit.' ),

            Field::make( 'separator', 'nsfc_sep_spring_summer', 'Spring / Summer card' ),
            Field::make( 'text', 'nsfc_spring_summer_date_range', 'Date range' )
                ->set_help_text( 'e.g. "March – June". Leave blank to omit this line.' ),
            Field::make( 'textarea', 'nsfc_spring_summer_description', 'Description' )
                ->set_help_text( 'Leave blank to omit. The card still renders and still links through.' ),

            Field::make( 'separator', 'nsfc_sep_fall', 'Fall card' ),
            Field::make( 'text', 'nsfc_fall_date_range', 'Date range' ),
            Field::make( 'textarea', 'nsfc_fall_description', 'Description' ),

            Field::make( 'separator', 'nsfc_sep_winter', 'Winter card' ),
            Field::make( 'text', 'nsfc_winter_date_range', 'Date range' ),
            Field::make( 'textarea', 'nsfc_winter_description', 'Description' ),
        ] );
}

function nsfc_register_camp_type_fields() {

    // ── Camp Type descriptions ──────────────────────────────────────────────
    // One write-up per camp type (Technical, Goalkeeper, World Cup, etc.),
    // shown in every session's "more info" card/modal of that type — write
    // it once here, every matching camp-session reuses it automatically.
    Container::make( 'term_meta', 'Camp Type Description' )
        ->show_on_taxonomy( 'camp_type' )
        ->add_fields( [
            Field::make( 'textarea', 'intro', 'Intro' )
                ->set_help_text( 'A paragraph introducing this camp type. Shown at the top of the "more info" card for every session of this type.' ),
            Field::make( 'complex', 'points', 'Highlights' )
                ->set_help_text( 'Optional. One row per highlight — e.g. Title: "Ball Control", Text: "Players learn how to receive and control the ball using different parts of their body."' )
                ->add_fields( [
                    Field::make( 'text', 'title', 'Title' ),
                    Field::make( 'text', 'text', 'Text' ),
                ] ),
        ] );
}
