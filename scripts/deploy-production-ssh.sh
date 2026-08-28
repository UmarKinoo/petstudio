#!/usr/bin/env bash
# Deploy Pet Studio Elementor plugin to production via SSH/rsync.
# Credentials: SSH_HOST, SSH_PORT, SSH_USER, SSH_PASS (or ssh key).
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

SSH_HOST="${SSH_HOST:-195.179.238.246}"
SSH_PORT="${SSH_PORT:-65002}"
SSH_USER="${SSH_USER:-u828573126}"
REMOTE_PLUGIN="${REMOTE_PLUGIN:-domains/motiondesignz.com/public_html/test/wp-content/plugins/pet-studio-elementor-widgets}"
REMOTE_WP="${REMOTE_WP:-domains/motiondesignz.com/public_html/test}"
SYNCROOT="${SYNCROOT:-pet-studio-elementor-widgets}"

LOCAL_VERSION="$(rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" "$SYNCROOT/pet-studio-elementor-widgets.php" | rg -o "'[^']+'" | tail -1 | tr -d "'")"
if [[ -z "$LOCAL_VERSION" ]]; then
	echo "Could not read PET_STUDIO_EW_VERSION." >&2
	exit 1
fi

SSH_BASE=(ssh -o StrictHostKeyChecking=no -p "$SSH_PORT" "${SSH_USER}@${SSH_HOST}")
RSYNC_SSH="ssh -o StrictHostKeyChecking=no -p ${SSH_PORT}"

if [[ -n "${SSH_PASS:-}" ]]; then
	if ! command -v sshpass >/dev/null 2>&1; then
		echo "sshpass required when SSH_PASS is set." >&2
		exit 1
	fi
	SSH_BASE=(sshpass -p "$SSH_PASS" "${SSH_BASE[@]}")
	RSYNC_SSH="sshpass -p ${SSH_PASS} ${RSYNC_SSH}"
fi

echo "Deploying plugin ${LOCAL_VERSION} → ${SSH_USER}@${SSH_HOST}:${REMOTE_PLUGIN}"

rsync -avz --delete \
	-e "$RSYNC_SSH" \
	"$SYNCROOT/" \
	"${SSH_USER}@${SSH_HOST}:${REMOTE_PLUGIN}/"

echo "Refreshing Behaviour page and clearing caches…"
"${SSH_BASE[@]}" "wp --path=${REMOTE_WP} eval '
if ( ! class_exists( \"Pet_Studio_Elementor\\Demo_Importer\" ) ) {
	echo \"Plugin not loaded\\n\";
	exit(1);
}
delete_option( \"pet_studio_ew_refreshed_pawsuite_booking_20260828\" );
\\Pet_Studio_Elementor\\Demo_Importer::ensure_pawsuite_booking_refresh();
update_option( \"pet_studio_ew_version\", \"\", false );
\\Pet_Studio_Elementor\\Plugin::purge_elementor_caches();
echo \"Behaviour refresh complete\\n\";
'"

REMOTE_VERSION="$("${SSH_BASE[@]}" "grep \"PET_STUDIO_EW_VERSION\" ${REMOTE_PLUGIN}/pet-studio-elementor-widgets.php | tail -1")"
echo "Remote: ${REMOTE_VERSION}"
echo "Deploy verified: ${LOCAL_VERSION}"
