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
 * Every weekly occurrence between two dates, inclusive.
 *
 * Sub-program classes meet weekly for a run of a few weeks, which is why they
 * are stored as a first/last date rather than one picker per class: entering 16
 * dates for a four-week, four-day-a-week session would be worse than the free
 * text this replaced, not better. All 36 stored rows were verified strictly
 * weekly before the conversion.
 *
 * Steps with '+7 days' rather than 7*86400 so a run crossing a DST boundary
 * stays on the same weekday.
 */
function nsfc_weekly_meetings( $start, $end ) {
    $s = $start ? strtotime( $start ) : false;
    if ( ! $s ) {
        return [];
    }

    $e = $end ? strtotime( $end ) : $s;
    if ( $e < $s ) {
        $e = $s;
    }

    $meetings = [];
    for ( $t = $s; $t <= $e; $t = strtotime( '+7 days', $t ) ) {
        $meetings[] = $t;
    }

    return $meetings;
}

/**
 * A weekly run as the day name and date list it used to be typed as by hand —
 * e.g. ['day' => 'Mondays', 'dates' => 'Feb 2, 9, 16, 23'].
 *
 * Both halves are derived, so the weekday can no longer disagree with the dates
 * (the old free-text pair could say "Mondays" above a list of Tuesdays).
 */
function nsfc_weekly_schedule_label( $start, $end ) {
    $meetings = nsfc_weekly_meetings( $start, $end );
    if ( ! $meetings ) {
        return [ 'day' => '', 'dates' => '' ];
    }

    $by_month = [];
    foreach ( $meetings as $t ) {
        $by_month[ date_i18n( 'M', $t ) ][] = date_i18n( 'j', $t );
    }

    $parts = [];
    foreach ( $by_month as $month => $days ) {
        $parts[] = $month . ' ' . implode( ', ', $days );
    }

    return [
        'day'   => date_i18n( 'l', $meetings[0] ) . 's',
        'dates' => implode( ' · ', $parts ),
    ];
}

/**
 * The formatted date range for a program — its Key details dates when set,
 * otherwise the full span of everything scheduled underneath it.
 *
 * Every listing card shows title / age / description / dates, and most programs
 * only carry dates on their sessions: a Youth League has three separately-dated
 * sessions and nothing at program level. Deriving the span keeps those cards
 * complete without asking anyone to retype dates that are already entered a
 * level down, and without the two copies drifting apart.
 *
 * The program's own Start/End dates are the override. Set them when the derived
 * span is wrong or misleading — most importantly on a program tagged to several
 * seasons, where one span necessarily reads oddly on some of its season pages.
 */
function nsfc_program_date_range( $post_id ) {
    $start = carbon_get_post_meta( $post_id, 'start_date' );
    $end   = carbon_get_post_meta( $post_id, 'end_date' );

    if ( $start || $end ) {
        return nsfc_format_date_range( $start, $end );
    }

    $starts = [];
    $ends   = [];

    $collect = function ( $row, $start_key, $end_key ) use ( &$starts, &$ends ) {
        if ( ! empty( $row[ $start_key ] ) ) {
            $starts[] = $row[ $start_key ];
        }
        if ( ! empty( $row[ $end_key ] ) ) {
            $ends[] = $row[ $end_key ];
        }
    };

    foreach ( (array) carbon_get_post_meta( $post_id, 'sessions' ) as $session ) {
        $collect( $session, 'session_start_date', 'session_end_date' );
        foreach ( (array) ( $session['session_times'] ?? [] ) as $time ) {
            $collect( $time, 'start_date', 'end_date' );
        }
    }

    foreach ( (array) carbon_get_post_meta( $post_id, 'sub_programs' ) as $sub ) {
        foreach ( (array) ( $sub['sessions'] ?? [] ) as $session ) {
            foreach ( (array) ( $session['schedule'] ?? [] ) as $row ) {
                $collect( $row, 'start_date', 'end_date' );
            }
        }
    }

    if ( ! $starts && ! $ends ) {
        return '';
    }

    // Y-m-d sorts correctly as a string.
    sort( $starts );
    sort( $ends );

    return nsfc_format_date_range(
        $starts ? reset( $starts ) : '',
        $ends ? end( $ends ) : ''
    );
}
