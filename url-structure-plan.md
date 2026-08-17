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

## The blocker is content, not routing

**Posts 78 and 211 are tagged to Albert Lea but hold Rochester content.**

```
#211  venue: North Star FC, 380 Woodlake Dr SE, Rochester
#78   session venue: Watson Soccer Complex          (also Rochester)
```

Both render on Albert Lea's season pages today, and `?from=234` gives them an
Albert Lea breadcrumb over a Rochester address. The `program_location` filtering
works exactly as designed; the *tagging* asserts something untrue.

One post cannot serve two locations while venue, dates and cost are
location-specific. Until that's resolved, adding the location to the URL would
publish the wrong thing more confidently than the flat URL does.

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

## Decisions needed before any of this is buildable

1. **Can one program post serve two locations?** If yes, nested URLs can't be
   canonical and should stay flat. If no, programs become one post per location
   and nested URLs are trivially correct. *This is a question about how the club
   actually operates, not about the code.*
2. **Are posts 78 and 211 genuinely offered in Albert Lea?** If yes they need
   their own venue/dates/costs, so they're separate posts. If no, the tag comes
   off. Either way the current state is wrong.
3. **Does a program's URL name its season?** Probably not — a program spanning
   three seasons has no canonical one, and the season is already in the
   breadcrumb. `/youth-soccer/{location}/{program}/` may be the right depth.
4. **Is `?from=` acceptable in the interim?** It works and canonicalises, but
   it's opaque in a shared link. Alternatives: drop it and accept an arbitrary
   season in the breadcrumb, or use a readable slug (`?from=rochester-fall`).

## Phases (not started)

- [ ] **0. Answer decisions 1 and 2** — content questions, needs a human.
- [ ] **1. Fix the Albert Lea tagging** to match the answer. Either split into
      per-location posts or remove the tag. Verify Albert Lea's season pages
      afterwards.
- [ ] **2. Choose the target URL shape** (decision 3) and write it down here
      before building anything.
- [ ] **3. Add an SEO plugin and configure metadata.** Deliberately before
      permalink changes: permalinks are the hardest thing to change later, and
      a URL move without redirects and canonicals is worse than no move.
- [ ] **4. Implement nested permalinks** — rewrite rules plus a `post_type_link`
      filter, and 301s from `/program/{slug}/` so existing links survive.
- [ ] **5. Revisit `?from=`** (decision 4) — it may become unnecessary once a
      program belongs to exactly one location.

## Do not

- Do not add nested permalinks before phase 1. It bakes the current ambiguity
  into URLs.
- Do not change permalinks without 301s from the flat form.
- Do not treat this as an SEO task. It's an IA task with a small SEO side
  effect.

## Status

**Not started.** Written 2026-08-16 at the end of a session that fixed the
breadcrumb (the immediate defect) and corrected CLAUDE.md's URL documentation.
The permalink question itself was deliberately deferred — it is gated on
decisions 1 and 2, which are the user's to make.
