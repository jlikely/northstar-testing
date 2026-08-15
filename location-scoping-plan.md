# Location-scoped programs, camps, and leagues — WordPress tracking

Working plan for making Competitive/Recreational/Camps fully self-serve and
location-aware for all 4 North Star FC locations (Rochester, Austin, Albert
Lea, Winona), plus small admin-usability fixes surfaced along the way. This
file is specific to the WordPress build (`wordpress/`) — it replaces the
repo-root `location-scoping-plan.md`, which was written for the React
prototype's data model (`locations: string[]` on JS objects) and doesn't
apply here. That file was deleted; this one is the single tracking doc for
location-scoping work.

**Resuming cold?** Read this whole file, then jump to the first unchecked
phase under Phases. Every phase below has the actual code/data needed to do
it — you shouldn't need to re-derive anything by reading templates again,
though it's always fine to double check against current code since this was
all captured as of 2026-08-14.

## Why

WordPress already solved most of what the prototype's plan set out to do —
earlier, on 2026-08-10 — through a `program_location` taxonomy instead of a
JS array. Reviewing the actual templates and live DB confirmed:

- **`program_location` is the centralized location source.**
  `nsfc_location_term_options()` (in `inc/carbon-fields.php`) reads it live,
  and every location dropdown site-wide (Location Hub, Season Landing, Camps
  Hub, Camps Season, and the new Level Hub field below) pulls from that one
  function. Add a 5th location via **Programs → Locations** in wp-admin and
  it appears everywhere immediately — no code change.
- **Sharing vs. location-specific offerings already works, including
  Leagues.** `program_location` is multi-select (checkbox) on the Program
  CPT — a location-specific offering is tagged with just that location; an
  offering shared across some locations (e.g. a regional Academy) is one
  post tagged with each. Leagues aren't a separate content type — confirmed
  in `documentation/adding-a-league.md`: a league is a Program post using
  the Sessions field, same mechanism as any other multi-session program.
  One taxonomy covers Programs, Leagues, and Camps (via the same taxonomy on
  `camp-session`).
- **Filtering already works.** `camps-season.php` / `season-landing.php`
  filter by matching `program_location` against the page's own location —
  proven live by Rochester's 44 tagged posts showing up only on Rochester's
  pages.
- **The real gap:** `/youth-soccer/{location}/competitive/` and
  `/recreational/` — the "hub" level one step above the season pages — have
  no template at all, not even for Rochester. Those pages, and the 3 other
  locations' equivalent Competitive/Recreational/Camps pages, are hand-typed
  "Plain Page" HTML.
- **Two smaller gaps surfaced during review** — ~~taxonomy seeding in code
  only creates the `rochester` term (the other 3 exist live only because
  someone added them by hand in wp-admin), and there's no way to filter the
  Programs/Camp Sessions admin list tables by location.~~ **Both closed by
  Phases 1 and 2 (2026-08-15).** This bullet is kept as the original review
  finding; see those phases for what was actually done. The real gap above —
  the missing level-hub template — is still open and is what Phases 3–5
  address.

## Deferred (not in this plan)

- Home page's hand-duplicated location card grid (Kadence block, can't call
  PHP/`carbon_get_post_meta()`) — already a known/flagged exception from the
  original locations self-serve work, unresolved.

## Prerequisite — version control

A GitHub repo was required before any code phase (1–5) below started, so this
work is tracked and reversible. Phase 0 (this file + deleting the old root
plan) was the exception — it ran first, as plain file operations, to make the
initial git commit meaningful.

**Met as of 2026-08-15.** The repo now has history and a remote:
`https://github.com/jlikely/northstar-testing.git`. Work is happening on
branch `feature/locations-update`. **Phases 1–5 are unblocked** — Phases 1–3 are
done; Phase 4 is the next actionable step (the first that touches live pages).

**Git does not cover the database.** Version control tracks
`wp-content/themes/nsfc-child/` only. Phase 4 clears `post_content` on live
pages and Phase 5 clears 9 more and creates 27 — none of that is recoverable
from git, no matter how clean the commit history is. **Two separate exports
are required**, because Phase 4's snapshot is already stale by the time
Phase 5 destroys different content:

```sh
ddev export-db --file=pre-phase4.sql.gz   # before Phase 4 (posts 7, 8)
ddev export-db --file=pre-phase5.sql.gz   # before Phase 5 (posts 117–127)
```

Also dump the affected `post_content` values to individual files as a
copy-paste fallback — posts 7/8 before Phase 4, posts 117/118/119, 121/122/123,
125/126/127 before Phase 5. Those nine hold the only copy of the
northstarfc.com link-out markup that exists anywhere.

## Reference — page/post IDs captured live (2026-08-14, via `ddev wp`)

Re-verify with `ddev wp post list --post_type=page --fields=ID,post_title,post_name,post_parent --format=csv`
if it's been a while — these are what existed as of this session.

