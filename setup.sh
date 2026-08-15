#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# North Star FC — One-Time WordPress Setup
# Run this ONCE from the wordpress/ directory after completing Phase 0.
#
# Usage:
#   cd "path/to/wordpress"
#   chmod +x setup.sh
#   ./setup.sh
# ─────────────────────────────────────────────────────────────────────────────

set -e

echo "▶ Starting DDEV..."
ddev start

echo "▶ Downloading WordPress core..."
ddev wp core download

echo "▶ Creating wp-config.php..."
ddev wp config create --dbname=db --dbuser=db --dbpass=db --dbhost=db

echo "▶ Installing WordPress..."
ddev wp core install \
  --url=https://nsfc.ddev.site \
  --title="North Star FC" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@nsfc.ddev.site \
  --skip-email

echo "▶ Setting pretty permalinks..."
ddev wp rewrite structure '/%postname%/'
ddev wp rewrite flush

echo "▶ Removing default content..."
ddev wp post delete 1 2 --force 2>/dev/null || true
ddev wp plugin deactivate hello akismet 2>/dev/null || true

echo "▶ Installing plugins..."
ddev wp plugin install kadence-blocks --activate
ddev wp plugin install custom-post-type-ui --activate
ddev wp plugin install wordpress-seo --activate
ddev wp plugin install tablepress --activate
ddev wp plugin install wpforms-lite --activate

echo "▶ Installing parent theme (Kadence)..."
ddev wp theme install kadence --activate

echo "▶ Installing Carbon Fields via Composer..."
ddev composer init --name=northstarfc/nsfc --no-interaction 2>/dev/null || true
ddev composer require htmlburger/carbon-fields

echo "▶ Activating child theme..."
ddev wp theme activate nsfc-child

echo "▶ Setting financial aid options..."
ddev wp option add nsfc_financial_aid_steps \
  '["Log in to your PlayMetrics account","Click the Financial Aid tab on your registration","Submit your application"]'
ddev wp option add nsfc_financial_aid_note \
  "Questions? Contact us at info@northstarfc.com."

echo "▶ Flushing rewrite rules..."
ddev wp rewrite flush

echo ""
echo "✓ Setup complete!"
echo ""
echo "  Site URL:   https://nsfc.ddev.site"
echo "  Admin URL:  https://nsfc.ddev.site/wp-admin"
echo "  Username:   admin"
echo "  Password:   admin"
echo ""
echo "Next step: Open a new Claude Code session in the wordpress/ directory."
echo "Claude will read build-plan.yaml and continue with Phase 2."
