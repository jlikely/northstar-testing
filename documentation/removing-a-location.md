# How to remove a location

Before doing anything, figure out which of these two situations you're in —
they need very different amounts of work.

- **Pausing a location** (closed for a season, on hold, taking a break) →
  go to [Option A](#option-a-pause-a-location-safe-and-reversible). Nothing
  gets deleted, and it's easy to undo.
- **Permanently removing a location** (it's closing for good, or you're
  merging it into another location) → go to
  [Option B](#option-b-permanently-remove-a-location). This deletes real
  content and is hard to undo — read it carefully before starting.

If you're not sure which one you need, use Option A. You can always come
back and do Option B later, but you can't easily undo Option B.

---

## Option A: Pause a location (safe and reversible)

This hides the location from the site without deleting anything. Everything
stays in place so you can turn it back on later.

1. **Pages → find the location's main page** → click **Quick Edit** →
   change Status from "Published" to "Draft" → **Update**. This takes the
   location's main page down.
2. **Appearance → Menus** → find the location under the "Youth Soccer"
   dropdown → remove it from the menu → **Save Menu**.
3. **Pages → Home → Edit** → remove or hide the location's card from the
   location grid → **Update**.
4. That's it. The location's pages, programs, and camps are all still there
   in the background — nothing was deleted. To bring it back, reverse these
   3 steps.

---

## Option B: Permanently remove a location

**This deletes content and cannot be easily undone. Before starting, make
sure you actually want to delete things, not just hide them (see Option A
above).**

### 1. Find everything tied to this location

1. Go to **Programs → All Programs** and use the **Location** dropdown at the
   top of the list to filter to this location. That shows every Program tied
   to it.
2. Do the same on **Camp Sessions → All Camp Sessions** — same Location
   dropdown, same idea.

Write down (or just keep these two tabs open) — you'll need to deal with
everything on both lists.

*(The **Programs → Locations** screen also shows a **Count** per location,
and clicking that number gets you the same list. Either route works.)*

### 2. Decide what happens to the programs and camps you found

For each Program and Camp Session on the lists from step 1, decide:

- **Is it offered at any other location too?** If yes, just remove this
  location from it (open it, uncheck the location, save) — don't delete it.
- **Is it only offered at this location?** If yes, and the location is
  really going away, delete it (open it → **Move to Trash**).

### 3. Delete the location's pages

**Every location has 13 pages.** They're arranged like this:

```
Austin                        ← the location's main page
├── Competitive
│   ├── Spring/Summer
│   ├── Fall
│   └── Winter
├── Recreational
│   ├── Spring/Summer
│   ├── Fall
│   └── Winter
└── Camps & Clinics
    ├── Spring/Summer Camps
    ├── Fall Camps
    └── Winter Camps
```

> **⚠️ Don't search by the location's name — you'll only find 1 of the 13.**
> Only the main page is called "Austin". The other 12 are called
> "Competitive", "Spring/Summer", "Fall Camps" and so on, exactly like every
> other location's pages. Searching finds the first one and hides the rest.

Instead, go to **Pages** and find the location's main page in the list. The
Pages screen shows pages in a tree — everything belonging to this location is
indented underneath it, with dashes in front. Delete (**Move to Trash**) the
whole block, **children first, working upward**, finishing with the main page
last.

> **Deleting a parent does not delete its children.** If you trash
> "Competitive" while its three season pages are still there, those pages stay
> published with a broken address. Always clear the deepest level first.

### 4. Remove the location from the Locations list

Only do this after steps 1–3 are done (nothing should still be using it).

1. Go to **Programs → Locations**.
2. Find the location, click **Delete**.
3. Repeat for **Camp Sessions → Locations**.

### 5. Remove it from navigation and the Home page

1. **Appearance → Menus** → remove the location from the "Youth Soccer"
   dropdown → **Save Menu**.
2. **Pages → Home → Edit** → remove its card from the location grid →
   **Update**.

### 6. Decide about old links

If this location's pages have been live for a while, people may have
bookmarked them, or they may show up in Google search results. Once you
delete the pages, those old links will show an error page.

- If that's not a concern (brand-new location, nobody's linked to it), skip
  this step.
- If it is a concern, ask for help setting up a redirect so old links send
  visitors somewhere useful (like the main "choose your location" page)
  instead of an error.

### 7. Refresh the site's web addresses

1. Go to **Settings → Permalinks**.
2. Click **Save Changes**.

### 8. Double-check everything

- Visit the site and confirm the location no longer appears anywhere
  (menu, Home page, "choose your location" page).
- Spot-check a couple of Programs/Camp Sessions you edited in step 2 to
  make sure they still look right for their remaining location(s).
- Try visiting one of the old page addresses directly — confirm it either
  redirects (if you set that up in step 6) or shows a normal "page not
  found," not a broken/half-working page.

---

## Good to know

- **Do Option A if there's any doubt.** It's free to undo. Option B is not.
- **The order in Option B matters.** Removing the location from the
  Locations list (step 4) before cleaning up what's using it (steps 2–3)
  will just leave things pointing at a location that no longer exists —
  always find and fix everything first, then remove the location itself
  last.
- **"Move to Trash" isn't gone forever right away.** Trashed pages/programs
  sit in the Trash for a while before WordPress permanently deletes them, so
  there's a short safety window if you realize you deleted the wrong thing.
