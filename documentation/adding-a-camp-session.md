# How to add a camp session

This covers adding a new camp session — a specific week/date range of an
existing camp offering (e.g. a new "Technical Camp — Sept 5–8"). Every step
happens inside the normal WordPress dashboard — no developer or code needed.

If the *kind* of camp you're adding has never existed before (not just a new
date for an existing one, but a genuinely new offering with its own
description), see
[adding-a-camp-type.md](adding-a-camp-type.md) first — you'll need that
before this session has anything to point to.

---

## First, a few concepts

Every camp session gets tagged with four things. These are all "type and
pick" search boxes, not checkboxes — start typing and choose from the list
that appears:

- **Season** — Spring/Summer, Fall, or Winter. This is a fixed list that
  essentially never changes.
- **Level** — Recreational or Competitive. Also fixed. This is what powers
  the "All / Recreational / Competitive" filter buttons on the camps pages.
- **Location** — which North Star FC location this session is at. See
  [adding-a-location.md](adding-a-location.md) if you need to add a new
  location — that's a separate, occasional task, not part of adding a camp.
- **Camp Type** — what *kind* of camp this is (Technical, Goalkeeper, World
  Cup, etc.). This is the one that drives the description shown in each
  camp's "more info" popup — see
  [adding-a-camp-type.md](adding-a-camp-type.md) for how that works and how
  to add a new one.

## 1. Create the camp session

1. Go to **Camp Sessions → Add New**.
2. Title: follow the existing naming convention — **{Camp name} — {dates}**,
   e.g. "Technical Camp — Sept 5–8."

## 2. Fill in the details

Scroll down to the **Camp Session Details** box:

- **Date Label** — the short, human-readable date range shown on the card,
  e.g. "Sept 5–8"
- **Start Date** / **End Date** — the actual calendar dates (used to sort
  camps chronologically on the page — always fill these in even though the
  Date Label above is what visitors actually see)
- **Venue** — where it's held
- **Ages** — e.g. "Ages 5–9"
- **Time** — e.g. "9:00–10:30am"
- **Cost** — e.g. "$95"
- **Registration URL** — the signup link

## 3. Tag it

In the right sidebar, use the four search boxes described above: type and
select the **Season**, **Level**, **Location**, and **Camp Type** that apply.
All four matter — a camp missing one of these won't show up correctly (or
at all) on the camps pages.

## 4. Publish

Click **Publish**.

## 5. Double-check

1. Visit the matching season's camps page (e.g.
   `/youth-soccer/rochester/camps/fall/`) and confirm your new camp shows up
   as a card with the right date and cost.
2. Click the card to open its "more info" popup — confirm the description
   looks right (that's coming from the Camp Type you picked, not anything
   you typed here) and the Register button works.
3. Try the Level filter buttons (Recreational/Competitive) at the top of the
   page and confirm your camp shows or hides correctly depending on which
   Level you tagged it with.

---

## Good to know

- **The description in the "more info" popup doesn't live on this page.**
  It's shared across every session of the same Camp Type, edited in one
  place — see [adding-a-camp-type.md](adding-a-camp-type.md). If you don't
  see a description, or need to change the wording, that's where to go, not
  here.
- **Season and Level are fixed lists you'll basically never need to add to.**
  If you ever do think a new season or level is needed, that's a bigger
  change than a normal camp addition — ask for help rather than guessing at
  it.
