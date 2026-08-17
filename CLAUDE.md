# CLAUDE.md — North Star FC WordPress POC

This file tells Claude Code how to work in this WordPress project.

## Starting on a new machine?

The theme scaffold and build plan are already here — no code needs to be regenerated.
The local WordPress environment has NOT been set up yet (Phase 0 and Phase 1 are pending).

**First thing to ask Claude:** "What do I need to install to get started?"
Claude will read `build-plan.yaml` and walk you through Phase 0 step by step.

## What this project is

A WordPress POC that rebuilds the North Star FC information architecture prototype (originally a React SPA). The goal is to validate the IA and navigation patterns using a zero-cost plugin stack before committing to a paid production build.

**The React prototype is not in this repo.** This project used to live at
`NorthStar/wordpress/`, alongside a `NorthStar/prototype/` sibling; as of
2026-08-15 it was copied out to be its own repo root, so `../prototype/` no
longer resolves. The prototype still exists on this machine at:

```
/Users/jamielikely/Desktop/NorthStar/prototype/
```

Its data files (`prototype/src/data/`) remain the authoritative source for
program details, camp sessions, schedules, and costs when entering content.
Nothing in this repo's code depends on that path — only the reference table
below — so a missing prototype breaks content entry, not the site.

## How to continue work across sessions

**Always start here:**

1. Read `build-plan.yaml` in this directory
2. Find the first phase where `status` is not `done`
3. Find the first task in that phase where `status` is not `done`
4. Do that task
5. Mark the task `done` in `build-plan.yaml` when complete
6. Move to the next task

If a task is blocked, set its `status` to `blocked` and add a `blocker` note explaining why. Do not skip ahead to later phases.

**Once every phase in `build-plan.yaml` is `done`:** check `location-scoping-plan.md` (also in this directory) for active follow-on work. It's a separate tracking file, not part of `build-plan.yaml`'s phase list — started 2026-08-14 to make Competitive/Recreational/Camps fully location-aware for all 4 locations. Same resumption pattern: read the whole file, jump to the first unchecked phase.

## Environment

- **Local URL:** https://northstar-testing.ddev.site
- **Admin URL:** https://northstar-testing.ddev.site/wp-admin (user: admin, pass: admin)
- **Run WP-CLI commands:** prefix with `ddev wp` (e.g. `ddev wp post list`)
- **Run Composer:** prefix with `ddev composer` (e.g. `ddev composer require ...`)
- **Start/stop DDEV:** `ddev start` / `ddev stop` from this directory
- **PHP logs:** `ddev logs`

All `ddev` commands must be run from the repo root (this directory).

**DDEV project name is `northstar-testing`.** It was renamed from `nsfc` when
this repo was split out on 2026-08-15, specifically so it gets its own
database volume rather than sharing one with the original copy still at
`~/Desktop/NorthStar/wordpress/`. The two are fully independent — work here
cannot affect that copy's data. Don't rename it back.

`vendor/` (Carbon Fields) is gitignored, so after a fresh clone run
`ddev composer install` before the theme will load.

## Project structure

```
northstar-testing/              ← repo root + DDEV project root (this directory)
├── .ddev/config.yaml           ← DDEV environment config (gitignored)
├── build-plan.yaml             ← Phased build tracker (READ THIS FIRST)
├── CLAUDE.md                   ← This file
├── setup.sh                    ← One-time setup script (run after Phase 0)
├── composer.json               ← Created by ddev composer init
├── vendor/                     ← Carbon Fields lives here (gitignored)
├── wp-content/
│   └── themes/
│       └── nsfc-child/         ← The only code tracked in git
│           ├── style.css       ← Theme declaration
│           ├── functions.php   ← Bootstrap point (loads inc/ files)
│           ├── inc/
│           │   ├── cpt.php             ← CPT registration
│           │   ├── taxonomies.php      ← Taxonomy registration
│           │   ├── carbon-fields.php   ← Field group definitions
│           │   ├── location-data.php   ← Shared location-hub card copy (see "Location Hub pages")
│           │   ├── admin-filters.php   ← Location filter on the Programs/Camp Sessions list tables
│           │   ├── admin-ui.php        ← assets/admin.css enqueue + hides inline term creation
│           │   └── breadcrumbs.php     ← nsfc_breadcrumb() — native trail, no plugin
│           ├── assets/
│           │   └── admin.css           ← wp-admin only: Carbon Fields section headings
│           ├── single-program.php      ← Program detail template
│           ├── archive-camp-session.php ← Orphaned Camp listing template (unlinked, left alone)
│           └── page-templates/
│               ├── location-chooser.php ← /youth-soccer/ location picker
│               ├── location-hub.php    ← Per-location hub page
│               ├── level-hub.php       ← Per-location Competitive/Recreational landing
│               ├── season-landing.php  ← Season hub template
│               ├── camps-hub.php       ← Per-location Camps & Clinics landing
│               ├── camps-season.php    ← One season's camp listing
│               └── plain.php           ← Minimal template (Home, stub pages)
└── (WP core files — gitignored, downloaded by DDEV)
```

