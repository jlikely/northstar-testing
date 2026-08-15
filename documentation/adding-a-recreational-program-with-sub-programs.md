# How to add a recreational program with multiple offerings (sub-programs)

Use this guide when a single program is really **two or more named
offerings bundled together under one umbrella** — each with its own age
range, price, and schedule, but sharing one intro and one page. Examples
already on the site: "Kickstarters Classes" (Lil Dribblers + Junior Kickers)
and "Recreation Classes" (Ages 5–6 / Ages 7–8 / Ages 9–10).

If you're adding a normal single-offering program instead, use
[adding-a-recreational-program.md](adding-a-recreational-program.md) — it's
shorter and covers the common case.

This is a more involved setup than a standard program, but every step still
happens inside the normal WordPress dashboard — no developer or code needed.

---

## 1. Create the program

1. Go to **Programs → Add New**.
2. Title: the umbrella name shown to visitors, e.g. "Kickstarters Classes."
   The individual offerings (Lil Dribblers, Junior Kickers, etc.) get their
   own names in step 4 — this title is just the shared card/page name.

## 2. Write the shared intro (optional)

In the main content box (where you'd normally write a blog post), write a
short paragraph introducing the whole offering — shown at the top of the
page, above all the individual offerings. Leave it blank if you don't need
one.

## 3. Tag the program

Same as a standard program — right sidebar, three checkbox lists:

- **Seasons** — check **every season this offering runs in.** One entry
  covers all of them; don't create a separate copy per season.
- **Levels** — check **Recreational**.
- **Locations** — check whichever location(s) it's offered at.

## 4. Add each individual offering

Scroll down to the **Program Details** box, past the normal fields, to the
**Sub-programs** section. Click **Add** to create the first offering (e.g.
"Lil Dribblers"), and fill in:

- **Name** — e.g. "Lil Dribblers"
- **Description** — a sentence or two specific to this offering
- **Age range** — e.g. "Ages 3–4"
- **Extra details** (optional) — any other facts specific to this offering.
  Click **Add** under this section once per fact, e.g. Label: "Staff ratio,"
  Value: "8:1," or Label: "Class length," Value: "45 minutes."
- **Cost** — click **Add** once per price row, e.g. Label: "3-week class,"
  Cost: "$40."
- **Sessions** — see step 5 below.

Once this offering is fully filled in, click **Add** again under
**Sub-programs** (not under one of the fields inside it) to create the next
offering (e.g. "Junior Kickers"), and repeat. Add as many as you need.

## 5. Fill in each offering's sessions

Each offering can run across several named time periods throughout the year
(e.g. "Winter Session IV — February," "Summer Session — June"). Inside a
sub-program's **Sessions** area, click **Add** once per session and fill in:

- **Session name** — e.g. "Winter Session IV — February"
- **Venue** (optional) — only fill this in if this particular session meets
  somewhere different than the others (e.g. an indoor winter location vs. an
  outdoor summer one). Leave blank otherwise.
- **Weekly schedule** — click **Add** once per day it meets that week, e.g.
  Day: "Mondays," Dates: "2/2, 9, 16, 23," Time: "4:30pm." Add another row
  for Tuesdays if it also meets then, and so on.
- **Note** (optional) — use this instead of (or alongside) the schedule for
  something like "Schedule posted September 1" when a future session isn't
  fully set yet.
- **Registration dropdown label** — what shows in the Register dropdown for
  this specific session, e.g. "September (opens 8/1)." If left blank, the
  session name above is used instead.
- **Registration URL** — the signup link for this specific session.
  **Leave this blank if registration isn't open yet** — the session simply
  won't appear in the Register button's dropdown until you come back and add
  a link. This is exactly how "coming soon" sessions are meant to work — no
  separate status to set anywhere else.

## 6. How the Register button works

You won't see a single "Register" button per offering — it's a dropdown,
built automatically from whichever sessions you've given a Registration URL
in step 5. As you add links for new sessions (e.g. once fall registration
opens), they'll start appearing in the dropdown on their own — nothing else
needs to change.

## 7. Publish

Click **Publish**.

## 8. Double-check

- Visit the program's own page and confirm every offering shows up with the
  right age range, cost, and sessions.
- Open the Register dropdown for each offering and confirm it only lists
  sessions you've actually added a link for — nothing extra, nothing missing.
- Visit each season page this should appear on and confirm there's exactly
  **one** card for it (not one per season).

---

## Good to know

- **Cost and other details often repeat across offerings** — e.g. Lil
  Dribblers and Junior Kickers might have the identical price table. That's
  fine — re-enter it for each offering. This is a much smaller, contained
  kind of repetition than duplicating the whole program across multiple
  pages, which is the thing this whole setup exists to avoid.
- **One entry, every season, all sessions together.** Because this is one
  single entry covering every season it runs in, all of an offering's
  sessions (winter, spring/summer, fall) show together on the same page,
  regardless of which season page a visitor clicked in from. That's expected
  — it's the tradeoff for not duplicating the whole thing per season.
- **Not sure if something needs this guide or the simpler one?** Ask: "does
  this need more than one price, more than one age range, or its own
  Register button per offering?" If yes to any of those, it's a
  sub-programs case. If it's just one offering with one price and one
  schedule, use the standard guide instead.
