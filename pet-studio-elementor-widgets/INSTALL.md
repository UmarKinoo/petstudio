# Pet Studio Elementor Widgets — Install guide

Version **0.5.70**

This plugin adds The Pet Studio widgets to Elementor and can import the demo pages (Home, Dog Grooming, Grooming Academy, Behaviour, Team, Contact) plus header and footer.

---

## What you need first

| Requirement | Notes |
|---|---|
| WordPress 6.0+ | PHP 7.4 or newer |
| **Elementor** | Free plugin — required. Install and activate it first. |
| **Elementor Pro** | Strongly recommended. Header and footer use Theme Builder. Without Pro, pages still work but site-wide header/footer will not assign automatically. |
| Theme | **Hello Elementor** is the recommended theme (Appearance → Themes). |

---

## 1. Upload the plugin

### Option A — WordPress admin (easiest)

1. In WP Admin go to **Plugins → Add New → Upload Plugin**.
2. Choose `pet-studio-elementor-widgets-0.5.70.zip`.
3. Click **Install Now**, then **Activate**.

If WordPress says the file is too large, use Option B.

### Option B — FTP / file manager

1. Unzip the file on your computer.
2. Upload the folder `pet-studio-elementor-widgets` into:

   `wp-content/plugins/pet-studio-elementor-widgets/`

3. In WP Admin go to **Plugins** and activate **Pet Studio Elementor Widgets**.

---

## 2. Import the demo site

1. Confirm **Elementor** is active (and **Elementor Pro** if you have it).
2. Go to **Tools → Pet Studio Demo**.
3. Click **Import demo content**.
4. Wait until you see a success message. This uploads images/video and builds the pages.

You can run the import again later. Existing Pet Studio pages will be refreshed, not duplicated.

---

## 3. Check the site

1. Go to **Settings → Reading** and confirm the homepage is set to **Home**.
2. Go to **Settings → Permalinks** and click **Save Changes** (leave the setting as-is).
3. Open the front of the site and check:

   - Home
   - Dog Grooming
   - Grooming Academy
   - Behaviour
   - Team
   - Contact

4. If you use a cache plugin (LiteSpeed, WP Super Cache, etc.), purge the cache.

### Header and footer (Elementor Pro)

After import, header and footer should already be assigned site-wide.

If they are missing:

1. Go to **Templates → Theme Builder**.
2. Open **Pet Studio Header** → set display condition to **Entire Site**.
3. Open **Pet Studio Footer** → set display condition to **Entire Site**.

---

## 4. Edit content

Every block is editable in Elementor. No code changes are required.

1. Open a page → **Edit with Elementor**.
2. Pet Studio widgets are in the left panel under the **Pet Studio** category.
3. Change text, images, links, colours, and spacing in the widget settings.

---

## Troubleshooting

| Problem | What to try |
|---|---|
| Plugin will not activate | Install and activate **Elementor** first. |
| Upload rejected / file too large | Use FTP (Option B) or raise the host PHP upload limit. |
| Widgets missing in Elementor | Deactivate and reactivate this plugin, then Elementor → Tools → Regenerate CSS & Data. |
| Pages look unstyled or old | Purge LiteSpeed / host cache, then hard-refresh the browser. |
| No header or footer | Install Elementor Pro and assign the Theme Builder templates (step 3). |
| Contact form not sending | In WP Admin check **Settings → General** email, and that the host allows `wp_mail`. |

---

## Support

If something fails during install, send:

- A screenshot of the error
- WordPress, Elementor, and PHP versions (**Tools → Site Health → Info**)
- Whether Elementor Pro is installed
