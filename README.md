# Pet Studio

WordPress + Elementor rebuild of [The Pet Studio](https://mature-brown-antelope.69-72-248-210.cpanel.site/).

## Plugin

`pet-studio-elementor-widgets/` — custom Elementor widgets, demo importer, mirror-faithful UIkit theme.

## Local development

1. Local site: `the-pet-studio.local` (Local by Flywheel)
2. After edits: `bash sync-plugin-to-local.sh`
3. Demo import: **Tools → Pet Studio Demo** or `php pet-studio-elementor-widgets/bin/run-demo-import.php`
4. HTTrack mirror preview: `cd pet-studio && php -S 127.0.0.1:8080`

## Push (GitHub + production)

When you say **push**, commit first, then run the deploy script — it **pushes to GitHub, then deploys to production**:

1. Commit any uncommitted changes
2. `bash scripts/deploy-production.sh` (runs `git push origin` then FTP upload)

One-liner:

```bash
git add -A && git commit -m "your message" && bash scripts/deploy-production.sh
```

Emergency FTP-only (skip GitHub): `DEPLOY_SKIP_GITHUB_PUSH=1 bash scripts/deploy-production.sh`

### First-time deploy setup

```bash
cp .vscode/sftp.json.example .vscode/sftp.json
# Edit .vscode/sftp.json (password stays in JSON — safe for special characters)
bash scripts/deploy-production.sh
```

Force full re-upload: `DEPLOY_FORCE=1 bash scripts/deploy-production.sh`

Credentials live in `.vscode/sftp.json` (gitignored), same pattern as [bizkarts](https://github.com/UmarKinoo/bizkarts).

Production site: [test.motiondesignz.com](https://test.motiondesignz.com/)
