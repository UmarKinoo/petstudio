#!/usr/bin/env bash
# Incremental deploy via git-ftp (same pattern as bizkarts).
# Only changed files since the last remote .git-ftp.log are uploaded.
# DEPLOY_FORCE=1 — re-upload all plugin files (slow; includes demo media).
# Credentials: .vscode/sftp.json (gitignored).
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

SFTP_JSON="$REPO_ROOT/.vscode/sftp.json"
CTX="${DEPLOY_CONTEXT:-petstudio-prod}"
SYNCROOT="${SYNCROOT:-pet-studio-elementor-widgets}"

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

IFS=$'\t' read -r PROTO U P H PORT R PROFILE_SYNCROOT <<<"$row"

if [[ -z "$U" || -z "$P" || -z "$H" || -z "$R" ]]; then
	echo "Incomplete profile for context \"$CTX\"." >&2
	exit 1
fi

if [[ -n "$PROFILE_SYNCROOT" ]]; then
	SYNCROOT="$PROFILE_SYNCROOT"
fi

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
SYNC_DIR="$REPO_ROOT/$SYNCROOT"

curl_tls=()
extra=()
if [[ "${GIT_FTP_INSECURE:-1}" == "1" ]]; then
	extra+=(--insecure)
	curl_tls+=(--insecure)
fi

LOCAL_VERSION="$(rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" "$SYNC_DIR/pet-studio-elementor-widgets.php" | rg -o "'[^']+'" | tail -1 | tr -d "'")"

remote_version() {
	local raw
	raw="$(curl "${curl_tls[@]}" -sS --user "$U" --passwd "$P" \
		"${FTP_URL}/pet-studio-elementor-widgets.php" 2>/dev/null || true)"
	if [[ -z "$raw" ]]; then
		raw="$(curl "${curl_tls[@]}" -sS --user "$U:$P" \
			"${FTP_URL}/pet-studio-elementor-widgets.php" 2>/dev/null || true)"
	fi
	printf '%s' "$raw" \
		| rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" \
		| rg -o "'[^']+'" | tail -1 | tr -d "'" || true
}

find_version_commit() {
	local ver="$1"
	local commit
	while IFS= read -r commit; do
		[[ -z "$commit" ]] && continue
		if git show "$commit:$SYNCROOT/pet-studio-elementor-widgets.php" 2>/dev/null \
			| rg -q "define\\( 'PET_STUDIO_EW_VERSION', '${ver}'"; then
			printf '%s' "$commit"
			return 0
		fi
	done < <(git log --format=%H -40 HEAD -- "$SYNCROOT/pet-studio-elementor-widgets.php")
	return 1
}

remote_deploy_commit() {
	curl "${curl_tls[@]}" -sS --user "$U" --passwd "$P" \
		"${FTP_URL}/.git-ftp.log" 2>/dev/null | tr -d '[:space:]' || true
}

upload_file() {
	local rel="$1"
	local file="$SYNC_DIR/$rel"
	if [[ ! -f "$file" ]]; then
		echo "Skip missing: $rel" >&2
		return 0
	fi
	curl "${curl_tls[@]}" -sS --ftp-create-dirs --user "$U" --passwd "$P" \
		-T "$file" "${FTP_URL}/${rel}" >/dev/null
	echo "  ↑ ${rel}"
}

upload_changed_via_curl() {
	local base="$1"
	local head="$2"
	local -a paths=()
	local path rel

	if [[ "${DEPLOY_FORCE:-0}" == "1" ]]; then
		echo "Curl fallback: uploading all plugin files…" >&2
		while IFS= read -r -d '' file; do
			upload_file "${file#"$SYNC_DIR"/}"
		done < <(find "$SYNC_DIR" -type f ! -path '*/.git/*' -print0)
		return 0
	fi

	if [[ -n "$base" && "$base" =~ ^[0-9a-f]{40}$ ]]; then
		while IFS= read -r path; do
			[[ -n "$path" ]] && paths+=( "$path" )
		done < <(git diff --name-only --diff-filter=ACMR "$base" "$head" -- "$SYNCROOT")
	else
		echo "No remote deploy marker — uploading latest commit only." >&2
		while IFS= read -r path; do
			[[ -n "$path" ]] && paths+=( "$path" )
		done < <(git diff-tree --no-commit-id --name-only --diff-filter=ACMR -r "$head" -- "$SYNCROOT")
	fi

	if [[ ${#paths[@]} -eq 0 ]]; then
		echo "No changed plugin files to upload." >&2
		return 0
	fi

	echo "Curl fallback: uploading ${#paths[@]} changed file(s)…" >&2
	for path in "${paths[@]}"; do
		rel="${path#"$SYNCROOT"/}"
		upload_file "$rel"
	done
}

printf '' | curl "${curl_tls[@]}" --user "$U" --passwd "$P" --ftp-create-dirs -sS -T - \
	"${FTP_URL}/.git-ftp-remote-dir-ok" -o /dev/null 2>/dev/null || true

ftp_args=( push --auto-init "${extra[@]}" --user "$U" --passwd "$P" )
if [[ -n "$SYNCROOT" ]]; then
	ftp_args+=( --syncroot "$SYNCROOT" )
fi
if [[ "${DEPLOY_FORCE:-0}" == "1" ]]; then
	ftp_args+=( --force )
	echo "DEPLOY_FORCE=1: re-uploading all plugin files."
fi

echo "Deploying ${LOCAL_VERSION} to ${FTP_URL} (incremental via git-ftp)…"
git ftp "${ftp_args[@]}" "$FTP_URL"

REMOTE_VERSION="$(remote_version)"
if [[ -z "$REMOTE_VERSION" || "$REMOTE_VERSION" != "$LOCAL_VERSION" ]]; then
	echo "git-ftp finished but remote is ${REMOTE_VERSION:-missing} (expected ${LOCAL_VERSION}). Curl upload of changed files…" >&2
	BASE="$(remote_deploy_commit)"
	HEAD="$(git rev-parse HEAD)"
	# When git-ftp advanced the log but skipped files, diff from the version on the server.
	if [[ -n "$REMOTE_VERSION" ]]; then
		REMOTE_BASE="$(find_version_commit "$REMOTE_VERSION" || true)"
		if [[ -n "$REMOTE_BASE" && "$REMOTE_BASE" =~ ^[0-9a-f]{40}$ ]]; then
			BASE="$REMOTE_BASE"
		fi
	fi
	upload_changed_via_curl "$BASE" "$HEAD"
	git rev-parse HEAD | curl "${curl_tls[@]}" -sS --user "$U" --passwd "$P" \
		-T - "${FTP_URL}/.git-ftp.log" >/dev/null
	REMOTE_VERSION="$(remote_version)"
fi

if [[ "$REMOTE_VERSION" != "$LOCAL_VERSION" ]]; then
	echo "Deploy FAILED: remote ${REMOTE_VERSION:-missing}, expected ${LOCAL_VERSION}." >&2
	echo "Try: DEPLOY_FORCE=1 bash scripts/deploy-production.sh" >&2
	exit 1
fi

echo "Deploy verified: ${LOCAL_VERSION}"
