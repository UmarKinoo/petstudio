#!/usr/bin/env bash
# Create an Elementor test page with Pet Studio widgets (requires Local site running).
set -euo pipefail

WP_ROOT="/Users/kinooumarkhayyamhassam/Local Sites/the-pet-studio/app/public"
PAGE_SLUG="pet-studio-widget-test"

cd "$WP_ROOT"

if ! wp core is-installed 2>/dev/null; then
  echo "WordPress not reachable — start the-pet-studio in Local first."
  exit 1
fi

PAGE_ID=$(wp post list --post_type=page --name="$PAGE_SLUG" --field=ID --format=csv 2>/dev/null || true)

if [ -z "$PAGE_ID" ]; then
  PAGE_ID=$(wp post create --post_type=page --post_title="Pet Studio Widget Test" --post_name="$PAGE_SLUG" --post_status=publish --porcelain)
  echo "Created page ID $PAGE_ID"
else
  echo "Using existing page ID $PAGE_ID"
fi

# Enable Elementor on the page.
wp post meta update "$PAGE_ID" _elementor_edit_mode builder
wp post meta update "$PAGE_ID" _elementor_template_type wp-page
wp post meta update "$PAGE_ID" _wp_page_template elementor_canvas

echo "Open in Elementor:"
echo "  $(wp option get siteurl)/wp-admin/post.php?post=${PAGE_ID}&action=elementor"
echo "Frontend:"
echo "  $(wp option get siteurl)/${PAGE_SLUG}/"