## Conventions

### Theme
- **Theme slug:** `nsfc-child`
- **Parent theme:** `kadence`
- **Text domain:** `nsfc`
- **Bootstrap version:** 5 — CSS *and* the JS bundle are enqueued in `functions.php` from the jsDelivr CDN (not supplied by Kadence). Modals and dropdowns depend on the bundle.

### CPTs and taxonomies
| CPT slug | Label |
|---|---|
| `program` | Programs |
| `camp-session` | Camp Sessions |

All four are registered `hierarchical => true` — not because any of them nest,
but because that is what makes the block editor render a **checkbox list**
instead of a free-text token field. A flat taxonomy's token field creates a new
term from whatever you type, which is how three locations originally got
slug-style names. The flag only affects the editing UI; stored terms and
assignments are untouched by it.

**Terms are created on the taxonomy admin screens only** (Programs → Locations,
Camp Sessions → Camp Types, etc.). `nsfc_hide_inline_term_creation()` in
`inc/admin-ui.php` strips the block editor's inline "Add New …" form from all
four taxonomy panels on `program` and `camp-session` posts — the assignment
checkboxes are untouched. Two reasons: the inline form has no Slug field
(auto-slugging a typed name is the same failure mode the `hierarchical` flag
above was meant to close), and creating a location is only step 1 of 4 in
`documentation/adding-a-location.md` — a term made inline shows up in every
location dropdown and admin filter site-wide with no Location Hub page behind
it. It works by removing the post's `wp:action-create-{taxonomy}` REST link,
not by changing capabilities, so the term screens stay fully usable.

**Parent and Description are hidden on those four term screens** — the CSS
rules in `assets/admin.css`, plus `nsfc_hide_term_description_column()` for the
list-table column. Core renders Parent on anything hierarchical, but none of
these nest and setting a parent would indent the term inside another one in
every checkbox list. Term descriptions are read by nothing in the theme (a
location's intro copy is on its Location Hub page; a camp type's description is
the Carbon Fields term-meta box on the same screen), so core's field was a
second, identically-named box whose text went nowhere. Both are scoped by
taxonomy — post categories use the same markup and need both fields. **Name and
Slug are what's left, and Slug is load-bearing:** page meta (`_nsfc_location`,
`_nsfc_season`, `_nsfc_level`) stores the slug, so renaming a slug silently
detaches every page pointing at it. Names are safe to edit; slugs are not.

| Taxonomy slug | Label | Terms |
|---|---|---|
| `season` | Season | `spring-summer`, `fall`, `winter` |
| `program_level` | Level | `competitive`, `recreational` |
| `program_location` | Location | `rochester`, `austin`, `albert-lea`, `winona` |
| `camp_type` | Camp Type | 13 terms (see "Camps pages" below) — `camp-session` only |

### Carbon Fields meta keys
All program fields are stored with the prefix `_` (Carbon Fields default). Access in templates with `carbon_get_post_meta('field_name')`.

### No block editor on Programs or Camp Sessions (removed 2026-08-16)
Both CPTs were registered with `'editor'` support, which is the only reason the
block canvas was ever there. It was actively unsafe on `program`:
`single-program.php` wrapped `get_the_content()` in a `<p class="lead">`
*without* running block filters, so any real block (heading, list, columns)
emitted a `<p>` inside a `<p>`; and the whole paragraph was suppressed on any
program with a structured practice/game schedule, so the canvas silently
discarded whatever was typed there.

A program's lead paragraph is now the **`description`** textarea in the Key
details section, and it always renders — the old "hide it when there's a
schedule" condition is gone. The 2 programs that had `post_content` (210
Kickstarters Classes, 81 Recreation Classes) were moved to it and their
`post_content` cleared.

**The WordPress Excerpt is gone from `program` too** (2026-08-16). It was the
card blurb on the season landing pages, but it lived in a sidebar panel outside
the Program Details box — the single most important field for the listing pages
was the one field not in the ordered box. It is now the `card_description`
Carbon field, second in Key details, and `season-landing.php` reads that.

That move also fixed a regression this same session introduced: post 210 had no
*manual* excerpt, so WordPress had been auto-deriving one from `post_content`.
Clearing `post_content` when the block editor was removed silently emptied its
card. All 14 real excerpts were migrated; 210's was reconstructed from the first
sentence of its Intro.

The two summaries are deliberately separate and named for **where they appear**:
`description` is "Intro — on this program's page", `card_description` is "Card
description — on listing pages". Post 81 has genuinely different text in each.

