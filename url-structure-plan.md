# URL structure — program permalinks and location scoping

Started 2026-08-16. **Not started — this is a decision document, not a build
plan yet.** Nothing here is scheduled; it exists so the next session doesn't
have to re-derive the problem.

Same resumption pattern as `location-scoping-plan.md`: read the whole file,
then start at the first unchecked item in "Phases".

---

## The question

Program pages sit at a flat URL:

```
/youth-soccer/rochester/recreational/spring-summer/     ← season listing
/program/summer-rec-league/                             ← click a card, land here
```

The visitor drops out of the location hierarchy the moment they open a program.
Should the location (and level, and season) be in the program URL?

## What's actually true today

- **The permalink has always been flat.** `[ 'slug' => 'program', 'with_front'
  => false ]` in `inc/cpt.php`, unchanged since the repo's first commit
  (`04aa46f`). This was never a regression.
- **CLAUDE.md documented a nested form that has never resolved.** It claimed
  `/youth-soccer/rochester/competitive/spring-summer/developmental-academy/`;
  that 404s. Corrected 2026-08-16.
- **The breadcrumb reconstructs the hierarchy** from taxonomy terms, and season
  landing cards pass `?from={page_id}` so it knows which season page you came
  through (`nsfc_program_referring_season_page()`, `inc/breadcrumbs.php`).
- **Query strings are not a duplicate-content problem.** WordPress core's
  `rel_canonical()` emits `<link rel="canonical">` pointing at the clean
  permalink, with no SEO plugin installed. Verified 2026-08-16.

## Why it's flat, and why that's currently correct

A program is deliberately **single-sourced** across every season it runs in and
every location that offers it (see CLAUDE.md, "Programs: single-sourcing across
seasons"). Live examples:

| Post | Title | Spans |
|---|---|---|
| 210 | Kickstarters Classes | 3 seasons (winter, spring-summer, fall) |
| 81 | Recreation Classes | 3 seasons |
| 78 | Summer Rec League | 2 locations (rochester, albert-lea) |
| 211 | Youth League · U15–U18 | 2 locations |

A nested permalink must therefore either pick one canonical combination — so
the other combinations get a URL naming a season or city the visitor isn't in —
or publish identical content at several URLs.

## Multi-location programs are intentional — this is the answer to decision 1

Initially recorded here as a tagging bug. **The user confirmed on 2026-08-16
that it is deliberate.** Some programs are **centralized in Rochester** and open
to players from every location — tryouts being the clearest case. One program,
one venue, listed under each location it serves:

```
#211  venue: North Star FC, 380 Woodlake Dr SE, Rochester   tagged rochester + albert-lea
#78   session venue: Watson Soccer Complex   (Rochester)    tagged rochester + albert-lea
```

Splitting these into per-location posts would duplicate identical listings
across locations — exactly what single-sourcing exists to prevent.

**This settles the URL question in favour of the flat permalink.** If one post
legitimately belongs to several locations, a location-scoped permalink has no
canonical form: `/youth-soccer/albert-lea/summer-rec-league/` and
`/youth-soccer/rochester/summer-rec-league/` would be the same page, and picking
one means the other location's visitors get a URL naming a city they didn't
choose. The hierarchy belongs in the breadcrumb — where it now is, and where
`?from=` makes it reflect the route actually taken.

The remaining nuance is presentation, not structure: a visitor browsing Albert
Lea can see a Rochester address. That's accurate — the venue is shown — but if
centralized programs become common, labelling them in the listing ("Held in
Rochester") would be clearer than changing any URL.

## Does this matter for SEO?

**Less than it feels like.** URL keywords are a weak ranking signal. What moves
local search for youth sports is the Google Business Profile, title tags and
H1s, LocalBusiness schema, NAP consistency, and genuinely distinct content per
location.

This site currently has **no SEO output at all** — no meta descriptions, no
Open Graph, no schema, no XML sitemap (Yoast removed 2026-08-15; see CLAUDE.md
"Production readiness"). Canonical tags are the one exception, and those come
from core.

So: location in the URL is worth doing for **human clarity and IA correctness**
— a shared link that names the city is genuinely more useful — but it is not
the SEO lever it appears to be, and it should not jump the queue ahead of
having any metadata at all.

## Decisions

1. ~~**Can one program post serve two locations?**~~ **Answered 2026-08-16:
   yes.** Centralized programs (tryouts and similar) run in Rochester and serve
   every location. This is why the permalink stays flat.
2. ~~**Are posts 78 and 211 genuinely offered in Albert Lea?**~~ **Answered:
   yes**, as centralized Rochester-based programs. Leave the tags alone.
3. **Still open — is `?from=` good enough?** It works and canonicalises, but is
   opaque in a shared link. Alternatives: a readable slug
   (`?from=rochester-spring-summer`), or dropping it and accepting an arbitrary
   season in the breadcrumb. Cosmetic, not structural.
4. **Still open — should centralized programs be labelled** in the listings, so
   an Albert Lea visitor sees "Held in Rochester" before clicking? This is the
   real residue of decision 1, and it's a content/UX question rather than a URL
   one.

## Phases

- [x] **0. Answer decisions 1 and 2** — done 2026-08-16. Multi-location tagging
      is intentional.
- [x] **1. Fix the Albert Lea tagging** — no change needed; the tags are
      correct.
- [ ] **2. Decide whether to label centralized programs** in listings
      (decision 4). Most likely next piece of work.
- [ ] **3. Revisit `?from=` readability** (decision 3), if it ever bothers
      anyone.

Nested permalinks are **not planned**. Decision 1 rules them out: a program
belonging to several locations has no canonical location-scoped URL.

## Do not

- Do not split multi-location programs into per-location posts. That duplicates
  identical listings and defeats single-sourcing.
- Do not add nested permalinks. See decision 1.
- Do not change permalinks without 301s from the flat form, if this is ever
  revisited.
- Do not treat this as an SEO task. It's an IA task with a small SEO side
  effect, and the site has no metadata at all yet — that's the bigger gap.

## Status

**Resolved for now.** Written 2026-08-16; decisions 1 and 2 answered the same
day, which settled the permalink question in favour of the existing flat URL.
What remains (decisions 3 and 4) is presentation, not structure. The session
that produced this also fixed the breadcrumb — the actual defect — and corrected
CLAUDE.md, which had documented a nested URL form that never resolved.
