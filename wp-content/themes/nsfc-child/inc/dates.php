<?php
defined( 'ABSPATH' ) || exit;

/**
 * Turn a start/end date pair into the string shown on the front end.
 *
 * Real dates are stored in `date` fields and the display text is derived from
 * them, rather than being typed a second time by hand — that's what keeps the
 * two from drifting apart. (Camp Sessions predate this and do it the other way:
 * `_start_date`/`_end_date` exist only to sort by, and every camp's visible
 * dates come from the separately hand-typed `date_label`.)
 *
 * Dates are pickers only — there is deliberately no free-text alternative. A
 * text override briefly existed alongside these for values that weren't really
 * dates (month-only spans, lists of individual meeting days, "dates TBD"), but
 * it let a stale string silently hide the real dates, and having two fields
 * meaning the same thing was the confusion it was supposed to solve. All 24
 * stored values were converted to real dates on 2026-08-16 and the text fields
 * removed. A program with nothing picked simply shows no dates.
 *
 * House style, matching the camp cards' "Jun 8–11":
 *   same month   Sep 7–24, 2026
 *   same year    Sep 7 – Oct 24, 2026
 *   across years Oct 20, 2026 – Mar 27, 2027
 */
function nsfc_format_date_range( $start, $end ) {
    $s = $start ? strtotime( $start ) : false;
    $e = $end ? strtotime( $end ) : false;

    if ( ! $s && ! $e ) {
        return '';
    }
    if ( ! $e ) {
        return 'Starts ' . date_i18n( 'M j, Y', $s );
    }
    if ( ! $s ) {
        return 'Through ' . date_i18n( 'M j, Y', $e );
    }

    // An end date before the start date means someone picked the wrong one.
    // Show both in full rather than rendering a nonsense compacted range.
    if ( $e < $s || date_i18n( 'Y', $s ) !== date_i18n( 'Y', $e ) ) {
        return date_i18n( 'M j, Y', $s ) . ' – ' . date_i18n( 'M j, Y', $e );
    }
    if ( date_i18n( 'n', $s ) === date_i18n( 'n', $e ) ) {
        return date_i18n( 'M j', $s ) . '–' . date_i18n( 'j, Y', $e );
    }

    return date_i18n( 'M j', $s ) . ' – ' . date_i18n( 'M j, Y', $e );
}

/**
 * The formatted date range for a program post, from its own meta.
 */
function nsfc_program_date_range( $post_id ) {
    return nsfc_format_date_range(
        carbon_get_post_meta( $post_id, 'start_date' ),
        carbon_get_post_meta( $post_id, 'end_date' )
    );
}