`camp-session` lost both `editor` and `excerpt` (0 posts used either, and no
template reads them — a camp renders as a card + modal built from Carbon Fields
and its `camp_type` term description). `thumbnail` came off `program` too: no
template in this theme renders a featured image.

### Program Details field order and the two selects (2026-08-16)
The box is ordered **exactly as the page renders**: Key details → Pricing →
Sessions → Sub-programs → Notes → Financial assistance → Registration → Coaches.
Registration sits last because it renders last (see "Registration is always
last"); it used to be second in the box while rendering tenth.

Two `select` fields make the box's either/or choices explicit, with Carbon
Fields conditional logic hiding whatever doesn't apply:

- **`program_structure`** — `single` | `sub_programs`. Filling in Sub-programs
  used to *silently* switch off Key details, Pricing, Sessions and Registration
  in `single-program.php`, with nothing in the admin saying so. Choosing
  "Several named sub-programs" now hides those groups outright. Notes and
  Financial assistance stay visible for both, because both render for both.
- **`pricing_style`** — `none` | `single` | `grade` | `tiers`, selecting between
  `cost_text`, `costs` and `cost_tiers`. Those three were mutually exclusive
  with only help text saying so.

**Both were backfilled on all 17 programs.** A blank select would evaluate as
"no match" and hide every conditional field, so any new program-like content
created outside the editor must set `program_structure` (default `single`).

`cost_text` now renders **in the Pricing section**, not in Key details. Before,
a program using it and a program using the tables each produced an `<h2>Cost` in
a different part of the page; now all three pricing shapes share one heading.

`venue` was relabelled **"Venue"** (was "Venue / Location"). It still renders on
the page under a "Location" heading, which is worth revisiting — the city comes
from the `program_location` taxonomy, and two things called Location is the
confusion this rename was meant to start unpicking.

### Sessions: one scheduling model (consolidated 2026-08-16)
A Program has exactly one way to say when it meets: the **`sessions`** repeater.
It replaced four overlapping groups — `schedule_practices` and `schedule_games`
(each a `complex` capped at one row), `practice_nights`, and the old `sessions`.
**No program used more than one of them**: they were the same shape truncated at
different depths, one added per offering as the site grew. All four old meta
keys were deleted from the database.

Each session row: Name, Start/End date, Cost, a `session_times` repeater
(Label / Day(s) / Time / Venue / optional Start–End), and Note.

- **Several named sessions** = a program sold in separately-priced blocks. The
  page renders "Sessions" as a card grid. 9 programs.
- **One session with a blank Name** = a program that runs straight through a
  season. The page renders "Schedule" full-width instead. 3 rec leagues.

That heading swap is driven by `$is_single_block` in `single-program.php` and
exists so consolidating the field groups didn't change either page's wording.

**Meeting-time rows carry the per-grade schedule.** Fall Rec League's six
`practice_nights` rows became six `session_times` rows labelled by grade, which
retired its old "Varies by grade — see table below" cross-reference and the
separate table that pointed at. Its practices venue moved to the program-level
`venue` field. Venue and dates sit on the *row*, not the session, because
practices and games genuinely differ on both.

**Sub-programs still have their own nested session shape and were not touched.**
Unifying it with this one is the obvious follow-up, but its `schedule[]` rows
store lists of days-of-month ("2/2, 9, 16, 23") that this model can't express.

Snapshot `pre-session-consolidation` holds the pre-migration database.

### Program dates (converted to real dates 2026-08-16)
A Program's Key details dates are **`start_date` + `end_date` `date` fields**,
and the displayed string is derived from them by `nsfc_program_date_range()` /
`nsfc_format_date_range()` in `inc/dates.php` — never typed by hand. Both
`single-program.php` and `season-landing.php`'s program cards call the helper,
so they cannot disagree. House format matches the camp cards: `Jun 8–11, 2026`
(same month), `Sep 7 – Oct 24, 2026` (same year), `Oct 20, 2026 – Mar 27, 2027`
(across years).

**Session dates work the same way**: each `sessions` row has
`session_start_date` / `session_end_date` pickers, rendered through the same
helper. Rows saved before the pickers existed have no start/end keys at all,
hence the `?? ''` guards in `single-program.php`.

**Pickers only — there is no free-text date field, by design.** `date_range`
and `session_dates` briefly survived as text overrides for values that weren't
really dates, then were removed the same day: a stale override silently hid the
real dates beneath it, and two fields meaning "dates" was exactly the confusion
the pickers were meant to end. All 24 stored values were converted on
2026-08-16 and the text fields deleted. A program with nothing picked shows no
dates at all.

**The migrated years are invented.** Only one stored value named its year
(post 98, "Oct 26 – Dec 19, 2025"), so everything was anchored to a 2025-26
season on the user's explicit instruction that this is throwaway POC content.
Three conversions also lost real detail and would need re-entering for a real
build: post 99's two sessions were **lists of individual meeting days**
("Oct 24, 31 · Nov 7, 14, 21 · Dec 5, 12, 19") flattened to their first and
last date; posts 89/90/98 held **month-only** spans ("March – June") given
arbitrary first/last-of-month days; and post 80's Session III dropped a
trailing "· 4 games". Snapshot `pre-date-migration` holds the originals.

**Camp Sessions deliberately still work the other way** and were not changed:
`_start_date`/`_end_date` exist only as a sort key for `camps-season.php`, and
every visible camp date comes from the separately hand-typed `date_label`. That
means a camp's pickers and its label can drift apart. Worth unifying onto
`inc/dates.php` if camps get revisited.

### Location Hub pages (self-serve, added 2026-08-10)
Each location's hub page (Rochester, Austin, Albert Lea, Winona, and any future
location) is a WP Page using the "Location Hub" template
(`page-templates/location-hub.php`), fully editable by a non-developer:
- **Label** = the page's own title — no separate field.
- **Location / Intro / Short description** = the "Location Details" Carbon
  Fields box in the Page editor sidebar (registered in `inc/carbon-fields.php`
  via `nsfc_register_location_fields()`), scoped to pages using that
  template. Meta keys: `_nsfc_location`, `_nsfc_intro`, `_nsfc_short_desc`.
  Location is a select sourced live from `nsfc_location_term_options()` (the
  `program_location` taxonomy) — same helper every other location dropdown in
  the theme uses, so `program_location` is the single source of truth for
  which locations exist. The taxonomy term must be created before it will
  appear in this dropdown.
