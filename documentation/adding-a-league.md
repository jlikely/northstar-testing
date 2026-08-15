# How to add a league

This guide covers league-style programs — indoor/winter leagues organized by
age tier that run as a series of sequential sessions within one season (e.g.
"Youth League · U15–U18," with a Session I, II, and III each running a few
months apart). Every step happens inside the normal WordPress dashboard — no
developer or code needed.

If what you're adding runs across **different seasons of the year** instead
(e.g. a program with a Spring/Summer version and a separate Fall version),
use [adding-a-competitive-program.md](adding-a-competitive-program.md)
instead — leagues and general competitive programs both use the same
Sessions mechanism, just for a different reason (session windows within one
season, vs. the same program across multiple seasons).

---

## 1. Create one program per age tier

Each age tier is its own program entry — e.g. "Youth League · U6–U12,"
"Youth League · U13–U14," "Youth League · U15–U18" are three separate
programs, not one. Go to **Programs → Add New** for each one.

Title format: "**{League name} · {Age range}**" — e.g.
"Youth League · U15–U18." Match this format for any new age tier so it's
consistent with the others.

## 2. Fill in the basics

Scroll down to the **Program Details** box. Fill in:

- **Age Range** — e.g. "U15–U18 (birth years 2008–2012)"
- **Format** — e.g. "7v7 indoor" or "Two 25-minute halves · 50-minute games"
- **Venue / Location** — where games are played

## 3. Write the description

In the **Notes** field (under "Extra Sections"), write everything a parent
needs up front: what the league is, officials/referee info, what day and
time games happen, and **divisions** — which age/skill group plays in which
division, and any guidance on which division a team should register for.
There's no separate structured field for divisions — write them out here,
e.g.:

> Divisions: Upper division — 2008–2010 (younger teams may enter, but should
> be aware they'll be playing older opponents) · Lower division — 2010–2012
> (do not register if more than half your team is juniors or seniors).

## 4. Add each session

Leagues like this typically run in several session windows across the
season (e.g. a fall window, a January window, a spring window), each with
its own dates and often its own price. Use the **Sessions** section: click
**Add** once per session and fill in:

- **Session Name** — e.g. "Session I"
- **Dates** — that session's date range
- **Cost** — that session's price, e.g. "$885/team"
- **Note (optional)** — anything specific to that session: how many weeks it
  runs, which dates have no games, when registration opens for it. This is
  where session-specific registration-opening dates belong (e.g.
  "Registration opens Nov 15") rather than the shared Registration section
  below.

Add as many sessions as the league actually runs.

## 5. Set up registration

Scroll to the **Registration** section. Leagues are usually **team**
registration — fill in **Register — Team (URL)**. If registration windows
differ per session, mention that in the **Registration Note** (e.g. "Session
I registration opens September 1. Session II opens November 15."), and put
each session's own opening date in that session's own Note too (step 4) so
it's not only in one place.

## 6. Tag the program

Right sidebar, three checkbox lists:

- **Seasons** — check whichever season this league runs in (usually just
  **Winter**, even though it has several sessions inside that one season —
  see "Good to know" below for why that's different from checking multiple
  seasons).
- **Levels** — check **Competitive**.
- **Locations** — check whichever location(s) it's offered at.

## 7. Publish

Click **Publish**.

## 8. Double-check

Visit the season page this league belongs to and confirm all age tiers show
up as separate cards, each linking to a page with the right sessions, cost,
and registration link.

---

## Good to know

- **One program per age tier — Sessions handle the schedule, not separate
  programs per session.** "Youth League · U15–U18, Session II" is not a
  second program — it's a row inside the same "Youth League · U15–U18"
  entry.
- **Sessions here vs. Sessions for a program that runs across seasons —
  same field, different reason.** A league's sessions are usually all inside
  one season (Winter) and represent sequential time chunks within that
  season. A competitive program's sessions (see the other guide) represent
  the same program running in genuinely different seasons of the year. Both
  use the same Sessions section — check the Seasons box(es) that actually
  apply either way.
