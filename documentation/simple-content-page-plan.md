# Plan: Simple Content Page template

**Status: not built yet.** This is a plan for later, not a how-to — there's
nothing to click through today. Written up now so the design isn't lost
before it's picked up.

## What this is for

A handful of pages on the site are currently either bare "Details coming
soon" placeholders or hand-typed HTML that only a developer can safely edit:
**Tryouts, Club, Resources, Events, Field Maps,** and **Soccer Store** (the
one page here with real content already — address, hours, a shop-online
link). None of these are part of the Youth Soccer location/season/program
system — they're simple, mostly one-off pages.

This is intentionally lower priority than Youth Soccer's programs and
locations, where the real, high-volume content lives. Revisit this once
there's a clearer picture of what each of these pages actually needs to say.

## The design

The goal, in plain terms: **enough structure that an editor can't
accidentally break the page layout, but enough flexibility that it isn't a
rigid form that fights whatever a specific page needs to say.**

A page using this template would have:

- **Subtitle** (optional) — a short line under the title, e.g. "Rochester"
- **Intro paragraph** — a sentence or two under the subtitle
- **Key details** — a flexible list of label/value pairs. Add as many rows
  as apply, e.g. "Location" / "380 Woodlake Dr SE, Rochester," "Hours" /
  "Weekdays 12–8pm." Different pages can have completely different details —
  there's no fixed set of fields to fill in or leave blank.
- **Action button** (optional) — one label + link, for something like "Shop
  online" or "See tryout dates."
- **Body content** — the normal WordPress editor area stays available below
  the structured fields, for anything that doesn't fit as a short fact —
  extra paragraphs, a list, whatever a specific page needs that the fields
  above don't cover.

This mirrors the same structure/fields already used for Location Hub pages
(intro + short fields + the rest left flexible) — same idea, applied to a
simpler, more general page.

## Rough build steps (for whenever this gets picked up)

1. New Carbon Fields box ("Simple Content Page Details") with the fields
   above, scoped to a new page template.
2. New page template rendering: breadcrumb → title/subtitle → intro → key
   details → body content → action button → contact footer. (Action button
   last, matching the site's "registration/CTA is always the last content
   section" convention used everywhere else.)
3. Apply the template to Tryouts, Club, Resources, Events, Field Maps, and
   Soccer Store, migrating Soccer Store's existing real content in as the
   first working example.
4. Write the matching plain-language how-to doc once it's built, same style
   as the other `adding-a-*.md` guides in this folder.
