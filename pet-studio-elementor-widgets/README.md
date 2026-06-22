# Pet Studio Elementor Widgets

Custom Elementor widgets for [The Pet Studio](https://mature-brown-antelope.69-72-248-210.cpanel.site/) — mirror-faithful HTML/CSS, full builder controls, API-ready schemas.

## Install

### Local by Flywheel (this project)

**WP root:** `/Users/kinooumarkhayyamhassam/Local Sites/the-pet-studio/app/public`

The plugin is copied into Local (symlinks outside the site folder are not detected by WP-CLI/Local):

```
wp-content/plugins/pet-studio-elementor-widgets/
```

After editing in `httrack/pet-studio-elementor-widgets`, sync to Local:

```bash
/Users/kinooumarkhayyamhassam/httrack/sync-plugin-to-local.sh
```

**WP-CLI:** Run from Local’s site shell with the site **started** (Site → Open site shell):

```bash
cd ~/Local\ Sites/the-pet-studio/app/public
wp plugin list | grep pet-studio
wp plugin activate pet-studio-elementor-widgets
```

### General install

1. Copy or symlink this folder to `wp-content/plugins/pet-studio-elementor-widgets/`
2. Activate **Pet Studio Elementor Widgets**
3. Requires **Elementor** (Pro recommended for Theme Builder + forms)
4. Upload media from `../wordpress-export/media/` to the WP Media Library

### Prerequisites on Local site (not yet installed)

- [ ] **Elementor** (+ **Elementor Pro** if available)
- [ ] **Hello Elementor** theme (child theme optional)
- [ ] Activate this plugin after Elementor is active

## UIkit (bundled)

Mirror fidelity uses **UIkit 3** + YOOtheme Kojiro icons + theme CSS from the HTTrack copy:

- `assets/js/uikit.min.js` — parallax, sticky, slider, tabs, off-canvas
- `assets/js/uikit-icons-kojiro.min.js` — nav/social icons
- `assets/css/uikit.min.css` — core UIkit styles
- `assets/css/yootheme-theme.css` — mirror theme (Manrope/Noto, colours, components)
- `assets/fonts/` — webfonts referenced by theme CSS

`assets/js/elementor-init.js` runs `UIkit.update()` when Elementor renders widgets.

## Status: v0.2.0 scaffold

- ✅ 15 widgets registered under **Pet Studio** category
- ✅ JSON schemas + fixtures (API contract)
- ✅ Content normalizer + filter hooks
- ⏳ Mirror HTML/CSS render per widget (next pass)
- ⏳ Full Elementor control panels per audit spec

## API-ready

- **Schemas:** `schemas/*.json` — payload shape per widget
- **Fixtures:** `fixtures/widgets/*.json` — local mirror content as examples
- **Filter:** `pet_studio_widget_settings` — inject API data before render

See `../wordpress-export/docs/PLUGIN-WIDGET-AUDIT.md` for full spec.

## Development

Mirror preview (do not break): `http://127.0.0.1:8080/`

```bash
php -l pet-studio-elementor-widgets.php
php -l widgets/class-header-widget.php
```
