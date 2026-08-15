#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# North Star FC — One-Time Setup From A Fresh Clone
#
# Builds a complete working copy of the POC: WordPress core, plugins, the
# Kadence parent theme, Carbon Fields, and the full site database (pages,
# programs, camp sessions, and all Carbon Fields / term meta).
#
# Prerequisites: Docker (or OrbStack) and DDEV installed and running.
#
# Usage:
#   cd northstar-testing
#   ./setup.sh
#
# Safe to re-run: it will overwrite the local database with the committed dump.
# ─────────────────────────────────────────────────────────────────────────────

set -euo pipefail

DB_DUMP="db/northstar-testing.sql.gz"

if [ ! -f "$DB_DUMP" ]; then
  echo "✗ $DB_DUMP not found. Run this from the repo root." >&2
  exit 1
fi

echo "▶ Starting DDEV..."
ddev start

echo "▶ Downloading WordPress core..."
ddev wp core download --skip-content --force

echo "▶ Creating wp-config.php..."
ddev wp config create --dbname=db --dbuser=db --dbpass=db --dbhost=db --force

echo "▶ Importing the site database..."
# Contains all content plus the admin user, active theme/plugin state, and the
# financial aid options — so no `wp core install` step is needed.
ddev import-db --file="$DB_DUMP"

echo "▶ Installing Carbon Fields via Composer..."
ddev composer install

echo "▶ Installing plugins..."
# The database records which plugins are active; these commands supply the
# actual plugin files.
ddev wp plugin install kadence-blocks custom-post-type-ui wordpress-seo tablepress wpforms-lite

echo "▶ Installing parent theme (Kadence)..."
ddev wp theme install kadence

echo "▶ Flushing rewrite rules..."
ddev wp rewrite flush

echo ""
echo "✓ Setup complete!"
echo ""
echo "  Site URL:   https://northstar-testing.ddev.site"
echo "  Admin URL:  https://northstar-testing.ddev.site/wp-admin"
echo "  Username:   admin"
echo "  Password:   admin"
echo ""
echo "If the browser warns about the certificate, run: mkcert -install"
echo "then quit and reopen the browser."
echo ""
echo "Next step: open a Claude Code session in this directory. Claude will read"
echo "build-plan.yaml and continue from the first unfinished task."