- **Which program cards show** = the "Program offerings" checkboxes in the
  same box (meta key `_nsfc_offerings`, keys `recreational`/`competitive`/
  `tryouts`/`camps`). Not every location offers all 4 — the card *copy* is
  shared (`nsfc_location_programs()` in `inc/location-data.php`), but each
  location's hub page filters that list against its own checked offerings.
  All 4 existing locations default to all 4 checked.
- **Display order on the `/youth-soccer/` chooser** = Page Attributes → Order.

`location-chooser.php` queries all published pages using the Location Hub
template directly (`get_posts()` by `_wp_page_template` meta) — it does not
read a hardcoded list, so publishing a new Location Hub page makes it appear
on the chooser automatically. `inc/location-data.php` no longer holds
per-location content; it only holds the 4 program cards and "already
registered" links that are genuinely identical across every location.

Adding a 5th location no longer requires a theme file edit or WP-CLI: create
the Page (Parent = Youth Soccer, Template = Location Hub), fill in the 3
fields, publish. See `documentation/adding-a-location.md` for the full
walkthrough. (Still requires standard non-content admin steps outside this
field group — the `program_location` taxonomy term, nav menu item, Home page
card, and fallback Competitive/Recreational/Camps child pages — see that doc.)

**"Location" is set independently in 3 more places**, each self-serve but not
linked to the Hub page's own slug — an admin must set each one to the matching
slug by hand (all read from `nsfc_location_term_options()` in
`inc/carbon-fields.php`, so the dropdown options themselves always match
current `program_location` terms, but the *choice* per page is manual):
- **Individual Program posts** — the native `program_location` taxonomy
  checkbox box on each `program` post (not Carbon Fields, standard WP
  taxonomy UI).
- **Season Landing pages** (`page-templates/season-landing.php`,
  `/youth-soccer/{location}/{level}/{season}/`) — "Season Landing Details"
  box: Location / Level / Season dropdowns. Meta keys `_nsfc_location`,
  `_nsfc_level`, `_nsfc_season` (same keys as before this was self-serve —
  no data migration needed when this box was added).
- **Camps Hub / Camps Season pages** (`page-templates/camps-hub.php` /
  `page-templates/camps-season.php`, `/youth-soccer/{location}/camps/` and
  its season children) — see "Camps pages" below.

### Level Hub pages (self-serve, added 2026-08-15)
`/youth-soccer/{location}/{competitive|recreational}/` — the level between a
Location Hub and its season pages. Until 2026-08-15 this depth had **no
template at all**: even Rochester's two pages were hand-typed Bootstrap HTML
pasted into the editor, and the other 3 locations' were link-out placeholders.
Both are now `page-templates/level-hub.php` (Template Name: "Level Hub").

- **Fields** live in the "Level Hub Details" box
  (`nsfc_register_level_hub_fields()` in `inc/carbon-fields.php`): Location
  (required, from `nsfc_location_term_options()`), Intro paragraph(s), Footer
  prompt, and a Date range + Description pair per season. Meta keys
  `_nsfc_location`, `_nsfc_intro`, `_nsfc_footer_prompt`,
  `_nsfc_{season}_date_range`, `_nsfc_{season}_description`.
