#!/usr/bin/env python3
"""Upload pet-studio-elementor-widgets to production via FTP."""

from __future__ import annotations

import ftplib
import os
import sys
from pathlib import Path


def upload_tree(ftp: ftplib.FTP, local_root: Path, remote_root: str) -> int:
	count = 0
	local_root = local_root.resolve()

	for dirpath, _dirnames, filenames in os.walk(local_root):
		current = Path(dirpath)
		rel = current.relative_to(local_root)
		remote_dir = remote_root if str(rel) == "." else f"{remote_root}/{rel.as_posix()}"

		try:
			ftp.cwd(remote_dir)
		except ftplib.error_perm:
			parts = remote_dir.strip("/").split("/")
			ftp.cwd("/")
			for part in parts:
				if not part:
					continue
				try:
					ftp.cwd(part)
				except ftplib.error_perm:
					ftp.mkd(part)
					ftp.cwd(part)

		for name in filenames:
			local_file = current / name
			with local_file.open("rb") as handle:
				ftp.storbinary(f"STOR {name}", handle)
			count += 1
			print(f"  uploaded {rel.as_posix()}/{name}" if str(rel) != "." else f"  uploaded {name}")

	return count


def main() -> int:
	root = Path(__file__).resolve().parents[1]
	plugin_dir = root / "pet-studio-elementor-widgets"
	host = os.environ.get("FTP_HOST", "")
	user = os.environ.get("FTP_USER", "")
	password = os.environ.get("FTP_PASS", "")
	port = int(os.environ.get("FTP_PORT", "21"))
	remote_path = os.environ.get(
		"FTP_REMOTE_PATH",
		"/public_html/test/wp-content/plugins/pet-studio-elementor-widgets",
	).rstrip("/")

	missing = [k for k, v in {
		"FTP_HOST": host,
		"FTP_USER": user,
		"FTP_PASS": password,
	}.items() if not v]
	if missing:
		print(f"Missing env: {', '.join(missing)}", file=sys.stderr)
		print("Copy .env.deploy.example to .env.deploy", file=sys.stderr)
		return 1

	if not plugin_dir.is_dir():
		print(f"Plugin not found: {plugin_dir}", file=sys.stderr)
		return 1

	print(f"Connecting to {host}:{port} …")
	ftp = ftplib.FTP()
	ftp.connect(host, port, timeout=60)
	ftp.login(user, password)

	try:
		ftp.cwd("/")
		for part in remote_path.strip("/").split("/"):
			if not part:
				continue
			try:
				ftp.cwd(part)
			except ftplib.error_perm:
				ftp.mkd(part)
				ftp.cwd(part)

		print(f"Uploading {plugin_dir} → {remote_path}")
		total = upload_tree(ftp, plugin_dir, ftp.pwd())
		print(f"Done. {total} files uploaded.")
	except ftplib.all_errors as exc:
		print(f"FTP error: {exc}", file=sys.stderr)
		return 1
	finally:
		try:
			ftp.quit()
		except ftplib.all_errors:
			pass

	return 0


if __name__ == "__main__":
	sys.exit(main())
