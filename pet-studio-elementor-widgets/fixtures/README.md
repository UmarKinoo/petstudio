# Fixtures

Local mirror content used as widget defaults and API contract examples.

- `site.json` — global site metadata and navigation
- `widgets/*.json` — per-widget payload matching `schemas/*.json`

When you add a Pet Studio widget in Elementor, controls pre-fill from these files.
On the frontend, empty panel fields also fall back to fixture content.

**Not included (yet):** pre-built Elementor pages/templates — you still assemble pages
from widgets in Theme Builder / the page editor.

Replace `https://thepetstudio.local/wp-content/uploads/pet-studio/` URLs after media import
(upload `wordpress-export/media/` to the WP Media Library).