- **Every field except Location is optional, by design.** Blank fields emit
  nothing at all — the 3 season cards still render and still link through.
  That's what lets a location with no content published yet (Austin, Albert
  Lea, Winona) use the same template as Rochester rather than a stub.
- **Intro is one textarea, split on blank lines.** First paragraph gets
  `lead`, last gets `mb-5`. One field reproduces both Competitive's single
  paragraph and Recreational's three.
- **No Level field.** The level is implicit in the page title and hierarchy;
  season card links come from `get_permalink()`. The child Season Landing
  pages carry their own Level field — that's the one `season-landing.php`'s
  `tax_query` actually reads.
- **`col-lg-9`, and "Spring / Summer" with spaces** — deliberately unlike
  `camps-hub.php`'s `col-lg-8` and "Spring/Summer". These reproduce the
  hand-typed markup this template replaced. Don't normalize them.

### Camps pages (self-serve, rebuilt 2026-08-10)
Rochester's live Camps & Clinics pages used to be hand-typed static HTML
tables, entirely disconnected from the real `camp-session` CPT posts (which
already had full Carbon Fields data — dates, cost, ages, registration URL —
just unused). A separate, real `archive-camp-session.php` template existed
too, but was orphaned at `/camps/`, linked from nowhere. Both are now unified:
- **`page-templates/camps-hub.php`** (Template Name: "Camps Hub") — the
  `/youth-soccer/{location}/camps/` page. "Camps Hub Details" box: Location
  dropdown, Intro paragraph. Renders 3 fixed season cards (Spring/Summer,
  Fall, Winter) linking to this page's own child pages at `{season-slug}/`.
- **`page-templates/camps-season.php`** (Template Name: "Camps Season") — one
  page per season, child of the Camps Hub page, e.g.
  `/youth-soccer/rochester/camps/spring-summer/`. "Camps Season Details" box:
  Location + Season dropdowns, optional Note (shown below the cards, or as
  the entire message when no camps are scheduled). Queries `camp-session`
  posts by `season` + `program_location` (+ `program_level` if the Level
  filter below is active), ordered by `_start_date`.
- `archive-camp-session.php` (the orphaned `/camps/` archive) still exists
  but remains unlinked; not part of the real site — left alone since fixing
  it wouldn't affect anything visitors actually see.
- Rochester's 4 real pages (Camps hub + 3 season pages) were switched to
  these templates with zero data loss — verified row counts and a full note
  match against the previous static HTML before clearing it. **Austin, Albert
  Lea, and Winona were moved onto the same templates on 2026-08-15** (Phase 5
  of `location-scoping-plan.md`), each with a Camps Hub page and 3 Camps
  Season children; they render the empty state until camp-session posts are
  tagged to those locations.
- **Camps Season page titles are load-bearing.** The empty state is built as
  `"No " . get_the_title() . " are currently scheduled."`, so these pages are
  titled "Spring/Summer Camps" / "Fall Camps" / "Winter Camps" — not
  "Spring/Summer". Note the slug must still be `spring-summer` (WordPress
  would auto-generate `spring-summer-camps` from that title, which the Camps
  Hub's card links don't match). Same for Fall and Winter.

