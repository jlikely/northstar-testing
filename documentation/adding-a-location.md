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

## 4. Add placeholder pages for Competitive, Recreational, and Camps

Until this location has real program details ready (schedules, costs,
rosters), create three simple placeholder pages as children of the page you
made in step 2:

1. Go to **Pages → Add New** (do this 3 times, once each for Competitive,
   Recreational, and Camps).
2. Title: "Competitive" (or "Recreational" / "Camps").
3. In **Page Attributes**: **Parent** → your new location's page. **Template**
   → "Plain Page."
4. Add the content — see below for what to put on it.
5. Publish.

**What to put on these pages depends on whether northstarfc.com (the old
website) is still online:**

- **If northstarfc.com is still live** — add a card or button linking out to
  the matching page on northstarfc.com. The easiest way is to open one of
  Austin's existing Competitive/Recreational/Camps pages, copy its layout,
  and swap in the new location's name and link.
- **If northstarfc.com has already been taken down** — do not link to it, the
  link will be broken. Instead just put the page title and the words
  "Details coming soon." Swap in the real program details whenever they're
  ready — there's no deadline tied to publishing the location itself.

Once real program details are ready for this location, these placeholder
pages get replaced with fuller pages (ask for help setting those up when
you're ready — they follow a different pattern).

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
- Click through to the Competitive/Recreational/Camps pages and make sure
  the links go where you expect.

---

## Good to know

- **Adding a location doesn't build out its programs for you.** Steps 1–3
  above create the location's main page. The Competitive, Recreational, and
  Camps pages (step 4) are separate and have to be created by hand — and once
  a location has real program details (not just placeholders), those pages
  get set up differently. Ask for help when you get to that point.
- **The Home page card (step 6) is the one exception** — everywhere else on
  the site updates automatically once you finish steps 1–3, but the Home
  page has to be edited by hand every time.