| ID | Title | Slug | Parent | Current template |
|---|---|---|---|---|
| 100 | Rochester | `rochester` | 6 (Youth Soccer) | `location-hub.php` |
| 7 | Competitive | `competitive` | 100 | `plain.php` → **retrofit to `level-hub.php` in Phase 4** |
| 8 | Recreational | `recreational` | 100 | `plain.php` → **retrofit to `level-hub.php` in Phase 4** |
| 9 | Camps & Clinics | `camps` | 100 | `camps-hub.php` (already done) |
| 39/40/41 | Spring/Summer, Fall, Winter | `spring-summer`/`fall`/`winter` | 7 | `season-landing.php` (already done, don't touch) |
| 42/43/44 | Spring/Summer, Fall, Winter | `spring-summer`/`fall`/`winter` | 8 | `season-landing.php` (already done, don't touch) |
| 73/74/75 | Spring/Summer, Fall, Winter Camps | `spring-summer`/`fall`/`winter` | 9 | `camps-season.php` (already done, don't touch) |
| 76 | Find Your Program | `find-your-program` | 100 | (Opportunity Finder — don't touch) |
| 116 | Austin | `austin` | 6 | `location-hub.php` |
| 117/118/119 | Competitive/Recreational/Camps | `competitive`/`recreational`/`camps` | 116 | `plain.php` → retrofit in Phase 5 |
| 120 | Albert Lea | `albert-lea` | 6 | `location-hub.php` |
| 121/122/123 | Competitive/Recreational/Camps | `competitive`/`recreational`/`camps` | 120 | `plain.php` → retrofit in Phase 5 |
| 124 | Winona | `winona` | 6 | `location-hub.php` |
| 125/126/127 | Competitive/Recreational/Camps | `competitive`/`recreational`/`camps` | 124 | `plain.php` → retrofit in Phase 5 |

`program_location` taxonomy terms (all 4 already exist live): `rochester`
(44 tagged posts), `austin`, `albert-lea`, `winona` (0 tagged posts each —
expected, no real content yet).

**Page 76 (Find Your Program) is Rochester-only and stays that way.**
`location-hub.php` (line 56) gates the "Not sure where to start?" Opportunity
Finder card on `get_page_by_path("youth-soccer/{$location_slug}/find-your-program")`,
so Austin/Albert Lea/Winona simply won't render that card. That is correct
behavior, not a Phase 5 bug — don't "fix" it during click-through.

**Precedent confirmed:** posts using a fully-custom template that never
calls `the_content()` (post 9 `camps-hub.php`, post 100 `location-hub.php`)
both have empty `post_content` in the DB. Do the same when retrofitting 7/8
and 117–127 onto `level-hub.php`/`camps-hub.php` in Phases 4–5 — clear
`post_content`, don't leave the old hand-typed HTML sitting there unused.

## Phases

- [x] **Phase 0 — Tracking file swap.** This file created; root
  `location-scoping-plan.md` deleted (confirmed with the user: prototype-only,
  never implemented, not applicable to WordPress).

- [x] **Phase 1 — Taxonomy seeding fix.** *(done 2026-08-15)*
  `nsfc_seed_taxonomy_terms()` in `inc/taxonomies.php` currently only seeds
  the `rochester` term:
  ```php
  $locations = [ 'rochester' => 'Rochester' ];
  ```
  Update to all 4 (matches the live terms confirmed above):
  ```php
  $locations = [
      'rochester'   => 'Rochester',
      'austin'      => 'Austin',
      'albert-lea'  => 'Albert Lea',
      'winona'      => 'Winona',
  ];
  ```
  `term_exists()` already guards each insert in that function, so this is a
  no-op against the current live DB and only matters for a fresh install.
  **Verify:** `ddev wp term list program_location` still shows exactly 4
  terms (no duplicates); `ddev logs` shows no PHP errors after reload.

  **Also done in this phase — live term-name repair (found during
  implementation, not anticipated above).** The three hand-added terms had
  been saved with slug-style *names*, not display names: `austin`,
  `albert-lea`, `winona` (only `rochester` was correct). That matters because
  `nsfc_location_term_options()` returns `$term->slug => $term->name`, and
  `camps-hub.php:39` / `camps-season.php:22` render that name as the page
  subtitle and back-link — as will `level-hub.php` per Phase 3. Left alone,
  Phase 5 would have published pages subtitled "austin" with a "← austin"
  back-link, and Phase 2's admin filter would have listed lowercase slugs.

  The `term_exists()` guards mean the code change above **cannot** fix this —
  existing terms are skipped, so the corrected names never get inserted. It
  needed a one-time update, names only, slugs untouched:
  ```sh
  ddev wp term update program_location austin     --by=slug --name="Austin"
  ddev wp term update program_location albert-lea --by=slug --name="Albert Lea"
  ddev wp term update program_location winona     --by=slug --name="Winona"
  ```
  **This is a database change and is therefore not in git** — this note is
  the only record of it. A restore from any DB dump taken before 2026-08-15
  will reintroduce the lowercase names; re-run the three commands if so.

  *Verified:* 4 terms, no duplicates after `init` re-ran; Rochester still 44
  tagged posts; 16 programs / 28 camp sessions unchanged;
  `nsfc_location_term_options()` returns all four proper display names;
  `/youth-soccer/rochester/camps/` still renders "Rochester" and "← Rochester"
  at HTTP 200 with zero PHP errors in the body or `ddev logs`.

- [x] **Phase 2 — Admin location filter.** *(done 2026-08-15)*
  No `restrict_manage_posts` hook exists anywhere in the theme today
  (confirmed by grep) and WP core's `WP_Posts_List_Table::extra_tablenav()`
  only auto-builds filters for category/date/format, not custom taxonomies
  — so **Programs → All Programs** and **Camp Sessions → All Camp Sessions**
  currently have no way to narrow by location. Add to `inc/taxonomies.php`
  (or a new `inc/admin-filters.php` if that reads cleaner):
  ```php
  add_action( 'restrict_manage_posts', 'nsfc_admin_location_filter' );
  function nsfc_admin_location_filter( $post_type ) {
      if ( ! in_array( $post_type, [ 'program', 'camp-session' ], true ) ) {
          return;
      }
      $selected = $_GET['program_location'] ?? '';
      echo '<select name="program_location">';
      echo '<option value="">All locations</option>';
      foreach ( nsfc_location_term_options() as $slug => $label ) {
          printf(
              '<option value="%s"%s>%s</option>',
              esc_attr( $slug ),
              selected( $selected, $slug, false ),
              esc_html( $label )
          );
      }
      echo '</select>';
  }

  add_action( 'pre_get_posts', 'nsfc_admin_location_filter_query' );
  function nsfc_admin_location_filter_query( $query ) {
      if ( ! is_admin() || ! $query->is_main_query() || empty( $_GET['program_location'] ) ) {
          return;
      }
      if ( ! in_array( $query->get( 'post_type' ), [ 'program', 'camp-session' ], true ) ) {
          return;
      }
      $query->set( 'tax_query', [ [
          'taxonomy' => 'program_location',
          'field'    => 'slug',
          'terms'    => sanitize_text_field( $_GET['program_location'] ),
      ] ] );
  }
  ```
  Reuses `nsfc_location_term_options()` (same helper every other location
  dropdown uses). Explicit `pre_get_posts` handling rather than relying on
  WP's implicit `$_GET` query-var behavior in admin list queries, for
  predictability.

  **If you create `inc/admin-filters.php`, add a matching `require_once` to
  `functions.php`.** That file lists its four `inc/` includes explicitly
  (`cpt.php`, `taxonomies.php`, `carbon-fields.php`, `location-data.php`) —
  a new file in `inc/` is not auto-loaded and will silently do nothing.
  Appending to `inc/taxonomies.php` avoids this entirely; either location
  works at runtime, because `nsfc_location_term_options()` is defined at the
  top level of `inc/carbon-fields.php` with no "Carbon Fields is loaded"
  guard around it, so it exists long before `restrict_manage_posts` fires
  regardless of include order.
  **Verify:** in wp-admin, both list screens show a Location dropdown;
  filtering to Rochester returns 44 posts combined across both post types;
  filtering to Austin/Albert Lea/Winona returns 0 (until Phase 5+ content
  exists).

  **As implemented.** Went into a new `inc/admin-filters.php`, with the
  matching `require_once` added to `functions.php` (5th include). Small
  additions over the sketch above: a shared `nsfc_admin_filter_post_types()`
  so the post-type list isn't repeated in both hooks, a `screen-reader-text`
  `<label>` matching core's filter markup, `sanitize_key()` on the `$_GET`
  value in both functions, and an early return when
  `nsfc_location_term_options()` is empty (a fresh install before terms seed).

  *Verified* by logging in over `curl` and reading the real list tables, not
  just eyeballing the code — Programs 16/16 for Rochester and 0 for the other
  three, Camp Sessions 28/28 and 0, i.e. the expected 44 combined. An invalid
  slug (`?program_location=bogus-slug`) returns the standard "no items" state
  rather than erroring. `ddev logs` clean.

  **Two false alarms recorded so they aren't re-investigated later:**
  1. Grepping wp-admin HTML for `Warning:` always matches — it's a Yoast SEO
     JS translation string (`variable_warning`), present on every admin page
     and unrelated to PHP. The front-end smoke test in Verification below is
     unaffected (front-end pages don't load it), but don't reuse that grep
     against wp-admin.
  2. `edit.php?post_type=page&program_location=rochester` returns 0 pages.
     This is **not** the filter leaking — `pre_get_posts` above returns early
     for any post type other than `program`/`camp-session`. It's WP core's
     implicit public-taxonomy query-var handling in `WP_Query`; `?season=fall`
     and `?program_level=competitive` do exactly the same on that screen and
     predate this phase. No UI constructs such a URL, so it's left alone.

- [x] **Phase 3 — Build the Level Hub template + fields (code only, not
  applied to any live page yet).** *(done 2026-08-15)*

  **As implemented.** `page-templates/level-hub.php` + a 6th
  `carbon_fields_register_fields` hook calling
  `nsfc_register_level_hub_fields()` in `inc/carbon-fields.php`. Built to the
  spec below, with two deliberate departures:

  - **Intro paragraph classes are assembled with `array_filter` + `implode`,
    not the `printf` sketch below.** The sketch emits
    `class=" text-muted mb-2"` (leading space) for any paragraph that isn't
    the first, which would have been a *sixth* Phase 4 diff line for no
    reason. Verified on the test page that the output now matches posts 7/8
    byte-for-byte: 1 paragraph → `lead text-muted mb-5` (post 7), 3
    paragraphs → `lead text-muted mb-2` / `text-muted mb-2` /
    `text-muted mb-5` (post 8).
  - **`separator` fields are prefixed `nsfc_sep_*`** rather than the `sep_*`
    below, matching the `nsfc_` namespacing every other page-level field in
    that file uses (they all share the `page` post type).

  **A 5th Phase 4 delta was discovered here — see Phase 4.** `get_permalink()`
  returns absolute URLs where posts 7/8 hand-typed root-relative paths. The
  original list of four would have made this look like template drift.

  *Verified* on throwaway page 214, since deleted (trash emptied; page count
  back to 51, 0 pages left on this template): "Level Hub Details" box appears
  with all 9 content fields; the Location dropdown resolves to the 4 real
  taxonomy terms with correct display names; with only Location + one date
  range filled, all 3 cards render and link through while the blank fields
  emit nothing at all (no stray empty `<p>` tags) — the exact condition
  Phase 5's 24 blank-field pages depend on; HTTP 200, zero PHP errors in the
  body, `ddev logs` clean.

  New `page-templates/level-hub.php` (Template Name: `Level Hub`). Follows
  `camps-hub.php`'s shape (read location from Carbon Fields, render 3 fixed
  season cards, no `the_content()`), plus per-season copy fields — unlike
  Camps, which queries real `camp-session` posts, a level hub's cards need
  their own typed date range/description.

  **Markup is specified to reproduce posts 7/8's current HTML exactly**
  (diffed against live `post_content` on 2026-08-15). Where this template
  differs from `camps-hub.php` — `col-lg-9` not `col-lg-8`, `Spring / Summer`
  with spaces, classed intro paragraphs — that is deliberate, so Phase 4's
  retrofit is a clean no-op. Do not "normalize" these to match `camps-hub.php`.

  ```php
  $page_location  = carbon_get_post_meta( get_the_ID(), 'nsfc_location' );
  $location_term  = $page_location ? get_term_by( 'slug', $page_location, 'program_location' ) : false;
  $location_label = $location_term ? $location_term->name : '';
  $intro          = carbon_get_post_meta( get_the_ID(), 'nsfc_intro' );
  $footer_prompt  = carbon_get_post_meta( get_the_ID(), 'nsfc_footer_prompt' );

  $seasons = [
      'spring-summer' => [ 'label' => 'Spring / Summer',
          'date_range' => carbon_get_post_meta( get_the_ID(), 'nsfc_spring_summer_date_range' ),
          'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_spring_summer_description' ) ],
      'fall' => [ 'label' => 'Fall',
          'date_range' => carbon_get_post_meta( get_the_ID(), 'nsfc_fall_date_range' ),
          'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_fall_description' ) ],
      'winter' => [ 'label' => 'Winter',
          'date_range' => carbon_get_post_meta( get_the_ID(), 'nsfc_winter_date_range' ),
          'description' => carbon_get_post_meta( get_the_ID(), 'nsfc_winter_description' ) ],
  ];
  ```

  Render order: breadcrumb (`yoast_breadcrumb()`, same call `camps-hub.php`
  uses) → `<h1 class="display-6 fw-bold mb-1">` (`the_title()`) →
  `<p class="text-muted mb-4">` location subtitle (only if `$location_label`)
  → intro paragraphs (only if `$intro`) → 3 season cards in
  `<div class="row g-3 mb-5">` / `col-sm-4` → footer prompt (only if set) +
  contact + back-link.

  Wrapper is `<div class="container py-5"><div class="row justify-content-center"><div class="col-lg-9">`
  — note `col-lg-9`, matching posts 7/8, not `camps-hub.php`'s `col-lg-8`.

  **Intro** — one textarea, blank line between paragraphs. First paragraph
  gets `lead`, last gets `mb-5`. This reproduces both Competitive (1 para)
  and Recreational (3 paras) from a single field. Use `esc_html`, matching
  how `camps-hub.php` / `location-hub.php` already escape intro copy:
  ```php
  $paras = array_values( array_filter( array_map( 'trim', preg_split( '/\R{2,}/', (string) $intro ) ) ) );
  $last  = count( $paras ) - 1;
  foreach ( $paras as $i => $para ) {
      printf(
          '<p class="%s text-muted %s" style="max-width:600px">%s</p>',
          $i === 0 ? 'lead' : '',
          $i === $last ? 'mb-5' : 'mb-2',
          esc_html( $para )
      );
  }
  ```

  **Card** — both `<p>`s conditionally omitted. That omission is what makes
  the Phase 5 empty state work: leave the fields blank and the cards still
  render (labeled Spring / Summer, Fall, Winter) with no date/description
  text, still linking through to a real season-landing page.
  ```php
  <div class="col-sm-4">
    <div class="card border h-100">
      <div class="card-body d-flex flex-column">
        <h2 class="h6 fw-semibold mb-0">
          <a href="<?php echo esc_url( trailingslashit( get_permalink() ) . $season_slug . '/' ); ?>" class="stretched-link"><?php echo esc_html( $season['label'] ); ?></a>
        </h2>
        <?php if ( $season['date_range'] ) : ?><p class="small text-muted mb-1"><?php echo esc_html( $season['date_range'] ); ?></p><?php endif; ?>
        <?php if ( $season['description'] ) : ?><p class="small text-muted flex-grow-1 mb-0"><?php echo esc_html( $season['description'] ); ?></p><?php endif; ?>
      </div>
    </div>
  </div>
  ```

  **Footer** — `border-top pt-4` (not `camps-hub.php`'s `mt-5`; the card row
  already carries `mb-5`). Back-link via
  `get_page_by_path("youth-soccer/{$page_location}")`, same pattern
  `camps-hub.php` uses.
  ```php
  <div class="border-top pt-4">
    <?php if ( $footer_prompt ) : ?><p class="small text-muted mb-2"><?php echo esc_html( $footer_prompt ); ?></p><?php endif; ?>
    <a href="mailto:info@northstarfc.com" class="btn btn-outline-secondary btn-sm">Contact us</a>
    <!-- back-link -->
  </div>
  ```

  *Cosmetic call already made:* the new template drops Recreational's
  current "Choose a season" eyebrow `h2` (a pre-existing heading-hierarchy
  bug in post 8 — two `h2`s in a row, confirmed live) to match
  Competitive's/`camps-hub.php`'s plainer layout.

  New `nsfc_register_level_hub_fields()` in `inc/carbon-fields.php`, hooked
  alongside the existing five `add_action( 'carbon_fields_register_fields', ... )`
  calls near the top of that file (lines 7–11 as of this writing — add a 6th
  line: `add_action( 'carbon_fields_register_fields', 'nsfc_register_level_hub_fields' );`):
  ```php
  Container::make( 'post_meta', 'Level Hub Details' )
      ->where( 'post_type', '=', 'page' )
      ->where( 'post_template', '=', 'page-templates/level-hub.php' )
      ->add_fields( [
          Field::make( 'select', 'nsfc_location', 'Location' )
              ->add_options( 'nsfc_location_term_options' )->set_required( true ),
          Field::make( 'textarea', 'nsfc_intro', 'Intro paragraph(s)' )
              ->set_help_text( 'Shown under the title. Leave a blank line between paragraphs for multiple.' ),
          Field::make( 'text', 'nsfc_footer_prompt', 'Footer prompt' )
              ->set_help_text( 'Optional line shown above the Contact us button. Leave blank to omit.' ),

          Field::make( 'separator', 'sep_spring_summer', 'Spring / Summer card' ),
          Field::make( 'text', 'nsfc_spring_summer_date_range', 'Date range' ),
          Field::make( 'textarea', 'nsfc_spring_summer_description', 'Description' ),

          Field::make( 'separator', 'sep_fall', 'Fall card' ),
          Field::make( 'text', 'nsfc_fall_date_range', 'Date range' ),
          Field::make( 'textarea', 'nsfc_fall_description', 'Description' ),

          Field::make( 'separator', 'sep_winter', 'Winter card' ),
          Field::make( 'text', 'nsfc_winter_date_range', 'Date range' ),
          Field::make( 'textarea', 'nsfc_winter_description', 'Description' ),
      ] );
  ```
  Flat fields, not `complex`/repeater — seasons are a fixed set of 3,
  matching the `camps-hub.php`/`camps-season.php` convention (a repeater
  would add UI overhead for no flexibility gain). `nsfc_` prefix matches
  every other page-level Carbon Fields box in this file, since they all
  share the `page` post type and need namespacing.

  **No `nsfc_level` field.** An earlier draft of this plan had one, set
  required — but nothing consumed it: season card links derive from
  `get_permalink()`, and the level is already implicit in the page hierarchy
  and the page title. It was dropped rather than shipped as a required field
  admins must fill in for no effect. This does not affect Phase 5's
  season-landing children, which carry their own Level field in the separate
  "Season Landing Details" box — that is the one `season-landing.php`'s
  `tax_query` actually reads.

  **Verify** — this is a code-only phase, so prove it on a throwaway page,
  never on posts 7/8:
  1. Create a test page (any title, no parent), set Template = Level Hub,
     **save once** — Carbon Fields containers scoped with
     `->where( 'post_template', ... )` only appear after the template is
     saved, so an empty sidebar before saving is expected, not a bug.
  2. Confirm the "Level Hub Details" box appears with all 12 fields, and
     that the Location dropdown lists exactly the 4 live `program_location`
     terms (proves `nsfc_location_term_options()` is wired, not hardcoded).
  3. Fill in **only** Location + one season's date range. Load the page and
     confirm: all 3 cards still render with their labels, the filled card
     shows its date line, the other two show label only, and no PHP notice
     appears for the blank fields. This conditional-omission behavior is
     exactly what Phase 5's 24 blank-field pages depend on — if it warns or
     renders empty `<p>` tags here, fix it now, not in Phase 5.
  4. `ddev logs` shows no PHP errors after the render.
  5. Delete the test page (and empty Trash — a lingering Level Hub page in
     Trash muddies Phase 5's `_wp_page_template` spot-checks).

- [ ] **Phase 4 — Retrofit Rochester (posts 7 and 8) onto Level Hub.**
  Highest-risk phase — touches live, currently-working pages. **Take the DB
  export first** (see Prerequisite). Switch `_wp_page_template` to
  `page-templates/level-hub.php`, clear `post_content` to empty (see
  precedent above), set these Carbon Fields values — transcribed verbatim
  from the current hand-typed HTML (re-verified against live `post_content`
  2026-08-15):

  **Post 7 — Competitive:**
  - `nsfc_location` = `rochester`
  - `nsfc_intro` = "Travel teams, league play, and skill development programs. Programs are organized by season."
  - `nsfc_footer_prompt` = "Not sure competitive is the right fit?"
  - Spring / Summer: "March – June" / "Travel teams and league play for U6–U19."
  - Fall: "August – November" / "Travel teams and league play for U6–U19."
  - Winter: "October – April" / "Indoor leagues, futsal, training, and goalkeeper development."

  **Post 8 — Recreational:**
  - `nsfc_location` = `rochester`
  - `nsfc_footer_prompt` = *(blank — post 8 has no such line today)*
  - `nsfc_intro` = three paragraphs, blank line between each:
    "Soccer starts with fun — and it starts early! Our Recreational Soccer
    Program welcomes players beginning at age 3 and continuing through 9th
    grade." / "Players enjoy age-appropriate coaching, skill-building
    activities, and exciting game experiences in a vibrant, positive
    environment. We offer year-round programming so players can stay active
    and improve in every season." / "No tryouts, no pressure — just fun,
    friendships, and soccer."
  - Spring / Summer: "March – August" / "Recreational leagues, youth classes, and Top Soccer."
  - Fall: "August – October" / "Fall recreational leagues for all ages."
  - Winter: "November – March" / "Indoor leagues and training programs."

  Carbon Fields stores these with a `_` prefix, so via WP-CLI it's
  `ddev wp post meta update 7 _nsfc_intro "..."`, `_nsfc_spring_summer_date_range`,
  and so on. Clear content with `ddev wp post update 7 --post_content=""`.

  No changes needed to the existing child season pages (posts 39–44) — they
  already work and this doesn't touch them.

  **Expected rendering change: five small deltas, everything else
  byte-comparable.** The template above was specified to reproduce the live
  markup, but these five are accepted rather than engineered around:
  1. Post 8's h1/subtitle margins shift `mb-2` → `mb-1`/`mb-4` (~4px), standardizing on post 7's spacing.
  2. Post 7's intro `max-width` 520px → 600px (one shared value rather than a per-page field; slightly wider wrap on one line).
  3. Post 8 loses the "Choose a season" eyebrow h2 — **intentional**, fixes the heading-hierarchy bug.
  4. Breadcrumb moves from the `[wpseo_breadcrumb]` shortcode to `yoast_breadcrumb()` — equivalent output, marginally different wrapper markup. Unavoidable when moving from `post_content` to a template.
  5. **Links become absolute URLs.** Posts 7/8 hand-typed root-relative
     hrefs (`/youth-soccer/rochester/competitive/spring-summer/`); the
     template derives them from `get_permalink()`, which returns the full
     URL (`https://northstar-testing.ddev.site/…`). Affects all 3 season
     cards and the back-link on each page. Functionally identical, and it
     matches what `camps-hub.php` already renders on post 9 — so this makes
     posts 7/8 *more* consistent with the site, not less. **Found while
     verifying Phase 3 on the test page (2026-08-15); this delta was missing
     from the original list of four, and would otherwise have tripped the
     "stop and fix the template" rule below as a false alarm.**

  **Verify:** capture both pages *before* retrofitting, then diff:
  ```sh
  curl -s https://northstar-testing.ddev.site/youth-soccer/rochester/competitive/ > /tmp/comp-before.html
  curl -s https://northstar-testing.ddev.site/youth-soccer/rochester/recreational/ > /tmp/rec-before.html
  # ...retrofit...
  curl -s https://northstar-testing.ddev.site/youth-soccer/rochester/competitive/ | diff /tmp/comp-before.html -
  ```
  The diff should contain **only** the five deltas above. Anything else —
  changed container width, a season label reading "Spring/Summer" without
  spaces, a missing footer prompt, unstyled intro paragraphs — means
  `level-hub.php` drifted from spec: stop and fix the template, don't accept
  it. This is the checkpoint that protects the 27-page Phase 5 rollout.

- [ ] **Phase 5 — Non-Rochester rollout (Austin, Albert Lea, Winona).**
  Only start once Phase 4 is verified clean.

  **Take a second DB export first: `ddev export-db --file=pre-phase5.sql.gz`.**
  Phase 4's export does not cover this phase. Nine more pages get their
  `post_content` cleared here (117/118/119, 121/122/123, 125/126/127), and
  those pages hold the *only* copy of the northstarfc.com link-out markup —
  it is not in git and not in the Phase 4 dump if Phase 4 has already been
  committed. Dump those nine `post_content` values to files as well, same
  copy-paste fallback used for posts 7/8.

  **Page titles are specified below and are not cosmetic — see the Camps
  note.** Create pages parent-first, per location.

  - **Competitive/Recreational** (posts 117, 118, 121, 122, 125, 126):
    switch to `level-hub.php`, clear `post_content`, set `nsfc_location` per
    page (matching the table above), leave `nsfc_intro`,
    `nsfc_footer_prompt`, and all 6 season-copy fields blank.
  - **18 new child pages** (3 seasons × 2 levels × 3 locations): publish
    under each of the 6 pages above, template `season-landing.php`, slugs
    `spring-summer` / `fall` / `winter`, **titles `Spring/Summer` / `Fall` /
    `Winter`** (matching posts 39–44), "Season Landing Details" fields set
    to the matching location/level/season. Zero Program posts tagged to
    these locations → existing "No programs listed for this season yet."
    fallback applies automatically, no template change needed.
  - **Camps** (posts 119, 123, 127): switch to `camps-hub.php`, clear
    `post_content`, set `nsfc_location` per page.
  - **9 new child pages** (3 seasons × 3 locations): publish under each
    Camps page above, template `camps-season.php`, slugs `spring-summer` /
    `fall` / `winter`, **titles `Spring/Summer Camps` / `Fall Camps` /
    `Winter Camps`** (matching posts 73–75). "Camps Season Details" fields
    set to matching location/season. Zero `camp-session` posts tagged → the
    existing empty-state fallback applies as-is, no template change needed.

  **Why the Camps titles matter.** `camps-season.php` (line ~211) builds its
  empty state by interpolating the page's own title:
  ```php
  No <?php echo esc_html( get_the_title() ); ?> are currently scheduled. Check back soon.
  ```
  There is no literal "No camps are currently scheduled" string in the theme
  — don't grep for one. Titling these pages `Spring/Summer` instead of
  `Spring/Summer Camps` yields *"No Spring/Summer are currently scheduled."*
  on all 9 pages. Since the empty state **is** the entire visible content of
  every page created in this phase, this is the most visitor-facing detail in
  the rollout. Rochester's posts 73/74/75 are titled correctly already and
  are the reference.

  **Order of operations — parent before children, one location at a time.**
  `level-hub.php`'s season cards link to `{parent-permalink}/{season}/`
  unconditionally, with no `get_page_by_path()` existence check (unlike
  `location-hub.php`'s Opportunity Finder card, which is gated). Retrofitting
  all 9 hub pages first would leave 27 dead links live until the children are
  published. Do Austin fully (retrofit 117 → publish its 3 children → 118 →
  its 3 → 119 → its 3), verify, then Albert Lea, then Winona.

  This replaces the current northstarfc.com link-out placeholder pages with
  a clean empty state (confirmed with the user: acceptable for this
  dev/IA-validation POC, matches the plan's original empty-query-fallback
  intent rather than a hardcoded per-location card).

  **Verify:** click through all 3 locations end-to-end — Location Hub →
  Competitive/Recreational/Camps → each season (empty state, no 404s) →
  back-link works. Read the camps empty-state sentence on at least one page
  per location and confirm it reads as a grammatical sentence. Expect no
  Opportunity Finder card on these 3 locations (page 76 is Rochester-only —
  see the Reference section; correct, not a bug).

  Confirm no *unintended* template changes by diffing the templates
  themselves rather than the page list, which now legitimately contains 27
  new pages:
  ```sh
  ddev wp post list --post_type=page --fields=ID,post_title,post_name --format=csv
  ddev wp post meta get <ID> _wp_page_template   # spot-check the 9 retrofitted hubs
  ```
  The only pages whose `_wp_page_template` should have changed in this phase
  are 117, 118, 121, 122, 125, 126 (→ `level-hub.php`) and 119, 123, 127
  (→ `camps-hub.php`). Posts 7, 8, 9, 39–44, 73–76 and 100/116/120/124 must
  be untouched.

- [ ] **Phase 6 — Documentation.** Only after Phases 1–5 are live-verified.
  - `wordpress/CLAUDE.md`: add a "Level Hub pages" section (parallel to the
    existing "Camps pages" section), update the project-structure file tree
    to list `level-hub.php`, rewrite the "URL structure" section's fallback
    description (currently says non-Rochester locations show "a fallback
    page linking to northstarfc.com" — no longer true).
  - `documentation/adding-a-location.md`: rewrite step 4 — replace the
    hand-typed-placeholder instructions with "use the Level Hub template for
    Competitive/Recreational and the Camps Hub template for Camps, publish 3
    empty season child pages under each," same non-developer, step-by-step
    framing as the rest of the doc. **Also fix the second stale passage at
    ~lines 129–134**, in the closing notes: "Camps pages (step 4) are
    separate and have to be created by hand — and once a location has real
    program details (not just placeholders), those pages get set up
    differently. Ask for help when you get to that point." After Phase 5
    that's no longer true — Camps pages use the same template flow as
    everything else. Spot-check `documentation/removing-a-location.md` and
    `documentation/index.md` for the same stale reference.
  - Consider resolving the breakpoint contradiction: `wordpress/CLAUDE.md`
    says "never use `lg`, `xl`, `xxl`," but `camps-hub.php` and
    `location-hub.php` use `col-lg-8`, posts 7/8 use `col-lg-9`, and
    `level-hub.php` will too. The rule is already false in practice — either
    amend it to allow `lg` for page-shell containers, or normalize the
    templates. Not blocking; just stop the doc from contradicting the code.
  - **Second doc-vs-code contradiction, same fix pass:** CLAUDE.md's
    Conventions section says Bootstrap 5 is "loaded via Kadence — do not
    enqueue separately," but `functions.php` enqueues both the Bootstrap CSS
    and the `bootstrap.bundle.min.js` script directly from jsDelivr. The
    bundle is a genuine runtime dependency — the camps detail modals and
    every registration dropdown are Bootstrap-native and stop working
    without it. Correct the convention text, and add the CDN to CLAUDE.md's
    "Production readiness (deliberately deferred)" list: a real build should
    bundle Bootstrap locally rather than depend on a third-party CDN.
  - Mark this file's Phases 1–6 complete.

## Verification summary (also embedded per-phase above)

No `npm` build and no test suite for the WP theme. Standard proof for this
project: `ddev wp` CLI checks, `ddev logs` for PHP errors, and authenticated
wp-admin/front-end checks — not just "the code looks right." **Don't proceed
to the next phase until the current one's verification is clean.**

### Gate per phase

| Phase | Passes when |
|---|---|
| 1 | `ddev wp term list program_location --fields=slug` returns exactly 4 slugs, no duplicates |
| 2 | Location dropdown appears on both admin list screens; Rochester filters to 44 posts across the two types; other 3 return 0 |
| 3 | Throwaway page renders 3 cards with blank fields, no PHP notices, then is deleted and Trash emptied |
| 4 | `curl` diff of posts 7 and 8 contains **only** the 4 documented deltas |
| 5 | All 27 new pages return HTTP 200 with a grammatical empty state; only the 9 intended `_wp_page_template` values changed |
| 6 | No doc in `documentation/` or CLAUDE.md still describes non-Rochester locations as link-outs |

### Smoke test — run after Phase 4 and again after Phase 5

Catches the two failure modes most likely to slip past a click-through: a
page that 404s because a child slug doesn't exist yet, and a page that
returns 200 while PHP-erroring into the markup.

```sh
BASE=https://northstar-testing.ddev.site

# Every URL this plan touches or creates. After Phase 4, only rochester lines apply.
for loc in rochester austin albert-lea winona; do
  for lvl in competitive recreational; do
    for s in "" spring-summer fall winter; do
      echo "$BASE/youth-soccer/$loc/$lvl/$s"
    done
  done
  for s in "" spring-summer fall winter; do
    echo "$BASE/youth-soccer/$loc/camps/$s"
  done
done | while read -r url; do
  code=$(curl -s -o /tmp/body -w '%{http_code}' "$url")
  err=$(grep -ciE 'Fatal error|Warning:|Notice:|Deprecated:' /tmp/body)
  printf '%s  errors=%s  %s\n' "$code" "$err" "$url"
done
```

Every line must read `200 errors=0`. A `404` means a child page is missing or
a slug is wrong; a non-zero `errors` count means PHP is warning into the
page — most likely a blank Carbon Fields value being used unguarded, which
is precisely the condition Phase 5's 24 blank-field pages create and Phase
3's step 3 is designed to catch early.

Then confirm the log is clean, since PHP can log without printing:

```sh
ddev logs | grep -iE 'PHP (Fatal|Warning|Notice|Deprecated)' | tail -20
```

### Rollback

Each phase is independently reversible, but by *different* means — theme
changes via git, content changes only via the DB exports:

- **Phases 1–3** (code only): `git checkout -- wp-content/themes/nsfc-child/`
- **Phase 4** (posts 7/8): `ddev import-db --file=pre-phase4.sql.gz`
- **Phase 5** (posts 117–127 + 27 new): `ddev import-db --file=pre-phase5.sql.gz`

Importing a dump is a **full database replace**, not a merge — it discards
every content change made since that dump was taken. If Phase 5 goes wrong
after unrelated content edits have happened, prefer restoring the nine
`post_content` files by hand over importing `pre-phase5.sql.gz`.

Commit after each phase's gate passes, so `git checkout` stays a meaningful
undo for the code half.

## Status

Last updated: 2026-08-15. Phases 0–3 complete; the git/GitHub prerequisite above
is now met. **Phase 4 is the next actionable step.** Implement one phase at a
time, verify live, and update this checklist before moving on.

**Reviewed and corrected 2026-08-15** against live theme code and the running
DDEV database. Everything structural checked out — page IDs, taxonomy terms
and counts, template assignments, the Phase 1/2 code gaps, and all of Phase
4's copy. Five corrections were applied:

1. **Phase 3/4 — markup fidelity.** The original Level Hub sketch mirrored
   `camps-hub.php`, which would have silently changed posts 7/8 on retrofit
   (`col-lg-8` vs `col-lg-9`, "Spring/Summer" vs "Spring / Summer", unstyled
   `wpautop` intro, and dropping Competitive's "Not sure competitive is the
   right fit?" line). Template is now specified to reproduce live markup;
   Phase 4 lists the four deltas that remain instead of claiming none.
2. **`nsfc_level` dropped** — was required but never read.
3. **`nsfc_footer_prompt` added** — preserves post 7's footer line, self-serve.
4. **Page 76 (Find Your Program)** added to the Reference table; Phase 5's
   verification rewritten (it would have false-positived on it).
5. **Git-vs-database gap** called out — git covers theme code only, so a DB
   export is now an explicit Phase 4 prerequisite.

**Second review pass, 2026-08-15** — re-verified against live theme code, the
running DDEV database, and the actual page titles in `wp_posts`. Six further
gaps closed, all in the "would have shipped and looked fine until someone
read the page" category:

6. **Camps child page titles specified.** `camps-season.php` interpolates
   `get_the_title()` into its empty-state sentence, so the 9 new Camps pages
   must be titled "Spring/Summer Camps" etc., not "Spring/Summer" — otherwise
   every one of them reads "No Spring/Summer are currently scheduled." Since
   the empty state is the whole visible page in Phase 5, this was the single
   most visitor-facing defect in the plan. The 18 season-landing pages take
   the plain titles, matching posts 39–44.
7. **The quoted camps fallback string was wrong** — no literal "No camps are
   currently scheduled" exists in the theme; it's title-interpolated. Fixed
   so nobody greps for it and concludes the template is broken.
8. **Second DB export added before Phase 5.** Phase 4's dump doesn't cover
   the 9 pages Phase 5 clears, which hold the only copy of the
   northstarfc.com link-out markup.
9. **Phase 5 order of operations pinned** — parent-then-children, one
   location at a time. `level-hub.php`'s season cards have no
   `get_page_by_path()` guard, so retrofitting all 9 hubs first would leave
   27 dead links live.
10. **Phase 2 include gap** — a new `inc/admin-filters.php` would silently
    never load; `functions.php` lists its includes explicitly.
11. **Bootstrap CDN contradiction** added to Phase 6 alongside the existing
    breakpoint one, plus a runnable smoke test and a rollback section in
    Verification.