**Cards + detail modal + Level filter (rebuilt 2026-08-10, matching the
React prototype's UX — see `prototype/src/components/camps/CampsList.jsx`):**
Camps Season pages render a card grid, not a table. Each card's title opens
a Bootstrap-native modal (`data-bs-toggle="modal"`, one modal per card, zero
custom JavaScript — same mechanism as the registration dropdown) showing
that camp type's shared description plus the session's own dates/ages/venue/
cost/registration link. A **Level filter** (All / Recreational / Competitive)
sits above the grid as real query-string links (`?level=competitive`),
server-rendered like every other toggle on the site — deliberately **not**
the prototype's client-side instant-filter-chip behavior, to keep this the
only site with zero custom JS. `program_level` was already tagged on every
camp-session post, so this needed no new schema, just a template that reads
it.

**`camp_type` is a real taxonomy, not a fixed dropdown (converted
2026-08-10).** Was a `Field::make('select', 'camp_type', ...)` on the Camp
Session Details box with 13 hardcoded options; is now a taxonomy (like
`program_location`) with a **term-meta** field group
(`nsfc_register_camp_type_fields()` in `inc/carbon-fields.php`) — `intro`
(textarea) + `points` (complex: title + text) — edited on **Camp Sessions →
Camp Types → {type} → Edit**, not on individual camp-session posts. One
description per type, shown in the modal for every session of that type.
Adding a brand-new camp type is "add a term," same as adding a location.
All 13 types have a description; 11 were ported from the prototype's
`campDescriptions.js`, 2 (Evening Technical, Evening World Cup) were drafted
fresh since the prototype never wrote them — worth a human review pass on
those two specifically before treating them as final copy.

### Programs: single-sourcing across seasons, and sub-programs (2026-08-10)
Every Program post's `season` taxonomy is checkbox-based (multi-select), not
a single value — a program that runs in more than one season should be
**one Program post with every applicable season checked**, not a separate
post per season. This already works via the existing `season`/`program_level`/
`program_location` tax_query on season-landing.php: check "Fall" and
"Spring/Summer" on one post and it shows up on both season pages
automatically. This is the answer to "the same program appears in multiple
seasons — how do we avoid duplicating it": check every season it applies to
on one entry. No separate mechanism needed.

**Sub-programs** — for a program that's really 2+ named offerings bundled
under one page (e.g. "Kickstarters" = "Lil Dribblers" + "Junior Kickers"),
each needing its own age range/cost/schedule/Register button, use the
`sub_programs` complex field on the Program Details Carbon Fields box
(`inc/carbon-fields.php`) instead of the normal flat fields. When a Program
post's `sub_programs` has entries, `single-program.php` skips Key Details /
Pricing / Schedule / Sessions / Registration entirely and renders stacked
sub-program sections instead (CLAUDE.md's Pattern C) — fill in one or the
other, not both. Each sub-program has: name, description, age range, a
flexible `details[]` list (label/value pairs — Staff ratio, Curriculum, Class
length, whatever applies), a `costs[]` table, and `sessions[]`. Each session
has its own name, optional venue (only needed when it differs from other
sessions — e.g. a winter indoor venue vs. a summer outdoor one), a nested
weekly `schedule[]` (day/dates/time — a session can meet more than once a
week), an optional note (for a not-yet-scheduled future session, e.g.
"Schedule posted September 1"), and its own optional registration
label/URL. **Registration renders as a dropdown built from whichever
sessions have a registration URL filled in** — same Bootstrap
`data-bs-toggle="dropdown"` markup as elsewhere, no custom JS. A session with
no registration URL yet is simply left out of the dropdown until one is
added — this is how "session posted, registration not open yet" is
represented; don't invent a separate status field for it.

This means one Program post is single-sourced across every season it runs
in, even when its actual session dates/venues differ season to season — the
`sessions[]` list holds all of them together rather than being duplicated
per season page. Live example: `program/kickstarters-classes/` (post 210)
carries Winter, Spring/Summer, and Fall sessions for both Lil Dribblers and
Junior Kickers in one entry, replacing 3 separate hand-typed pages that used
to exist per season.

**Note:** `Field::make('text', 'value', ...)` (or any field literally named
`value`) inside a `complex` field will fatal-error Carbon Fields — `value` is
a reserved keyword it uses internally (`Value_Set::VALUE_PROPERTY`). Use
something else (e.g. `detail_value`).

### URL structure
Full `:location` segment, covering all 4 North Star FC locations (as of 2026-08-10 —
this project moved from a Rochester-only POC toward a real multi-location site).
Structure:
- `/youth-soccer/` → 4-card location chooser (Rochester, Austin, Albert Lea, Winona)
- `/youth-soccer/{location}/` → location hub (Competitive / Recreational / Camps cards)
- `/youth-soccer/{location}/{competitive|recreational}/` → **level hub** (WP page using
  level-hub.php template) — see "Level Hub pages" below
- `/youth-soccer/rochester/competitive/spring-summer/` → season landing (WP page using season-landing.php template)
- `/youth-soccer/rochester/competitive/spring-summer/developmental-academy/` → program CPT single
- `/youth-soccer/rochester/recreational/fall/fall-rec-league/` → program CPT single
- `/youth-soccer/rochester/camps/` → camps hub

**All 4 locations use the same templates** (as of 2026-08-15, Phase 5 of
`location-scoping-plan.md`). Austin, Albert Lea, and Winona previously had
hand-typed pages linking out to northstarfc.com; those are gone. Each of the
4 locations now has Competitive/Recreational on `level-hub.php` and Camps on
`camps-hub.php`, each with 3 published season children — 48 URLs in total,
all returning 200.

**Only Rochester has real program data**, so the other 3 locations' season
pages render the templates' built-in empty states ("No programs listed for
this season yet." / "No {title} are currently scheduled."). That is the
finished state, not a stub — tag a `program` or `camp-session` post with
`austin` and it appears on Austin's pages automatically, no page edits
required.
- `/adult-soccer/`, `/upsl/`, `/tryouts/` → static WP pages (not location-specific)

Program CPT posts use the `program_location` taxonomy (terms: `rochester`, `austin`,
`albert-lea`, `winona`) so a program can eventually be scoped to the location it's
offered at. season-landing.php filters its program query by all three of
season + program_level + program_location (read from page meta `_nsfc_location`,
`_nsfc_season`, `_nsfc_level`) — this exists specifically so that adding real Austin/
Albert Lea/Winona programs later won't leak onto Rochester's pages or vice versa.

### Heading hierarchy (enforced)
Every template must flow h1 → h2 → h3 without skipping. The visual size is set by Bootstrap utility classes (`h6`, `h5`), not the semantic tag. Never use `.h4`, `.h5`, `.h6` on a tag that skips a level.

### Bootstrap class patterns
Use the same patterns as the React prototype. Key ones:
- Breadcrumb: call `nsfc_breadcrumb()` (`inc/breadcrumbs.php`) — don't hand-roll one.
  It renders `<nav aria-label="Breadcrumb" class="mb-4"><p class="breadcrumb">…</p></nav>`
  from the page's own ancestor chain. `single-program.php` is the one exception:
  a program's place in the hierarchy comes from its taxonomy terms, not a page parent,
  so it builds the same markup itself.
- Page title: `<h1 class="display-6 fw-bold mb-1">`
- Section subtitle: `<p class="text-muted mb-4">`
- Key detail pair: `<h2 class="h6 fw-semibold mb-1">` + `<p class="mb-0">`
- Major section heading: `<h2 class="h6 fw-semibold mb-3">`
- Card: `<div class="card border h-100"><div class="card-body d-flex flex-column">...`
- Only `sm` (576px) and `md` (768px) breakpoints inside components — never `xl` or `xxl`
  (verified 2026-08-15: zero `xl`/`xxl` classes anywhere in the theme).
  **Documented exception — the page-shell column.** Every full-page template wraps its
  content in `col-lg-8` (or `col-lg-9` on level-hub, camps-season, archive-camp-session):
  location-chooser, location-hub, level-hub, season-landing, camps-hub, camps-season,
  single-program. That is the standing pattern for page width, not a series of mistakes —
  match it in new templates, and don't "fix" the existing ones.

### Registration is always last
On every program detail page, registration buttons are the final content section — after dates, schedule, costs, coaching, and financial aid.

**The registration block renders every field that's filled in** (rewritten
2026-08-16). It used to be an `if/elseif` chain — Boys/Girls, else
Team/Individual, else External CTA — with the Registration Note *and* the whole
Coaches column living inside the Boys/Girls branch only. A Team-only program
(post 211) therefore showed neither, with nothing in the admin hinting why.
`single-program.php` now builds `$reg_buttons` from all five URL fields and
renders the note and Coaches block independently of which ones are set. Filling
Boys, Girls and Team now yields three buttons rather than silently hiding one —
that is intended: what's entered is what shows.

The old **"Extra Sections"** field group is gone (2026-08-16). It held three
unrelated things rendering in three different page sections: `notes` (the
callout box, 13/16 programs), `show_financial_aid` (the toggle, 4/16), and
`external_link_label`/`external_link_url` (0/16). They're now under `Notes`,
`Financial assistance`, and — since it renders as a Register button — inside
`Registration`, relabelled "Register — Other". Labels and grouping only; every
meta key is unchanged, so nothing needed migrating.

### Financial aid
Global text stored in WP Options (set via `ddev wp option update`). Key: `nsfc_financial_aid_steps` (JSON array of 3 steps) and `nsfc_financial_aid_note` (string).

Standard wording:
> "North Star FC believes every child should have the opportunity to play. Financial aid is available through PlayMetrics during registration."

Steps:
1. Log in to your PlayMetrics account
2. Click the Financial Aid tab on your registration
3. Submit your application

## Data sources (prototype → WordPress)

| WordPress content | Read from (relative to the prototype repo, see above) |
|---|---|
| Competitive programs | `prototype/src/data/competitiveSeasons.js` |
| Recreational programs | `prototype/src/data/recreationalPrograms.js` |
| Camp sessions | `prototype/src/data/campInstances.rochester.js` |
| Camp descriptions | `prototype/src/data/campDescriptions.js` |
| Camp templates (meta) | `prototype/src/data/campTemplates.js` |
| Program catalog (for finder page) | `prototype/src/data/programCatalog.js` |
| Recommendation rules (for finder page) | `prototype/src/data/recommendationRules.js` |

## Plugin inventory

| Plugin | Purpose | Activated |
|---|---|---|
| Kadence Blocks | Page layout blocks | Yes |
| Custom Post Type UI | CPT registration (UI backup — CPTs are code-registered) | Yes |
| TablePress | Roster, schedule, pricing tables | Yes |
| WPForms Lite | Optional: wizard form | Yes |
| Carbon Fields | Structured meta fields (loaded via theme) | Via Composer |

## Commands reference

```bash
# Start the site
ddev start

# Open the site in a browser
ddev launch

# WP-CLI
ddev wp post list --post_type=program
ddev wp post create --post_type=program --post_title="Developmental Academy" --post_status=publish
ddev wp term create season spring-summer
ddev wp post term set {post_id} season spring-summer

# Import/export
ddev wp export --dir=/var/www/html/exports

# Flush rewrite rules after adding CPTs
ddev wp rewrite flush

# PHP error log
ddev logs --follow
```

## Production readiness (deliberately deferred)

This is a POC whose purpose is validating the IA and navigation patterns, not
producing a deployable site. The shortcuts below are **intentional** — they
are listed so a future session can tell a deferred decision from a bug, and
so nothing here gets "fixed" prematurely while the POC is still the goal.
None of them affect whether the IA validates.

### Domain migration — read this before changing the site URL
Changing the domain is **not** just `siteurl` + `home` — content holds absolute
URLs too. Always snapshot first:

```bash
ddev snapshot --name pre-domain-fix          # always snapshot first
ddev wp search-replace 'old.host' 'new.host' --all-tables --skip-columns=guid
ddev wp cache flush
```

`--skip-columns=guid` is deliberate: GUIDs are permanent post identifiers,
never rendered as links, and rewriting them is the one thing the WP docs warn
against during a move. `wp_posts.guid` therefore still contains 181
`nsfc.ddev.site` strings. That is correct and should be left alone.

Do the same replace for `http://` → `https://` if the scheme changes.

**Historical note — why `--all-tables` is in that command.** This bit us on
2026-08-15 when the DDEV project was renamed `nsfc` → `northstar-testing`: the
site loaded fine, but every breadcrumb still pointed at `nsfc.ddev.site`,
because Yoast cached absolute permalinks in its own `wp_yoast_*` tables, which
the default search-replace scope misses. **Yoast was removed on 2026-08-15**
and breadcrumbs now come from `nsfc_breadcrumb()`, which builds URLs at render
time from `get_permalink()` and so cannot go stale. The six `wp_yoast_*` tables
were left in place (inert); `--all-tables` is kept because it is the safer
default for any plugin that behaves this way.

### Not production-ready, fix at build time
- **`setup.sh` hardcodes `--admin_password=admin`** (and CLAUDE.md documents
  admin/admin). Fine for a throwaway local DDEV site that is never deployed;
  must become an env var or a generated credential before any real hosting.
- **TLS is local-only.** Certificates come from mkcert, trusted per-machine.
  Production needs real certificates; nothing in the theme depends on this.
- **`wp-config.php` is gitignored** and holds DDEV's DB credentials. A prod
  deploy needs real secrets management and fresh salts — do not lift the
  local config.
- **Plugins are not tracked in git** (`/wp-content/plugins/` is ignored). The
  plugin set is reproducible only from the "Plugin inventory" table above.
  Consider Composer + wpackagist if this becomes a real build.
- **Content lives only in the local database.** Nothing but the child theme
  is in version control, so the ~50 pages / 16 programs / 28 camp sessions
  exist on this machine alone. Back up with `ddev snapshot` before risky
  work; migrating to prod means a real DB export, not a git checkout.
- **Only Rochester has real program data.** Austin, Albert Lea, and Winona
  use the same templates as Rochester (since 2026-08-15) and render the
  built-in empty states until `program` / `camp-session` posts are tagged to
  them. No stubs and no link-outs remain — see the URL structure section and
  `documentation/adding-a-location.md`.
- **No SEO output at all.** Yoast was removed on 2026-08-15 (it was only
  being used for breadcrumbs, and its editor UI was crowding out the content
  fields). There are now no meta descriptions, no Open Graph tags, no schema
  and no XML sitemap. Deliberate for an IA-validation POC; a production build
  needs an SEO plugin chosen and configured. Breadcrumbs are unaffected —
  they're native now and don't depend on that choice.
- **Bootstrap 5 loads from the jsDelivr CDN** (`functions.php`), not from
  Kadence and not bundled locally. The JS bundle is a hard dependency — the
  camp detail modals and every registration dropdown are Bootstrap-native and
  silently stop working without it. A real build should vendor it.

## What NOT to do

- Do not install new plugins without checking the build plan first
- Do not create custom CSS beyond what Kadence and Bootstrap 5 provide.
  **This rule is about the front end.** `assets/admin.css` is wp-admin-only
  (enqueued by `inc/admin-ui.php` on editor screens only) and is exempt —
  Kadence and Bootstrap don't style the Carbon Fields meta boxes at all.
- Do not use `xl` or `xxl` Bootstrap breakpoints, or introduce new `lg` ones — only `sm` and `md` (the existing `col-lg-*` page shells are the documented exception)
- Do not skip heading levels
- Do not place registration CTAs above schedule, pricing, or financial aid
- Do not modify the prototype at `/Users/jamielikely/Desktop/NorthStar/prototype/` — it is read-only for reference
