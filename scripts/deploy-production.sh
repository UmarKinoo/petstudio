#!/usr/bin/env bash
# Deploy plugin to production via git-ftp (same pattern as bizkarts).
# Credentials: .vscode/sftp.json (gitignored).
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

SFTP_JSON="$REPO_ROOT/.vscode/sftp.json"
CTX="${DEPLOY_CONTEXT:-petstudio-prod}"

if [[ ! -f "$SFTP_JSON" ]]; then
	echo "Missing SFTP config: $SFTP_JSON" >&2
	echo "Copy .vscode/sftp.json.example to .vscode/sftp.json and set credentials." >&2
	exit 1
fi

row="$(jq -r --arg c "$CTX" '
	.[] | select(.context == $c)
	| [.protocol // "ftp", .username, .password, .host, (.port // 21 | tostring), .remotePath, (.syncroot // "")] | @tsv
' "$SFTP_JSON" | head -n 1)"

if [[ -z "$row" ]]; then
	echo "No profile with context \"$CTX\" in $SFTP_JSON" >&2
	exit 1
fi

IFS=$'\t' read -r PROTO U P H PORT R SYNCROOT <<<"$row"

if [[ -z "$U" || -z "$P" || -z "$H" || -z "$R" ]]; then
	echo "Incomplete profile for context \"$CTX\"." >&2
	exit 1
fi

# SFTP needs Homebrew curl (Apple curl often lacks SFTP). Plain FTP uses system curl.
if [[ "$PROTO" == "sftp" ]]; then
	for _curl in /opt/homebrew/opt/curl/bin/curl /usr/local/opt/curl/bin/curl; do
		if [[ -x "$_curl" ]]; then
			export PATH="$(dirname "$_curl"):$PATH"
			break
		fi
	done
	if ! curl --version 2>/dev/null | grep "^Protocols: " | grep -qw sftp; then
		echo "SFTP deploy needs curl with SFTP support. Install: brew install curl" >&2
		exit 1
	fi
fi

R_NOSLASH="${R#/}"
FTP_URL="${PROTO}://${H}:${PORT}//${R_NOSLASH}"

extra=()
curl_tls=()
if [[ "${GIT_FTP_INSECURE:-1}" == "1" ]]; then
	extra+=(--insecure)
	curl_tls+=(--insecure)
fi

printf '' | curl "${curl_tls[@]}" --user "$U" --passwd "$P" --ftp-create-dirs -sS -T - \
	"${PROTO}://${H}:${PORT}//${R_NOSLASH}/.git-ftp-remote-dir-ok" -o /dev/null 2>/dev/null || true

ftp_args=( push --auto-init "${extra[@]}" --user "$U" --passwd "$P" )
if [[ -n "$SYNCROOT" ]]; then
	ftp_args+=( --syncroot "$SYNCROOT" )
fi
if [[ "${DEPLOY_FORCE:-0}" == "1" ]]; then
	ftp_args+=( --force )
	echo "DEPLOY_FORCE=1: re-uploading all plugin files."
fi

echo "Deploying to ${FTP_URL} …"
git ftp "${ftp_args[@]}" "$FTP_URL"

remote_version() {
	curl "${curl_tls[@]}" -sS --user "$U:$P" "${PROTO}://${H}:${PORT}//${R_NOSLASH}/pet-studio-elementor-widgets.php" \
		| rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" | rg -o "'[^']+'" | tail -1 | tr -d "'" || true
}

LOCAL_VERSION="$(rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" "$REPO_ROOT/${SYNCROOT:-pet-studio-elementor-widgets}/pet-studio-elementor-widgets.php" | rg -o "'[^']+'" | tail -1 | tr -d "'")"
REMOTE_VERSION="$(remote_version)"

if [[ -z "$REMOTE_VERSION" || "$REMOTE_VERSION" != "$LOCAL_VERSION" ]]; then
	echo "git-ftp reported success but remote is ${REMOTE_VERSION:-missing} (expected ${LOCAL_VERSION}). Falling back to curl FTP upload…" >&2
	SYNC_DIR="$REPO_ROOT/${SYNCROOT:-pet-studio-elementor-widgets}"
	while IFS= read -r -d '' file; do
		rel="${file#"$SYNC_DIR"/}"
		curl "${curl_tls[@]}" -sS --ftp-create-dirs --user "$U:$P" -T "$file" "${PROTO}://${H}:${PORT}//${R_NOSLASH}/${rel}" >/dev/null
	done < <(find "$SYNC_DIR" -type f ! -path '*/.git/*' -print0)
	git rev-parse HEAD | curl "${curl_tls[@]}" -sS --user "$U:$P" -T - "${PROTO}://${H}:${PORT}//${R_NOSLASH}/.git-ftp.log" >/dev/null
	REMOTE_VERSION="$(remote_version)"
	if [[ "$REMOTE_VERSION" != "$LOCAL_VERSION" ]]; then
		echo "Deploy verification failed: remote ${REMOTE_VERSION:-missing}, expected ${LOCAL_VERSION}." >&2
		exit 1
	fi
	echo "Fallback upload verified: ${LOCAL_VERSION}"
else
	echo "Deploy verified: ${LOCAL_VERSION}"
fi
