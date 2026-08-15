# How to add a new location

This guide walks through adding a new North Star FC location (like a new
city) to the website. Every step happens inside the normal WordPress
dashboard — you don't need a developer or any code for this.

**The order below matters.** Step 1 has to happen before step 3, because
step 3 involves picking your new location from a list, and that list won't
have your new location on it until you finish step 1.

---

## 1. Add the location to the Locations list

1. In the WordPress dashboard, go to **Programs → Locations**.
2. Click **Add New Location**.
3. Type the name (e.g. "Owatonna") and a short version with a dash instead of
   a space (e.g. `owatonna`) in the Slug field.
4. Save.

Every "pick a location" dropdown on the site reads from this list, so this
has to be done first.

## 2. Create the location's main page

1. Go to **Pages → Add New**.
2. Title: the location's name, e.g. "Owatonna." This is what will show as the
   location's name everywhere on the site, so make sure it's exactly what
   you want visitors to see.
3. On the right side of the screen, find the **Page Attributes** box:
   - **Parent** → choose "Youth Soccer"
   - **Template** → choose "Location Hub"
   - **Order** → a number that controls where this location shows up in the
     location list on the site (lower numbers show first). Rochester is `0`,
     Austin is `1`, Albert Lea is `2`, Winona is `3` — use `4` for a new one.
4. Click **Publish**.

The page's web address is created automatically based on the location name —
you don't need to set that yourself.

## 3. Fill in the location's details

After publishing, scroll down on that same page — you'll see a **Location
Details** box. Fill in:

- **Location** — a dropdown. Choose the location you added in step 1.
- **Intro paragraph** — a sentence or two shown at the top of this location's
  page, under the title.
- **Short description** — one short sentence shown on this location's card
  on the main "choose your location" page.
- **Program offerings** — checkboxes for Recreational Soccer, Competitive
  Soccer, Tryouts, and Camps & Clinics. All 4 are checked by default —
  **uncheck any this location doesn't actually have.** Only the checked
  ones will show as options on this location's page.

Click **Update** to save. The location picker page and this location's own
page both update right away — nothing else needs to be touched.

## 4. Add the Competitive, Recreational, and Camps pages

These use the site's own templates — you don't write any layout by hand, and
you don't need the programs to exist yet. A location with nothing scheduled
shows a tidy "nothing scheduled yet" message on its own, and starts showing
real programs automatically the moment any are added for this location.

There are three pages here, and **each one gets three season pages
underneath it**. Twelve pages in total. It goes quickest if you finish one
section completely (the main page, then its three seasons) before starting
the next.

### 4a. Competitive and Recreational

Do this twice — once for Competitive, once for Recreational:

1. **Pages → Add New**.
2. Title: "Competitive" (or "Recreational").
3. **Page Attributes**: **Parent** → your new location's page.
   **Template** → **"Level Hub."**
4. **Publish.** (The template's own settings box only appears after the first
   save — that's normal.)
5. Re-open the page. In the **"Level Hub Details"** box, set **Location** to
   your new location. Everything else in that box is optional — leave it all
   blank for now and the three season cards still appear and still work.
6. **Update.**

Then add its three season pages:

1. **Pages → Add New**.
2. Title: **Spring/Summer**, then **Fall**, then **Winter** (one page each).
3. **Page Attributes**: **Parent** → the Competitive (or Recreational) page
   you just made. **Template** → **"Season Landing."**
4. **Publish**, re-open, and in **"Season Landing Details"** set **Location**,
   **Level** (Competitive or Recreational — match the parent page), and
   **Season** (match the page title).
5. **Update.**

### 4b. Camps & Clinics

1. **Pages → Add New**, title "Camps & Clinics", **Parent** → your location's
   page, **Template** → **"Camps Hub."** Publish, re-open, set **Location** in
   the **"Camps Hub Details"** box, Update.
2. Add three season pages under it, exactly like above, but:
   - Titles: **Spring/Summer Camps**, **Fall Camps**, **Winter Camps** — the
     word "Camps" matters, see the warning below.
   - **Template** → **"Camps Season."**
   - In **"Camps Season Details"** set **Location** and **Season**.

> ### ⚠️ Two things to get right on the Camps season pages
>
> **1. Fix the web address.** WordPress will build the address from the title,
> giving you `spring-summer-camps` — but the Camps & Clinics page links to
> `spring-summer`, so the link would break. On each of the three pages, find
> the **URL / Permalink** field near the title and change it to just
> **`spring-summer`**, **`fall`**, or **`winter`**.
>
> *(The Competitive and Recreational season pages don't have this problem —
> "Spring/Summer" already becomes `spring-summer` on its own.)*
>
> **2. Keep "Camps" in the title.** When no camps are scheduled the page says
> *"No **Spring/Summer Camps** are currently scheduled"* — it builds that
> sentence from the page's title. Title the page just "Spring/Summer" and it
> reads "No Spring/Summer are currently scheduled."

When real programs or camps are added for this location later, they appear on
these pages automatically — there's nothing to come back and rebuild.

## 5. Add it to the navigation menu

1. Go to **Appearance → Menus**.
2. Find the "Youth Soccer" dropdown menu.
3. Add a new item that links to the new location's page (the one from step 2).
4. Save the menu.

## 6. Add it to the Home page

1. Go to **Pages → Home** and click **Edit**.
2. Find the row of location cards.
3. Duplicate one of the existing cards, and change its text and link to
   point to the new location.
4. Update the page.

(This is the one spot that has to be done by hand — the Home page is built
differently from the rest of the site, so it doesn't update automatically
like everything else.)

## 7. Refresh the site's web addresses

1. Go to **Settings → Permalinks**.
2. Click **Save Changes** (you don't need to change anything on this screen —
   just clicking Save refreshes things so the new pages work correctly).

## 8. Double-check everything

- Visit the new location's page and confirm the intro text and program
  cards look right.
- Visit the main "choose your location" page and confirm the new location
  shows up.
- **Click every season card.** From the location's page, go into Competitive,
  Recreational, and Camps, then click all three season cards on each — nine
  in total. Each should open a real page saying nothing is scheduled yet, not
  a "page not found."
- If a Camps season card gives you "page not found," its web address is
  almost certainly still `spring-summer-camps` instead of `spring-summer` —
  see the warning in step 4b.

---

## Good to know

- **Adding a location doesn't build out its programs for you.** Steps 1–3
  create the location's main page; step 4 adds its Competitive, Recreational,
  and Camps pages. Those pages start out empty and say so — which is fine to
  publish. Adding actual programs or camps later is a separate job (see
  `adding-a-competitive-program.md`, `adding-a-recreational-program.md`, and
  `adding-a-camp-session.md`), and once you tag one with this location it
  shows up on the right page by itself. **You never have to come back and
  rebuild these pages** — they were set up the same way every other location's
  are.
- **The Home page card (step 6) is the one exception** — everywhere else on
  the site updates automatically once you finish steps 1–3, but the Home
  page has to be edited by hand every time.
