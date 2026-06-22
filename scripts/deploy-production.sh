#!/bin/bash
# Deploy plugin to production (test.motiondesignz.com) via FTP.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$ROOT/.env.deploy"

if [[ ! -f "$ENV_FILE" ]]; then
	echo "Missing $ENV_FILE — copy .env.deploy.example and add credentials." >&2
	exit 1
fi

# shellcheck disable=SC1090
source "$ENV_FILE"

export FTP_HOST FTP_PORT FTP_USER FTP_PASS FTP_REMOTE_PATH

echo "Deploying pet-studio-elementor-widgets to ${FTP_REMOTE_PATH} …"
python3 "$ROOT/scripts/ftp-sync-plugin.py"
