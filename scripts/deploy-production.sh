#!/usr/bin/env bash
# Deploy plugin to production via curl FTP (git-ftp is unreliable on this host).
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

R_NOSLASH="${R#/}"
SYNC_DIR="$REPO_ROOT/${SYNCROOT:-pet-studio-elementor-widgets}"
FTP_BASE="${PROTO}://${H}:${PORT}//${R_NOSLASH}"

curl_tls=()
if [[ "${GIT_FTP_INSECURE:-1}" == "1" ]]; then
	curl_tls+=(--insecure)
fi

LOCAL_VERSION="$(rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" "$SYNC_DIR/pet-studio-elementor-widgets.php" | rg -o "'[^']+'" | tail -1 | tr -d "'")"

remote_version() {
	curl "${curl_tls[@]}" -sS --user "$U:$P" \
		"${FTP_BASE}/pet-studio-elementor-widgets.php" 2>/dev/null \
		| rg -o "define\\( 'PET_STUDIO_EW_VERSION', '[^']+'" \
		| rg -o "'[^']+'" | tail -1 | tr -d "'" || true
}

echo "Uploading ${LOCAL_VERSION} to ${FTP_BASE} …"

count=0
while IFS= read -r -d '' file; do
	rel="${file#"$SYNC_DIR"/}"
	curl "${curl_tls[@]}" -sS --ftp-create-dirs --user "$U:$P" \
		-T "$file" "${FTP_BASE}/${rel}" >/dev/null
	count=$(( count + 1 ))
done < <(find "$SYNC_DIR" -type f ! -path '*/.git/*' -print0)

git rev-parse HEAD | curl "${curl_tls[@]}" -sS --user "$U:$P" \
	-T - "${FTP_BASE}/.git-ftp.log" >/dev/null

echo "Uploaded ${count} files."

verified=""
for attempt in 1 2 3 4 5; do
	sleep 2
	verified="$(remote_version)"
	if [[ "$verified" == "$LOCAL_VERSION" ]]; then
		echo "Deploy verified: ${LOCAL_VERSION} (attempt ${attempt})"
		exit 0
	fi
	echo "Verify attempt ${attempt}: remote=${verified:-missing}, expected=${LOCAL_VERSION}" >&2
done

echo "Deploy FAILED: remote still ${verified:-missing}, expected ${LOCAL_VERSION}." >&2
exit 1
