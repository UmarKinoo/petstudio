#!/bin/bash
# Sync plugin from httrack workspace to Local WP (run after edits).
set -euo pipefail
SRC="/Users/kinooumarkhayyamhassam/httrack/pet-studio-elementor-widgets/"
DEST="/Users/kinooumarkhayyamhassam/Local Sites/the-pet-studio/app/public/wp-content/plugins/pet-studio-elementor-widgets/"
rsync -a --delete "$SRC" "$DEST"
echo "Synced to Local: $DEST"
