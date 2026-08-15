# North Star FC — WordPress POC

A proof of concept that rebuilds the North Star FC information architecture in
WordPress, using a zero-cost plugin stack. The goal is to validate the IA and
navigation patterns before committing to a paid production build.

This is **not a production site.** See
[Production readiness](CLAUDE.md#production-readiness-deliberately-deferred)
in `CLAUDE.md` for the list of shortcuts that are deliberate.

## Requirements

| Tool | Notes |
|---|---|
| [Docker](https://docs.docker.com/get-docker/) or [OrbStack](https://orbstack.dev) | Container runtime. OrbStack is lighter on macOS. |
| [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/) | v1.24+ — `brew install ddev/ddev/ddev` |
| [mkcert](https://github.com/FiloSottile/mkcert) | For trusted local HTTPS — `brew install mkcert` |

## Setup

```bash
git clone https://github.com/jlikely/northstar-testing.git
cd northstar-testing
mkcert -install        # one time per machine; prompts for your password
./setup.sh
```

`setup.sh` takes a few minutes. It downloads WordPress core, imports the site
database, installs the plugins and the Kadence parent theme, and pulls Carbon
Fields via Composer.

When it finishes:

- **Site:** https://northstar-testing.ddev.site
- **Admin:** https://northstar-testing.ddev.site/wp-admin — `admin` / `admin`

Day to day:

```bash
ddev start        # boot the site
ddev launch       # open it in a browser
ddev stop         # shut it down
ddev logs -f      # tail PHP errors
ddev wp <cmd>     # WP-CLI, e.g. ddev wp post list --post_type=program
```

There is no build step and no `package.json`. Edit a PHP file in
`wp-content/themes/nsfc-child/`, reload the browser. Bootstrap 5 comes from the
Kadence parent theme, and the site deliberately ships zero custom JavaScript —
dropdowns, modals, and filters are Bootstrap-native or server-rendered.

## What's in the repo

```
├── README.md                  ← You are here
├── CLAUDE.md                  ← Conventions, architecture, and working notes
├── build-plan.yaml            ← Phased build tracker
├── location-scoping-plan.md   ← Follow-on work (multi-location support)
├── documentation/             ← Content-entry guides for non-developers
├── db/                        ← Committed database dump (site content)
├── .ddev/                     ← Local environment config
└── wp-content/themes/nsfc-child/   ← The only application code
```

WordPress core, plugins, uploads, and `vendor/` are **not** tracked — `setup.sh`
fetches them. The child theme is the only code in version control.

## How content works

The site's content — roughly 50 pages, 16 programs, and 28 camp sessions —
lives in `db/northstar-testing.sql.gz`, not in the code. `setup.sh` imports it.

That dump is a snapshot, so it goes stale as content changes. After meaningful
content edits, refresh it:

```bash
ddev export-db --file=db/northstar-testing.sql.gz
```

Then commit the result. Before risky work, take a local snapshot you can roll
back to:

```bash
ddev snapshot --name before-my-change
ddev snapshot restore before-my-change
```

## Content entry

Programs, locations, camps, and camp types are all editable through the WordPress
admin without touching theme code. Step-by-step guides are in
[`documentation/`](documentation/) — start with
[`documentation/index.md`](documentation/index.md).

Real program data currently exists for **Rochester only**. Austin, Albert Lea,
and Winona are placeholder pages linking to northstarfc.com; the templates and
taxonomies are already location-aware and ready for real data.

## Troubleshooting

**Certificate warning in the browser** — the local CA isn't trusted yet. Run
`mkcert -install`, then fully quit and reopen the browser (trust is cached for
the life of the process). Firefox uses its own trust store and needs
`brew install nss` first.

**`ddev start` refuses, citing a different project root** — DDEV compares stored
paths literally, so a difference in capitalization is enough to trip it. Fix
with `ddev stop --unlist northstar-testing`, then `ddev start` from this
directory. Database volumes are keyed on the project name, so no data is lost.

**Blank page or a Carbon Fields fatal error** — `vendor/` is gitignored. Run
`ddev composer install`.

**Changing the site's domain** — this needs more than `siteurl` and `home`.
Yoast caches absolute permalinks in its own tables and supplies every breadcrumb.
See the domain-migration procedure in
[`CLAUDE.md`](CLAUDE.md#domain-migration--read-this-before-changing-the-site-url).
