# The Pet Studio — Elementor Widget Audit

**Source:** HTTrack mirror (Joomla + YOOtheme Pro / UIkit)  
**Pages:** 6 (Home + 5 inner)  
**Recommended plugin slug:** `pet-studio-elementor-widgets`

## Build principle (mandatory)

- **Do not modify** the mirror (`pet-studio/`) in ways that break current browser rendering.
- Widgets must **copy-paste** mirror HTML/CSS, clean up only for WordPress (escaping, dynamic controls, asset paths), and **preserve the same DOM, classes, attributes, and JS behaviour** so output matches the live mirror preview.

---

## Site map

| Page | URL slug | Sections (excl. header/footer) |
|------|----------|----------------------------------|
| Home | `/` | 4 unique |
| Dog Grooming | `/dog-grooming/` | 7 content + divider + testimonials |
| Grooming Academy | `/grooming-academy/` | 4 content + tabs + testimonials |
| Dog Training | `/dog-training/` | 4 content + tabs + testimonials *(placeholder copy)* |
| Team | `/team/` | 1 banner + 8 profiles |
| Contact | `/contact/` | 1 complex section + testimonials |

**Global (every page):** Header, Footer, Cookie consent  
**Shared inner pattern:** Hero video (no text overlay)

---

## Page-by-page section inventory

### Home (`index.html`)

| # | Section | Layout | Key elements |
|---|---------|--------|--------------|
| H | Header | Sticky transparent overlay → solid on scroll | Logo (dark/light), 6 nav items + subtitles, mobile off-canvas, TikTok/IG/FB |
| 1 | **Hero Home** | Full-viewport video + 3 stacked layers | Video MP4, parallax logo SVG (desktop/mobile), 3 words (Experienced/Accredited/Friendly), opening hours |
| 2 | **Services Cards** | Sticky heading + 3-col parallax scroll cards | 3 portrait images, titles, “See More” links |
| 3 | **About Intro** | 2-col text + portrait | “A Class Team” heading, body, Read more, City & Guilds logo, Liza signature SVG overlay |
| 4 | **Testimonials** | Auto carousel, 3-up desktop | 14 cards: paw icon, title, quote, author |
| F | Footer | 3 rows | Contact CTA (phone/email), logo + tagline, address + hours, privacy + copyright |
| C | Cookie consent | Fixed banner + modal | Accept/Reject/Manage, OpenStreetMap preference |

### Dog Grooming (`dog-grooming.html`)

| # | Section | Layout | Key elements |
|---|---------|--------|--------------|
| H/F/C | Global | — | Same as home |
| 1 | **Hero Inner** | Full-viewport video | Video only, no text |
| 2 | **Page Intro** | 2-col | “Dog Grooming” accent heading, blockquote, body, signature, 1 portrait |
| — | **Dog Icon Divider** ×6 | Parallax horizontal icon | icon_dog_01–06 between sections |
| 3 | Our Salon | 2-col | Heading, 3 paragraphs, 1 landscape + 2 stacked portraits |
| 4 | Calm & Relaxed | 2-col + tile | Tile-wrapped text + 1 photo, 2 stacked photos |
| 5 | Puppy Grooming | 2-col | Heading, body, 1 portrait |
| 6 | Difficult Dog? | 2-col | Heading, blockquote, 4 paragraphs, 1 + 2 photos |
| 7 | Hand Stripping | 2-col + tile | Tile text + 2 photos |
| 8 | Teeth Cleaning | 2-col | Heading, blockquote, body, bullet list (3), 1 + 2 photos |
| 9 | Testimonials | Carousel | Full 14-review set |

### Grooming Academy (`grooming-academy.html`)

| # | Section | Layout | Key elements |
|---|---------|--------|--------------|
| 1 | Hero Inner | Video | — |
| 2 | Page Intro | 2-col | “Training Academy” heading, intro, City & Guilds badge, 2 photos |
| — | Dog Icon Divider ×3 | Parallax | icon_dog_07, 08, 03 |
| 3 | North Somerset Training Salon | 2-col | Heading + paragraph, 1 portrait |
| 4 | Career Change? | 2-col + tile | Blockquote in tile, 1 landscape |
| 5 | **Courses Tabs** | Dark bg, pill tabs | 4 tabs: Level 2 (24 day), Groom Your Own Dog (1 day), Quick Refresher (5 day), In-depth Refresher (10 day) — each with badge, meta, rich content |
| 6 | Testimonials | Carousel | 7-review subset |

### Dog Training (`dog-training.html`)

| # | Section | Notes |
|---|---------|-------|
| 1–5 | Same structure as Academy | Intro has **placeholder text**; Career Change image is placeholder; tabs **identical copy** to Academy |
| 6 | Testimonials | Full 14-review set |

### Team (`team.html`)

| # | Section | Layout | Key elements |
|---|---------|--------|--------------|
| 1 | Hero Inner | Video | — |
| 2 | **Est Banner** | Full-width parallax text | “Est. 2000” scrolling text (desktop) |
| 3–10 | **Team Member** ×8 | 2-col each | Name (accent), role meta, bio paragraphs, portrait 600×900; Liza has signature |
| F | Footer | Inline on page | Same content as global footer |

*No testimonials on Team page.*

### Contact (`contact.html`)

| # | Section | Layout | Key elements |
|---|---------|--------|--------------|
| 1 | Hero Inner | Video | — |
| 2 | **Contact Block** | 2-col sticky | Left: sticky full-height photo; Right: h1, phone, form (First/Last/Email/Phone/Radio/Enquiry/Submit), address, Google Maps link, OSM map embed |
| 3 | Testimonials | Carousel | Full set |

---

## Widget consolidation plan

Goal: **15 custom widgets** (not 40+) by using flexible widgets with repeaters and style presets.

### Tier A — Global (Theme Builder)

| # | Widget slug | Name | Used on |
|---|-------------|------|---------|
| 1 | `pet_studio_header` | Pet Studio Header | All pages |
| 2 | `pet_studio_footer` | Pet Studio Footer | All pages |
| 3 | `pet_studio_cookie_consent` | Cookie Consent | All pages *(optional — can use CMP plugin)* |

### Tier B — Heroes

| # | Widget slug | Name | Used on |
|---|-------------|------|---------|
| 4 | `pet_studio_hero_home` | Hero — Home | Home only |
| 5 | `pet_studio_hero_inner` | Hero — Inner Page | 5 inner pages |

### Tier C — Home-only sections

| # | Widget slug | Name | Used on |
|---|-------------|------|---------|
| 6 | `pet_studio_services_cards` | Services Scroll Cards | Home |
| 7 | `pet_studio_about_intro` | About / A Class Team | Home |

### Tier D — Reusable content blocks

| # | Widget slug | Name | Used on | Instances |
|---|-------------|------|---------|-----------|
| 8 | `pet_studio_page_intro` | Page Intro Split | Grooming, Academy, Training | 3× |
| 9 | `pet_studio_content_split` | Content Split Section | Grooming (×6), Academy (×2), Training (×2) | ~10× |
| 10 | `pet_studio_dog_divider` | Dog Icon Divider | Grooming, Academy, Training | ~10× |
| 11 | `pet_studio_courses_tabs` | Courses Tab Section | Academy, Training | 2× |
| 12 | `pet_studio_testimonials` | Testimonials Carousel | Home, Grooming, Academy, Training, Contact | 5× |
| 13 | `pet_studio_team_member` | Team Member Profile | Team | 8× |
| 14 | `pet_studio_est_banner` | Est. Banner | Team | 1× |
| 15 | `pet_studio_contact` | Contact Section | Contact | 1× |

**Total: 15 widgets**

---

## Elementor controls spec (full editability)

Every visible element maps to a panel control. Defaults = mirror content.

### Shared Style tab groups (all widgets)

Each widget Style tab includes (where applicable):

- Section background (`COLOR` / gradient)
- Section padding (`DIMENSIONS`, responsive)
- Primary accent colour override (`COLOR`, default `#FF90AA`)
- Heading typography (`Group_Control_Typography`, selector `.el-title`)
- Body typography (`Group_Control_Typography`, selector `.el-content`)
- Link/button colours (`COLOR` normal + hover)

---

### 1. `pet_studio_header`

**Content**
| Control | Type | Notes |
|---------|------|-------|
| Logo (default) | MEDIA | Dark/header logo |
| Logo (inverse) | MEDIA | Light overlay logo |
| Logo link | URL | Home URL |
| Logo width desktop / mobile | SLIDER responsive | |
| Navigation items | REPEATER | `label`, `subtitle`, `link`, `is_active` |
| Show social icons | SWITCHER | |
| Social items | REPEATER | `icon` (ICONS), `link` (URL) |
| Enable sticky | SWITCHER | |
| Enable transparent overlay | SWITCHER | |
| Mobile menu label | TEXT | Accessibility |

**Style**
| Control | Type |
|---------|------|
| Nav link typography | Typography |
| Nav subtitle typography | Typography |
| Nav / logo colours (default + sticky) | COLOR ×4 |
| Off-canvas background | COLOR |
| Social icon size / colour | SLIDER + COLOR |

---

### 2. `pet_studio_footer`

**Content**
| Control | Type |
|---------|------|
| Contact heading desktop | TEXT |
| Contact heading link | URL |
| Phone | TEXT |
| Email | TEXT |
| Logo | MEDIA |
| Tagline | TEXTAREA |
| Address | WYSIWYG |
| Hours heading | TEXT |
| Hours text | TEXTAREA |
| Privacy label + URL | TEXT + URL |
| Copyright | TEXT |

**Style**
| Control | Type |
|---------|------|
| Footer background | COLOR |
| CTA heading typography | Typography |
| Phone/email link colours | COLOR |
| Muted text colour | COLOR |

---

### 3. `pet_studio_cookie_consent`

**Content**
| Control | Type |
|---------|------|
| Banner text | WYSIWYG |
| Accept / Reject / Manage labels | TEXT ×3 |
| Modal title | TEXT |
| Functional category title + description | TEXT + TEXTAREA |
| Preferences category title + description | TEXT + TEXTAREA |
| OpenStreetMap label | TEXT |
| Privacy policy link | URL |
| Accept all / Reject all / Save labels | TEXT ×3 |

**Style**
| Control | Type |
|---------|------|
| Banner background / text | COLOR ×2 |
| Button primary / secondary | COLOR ×2 |

---

### 4. `pet_studio_hero_home`

**Content**
| Control | Type |
|---------|------|
| Background video desktop | MEDIA |
| Background video mobile | MEDIA |
| Logo desktop | MEDIA |
| Logo mobile | MEDIA |
| Headline words | REPEATER (`word` TEXT) |
| Opening hours title | TEXT |
| Opening hours text | TEXTAREA |
| Parallax word 1/2/3 strings | TEXT (if not using repeater for static trio) |

**Style**
| Control | Type |
|---------|------|
| Headline typography | Typography |
| Headline colour | COLOR |
| Logo max-width responsive | SLIDER |
| Video overlay opacity | SLIDER |

---

### 5. `pet_studio_hero_inner`

**Content**
| Control | Type |
|---------|------|
| Background video | MEDIA |
| Viewport height offset | NUMBER |
| Minimum height | SLIDER responsive |

**Style**
| Control | Type |
|---------|------|
| Overlay colour / opacity | COLOR + SLIDER |

---

### 6. `pet_studio_services_cards`

**Content**
| Control | Type |
|---------|------|
| Section heading | TEXT |
| Section heading accent | TEXT |
| Sticky heading height | SLIDER |
| Service cards | REPEATER: `image`, `title`, `link`, `button_text`, `parallax_start` |

**Style**
| Control | Type |
|---------|------|
| Section heading typography | Typography |
| Card title typography | Typography |
| Button text colour | COLOR |
| Card overlay gradient | COLOR |

---

### 7. `pet_studio_about_intro`

**Content**
| Control | Type |
|---------|------|
| Heading | TEXT |
| Body | WYSIWYG |
| CTA text + link | TEXT + URL |
| Badge image | MEDIA |
| Main image | MEDIA |
| Show signature | SWITCHER |
| Signature image | MEDIA |
| Signature position X/Y | SLIDER ×2 |

**Style**
| Control | Type |
|---------|------|
| Heading / body typography | Typography ×2 |
| Signature max-width | SLIDER |

---

### 8. `pet_studio_page_intro`

**Content**
| Control | Type |
|---------|------|
| Heading | TEXT |
| Accent part | TEXT |
| Blockquote | TEXTAREA |
| Body | WYSIWYG |
| Show signature | SWITCHER |
| Signature image | MEDIA |
| Badge image | MEDIA |
| Primary image | MEDIA |
| Secondary image | MEDIA |
| Reverse columns | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Heading typography | Typography |
| Accent colour | COLOR |
| Blockquote border/colour | COLOR |

---

### 9. `pet_studio_content_split`

**Content**
| Control | Type |
|---------|------|
| Layout preset | SELECT: standard / tile-left |
| Section tone | SELECT: default / muted / secondary |
| Heading | TEXT |
| Blockquote | TEXTAREA |
| Body | WYSIWYG |
| Bullet list | REPEATER (`item` TEXT) |
| Image layout | SELECT: 1-right / 2-stacked / 1+1 |
| Image 1 / 2 / 3 | MEDIA ×3 |
| Reverse columns | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Tile background | COLOR |
| Heading / body typography | Typography ×2 |
| Image border-radius | SLIDER |

---

### 10. `pet_studio_dog_divider`

**Content**
| Control | Type |
|---------|------|
| Icon image | MEDIA |
| Parallax X range | TEXT (uk-parallax value string) |
| Show on mobile | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Icon max-width | SLIDER responsive |

---

### 11. `pet_studio_courses_tabs`

**Content**
| Control | Type |
|---------|------|
| Tabs | REPEATER: `tab_label`, `badge_image`, `title`, `duration_meta`, `content` (WYSIWYG), `features` (nested repeater TEXT) |
| Default active tab | NUMBER |

**Style**
| Control | Type |
|---------|------|
| Section background | COLOR |
| Tab pill active/inactive | COLOR ×2 |
| Tab title typography | Typography |

---

### 12. `pet_studio_testimonials`

**Content**
| Control | Type |
|---------|------|
| Reviews | REPEATER: `icon` MEDIA, `title` TEXT, `quote` WYSIWYG, `author` TEXT |
| Autoplay | SWITCHER |
| Autoplay interval (ms) | NUMBER |
| Slides desktop / tablet / mobile | NUMBER ×3 |
| Show dots | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Section background | COLOR |
| Title / quote / author typography | Typography ×3 |
| Divider colour | COLOR |

---

### 13. `pet_studio_team_member`

**Content**
| Control | Type |
|---------|------|
| Name line 1 | TEXT |
| Name accent (pink line) | TEXT |
| Role | TEXT |
| Bio | WYSIWYG |
| Portrait | MEDIA |
| Show signature | SWITCHER |
| Signature image | MEDIA |
| Reverse columns | SWITCHER |
| Show mobile divider | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Name / role / bio typography | Typography ×3 |
| Accent colour | COLOR |

---

### 14. `pet_studio_est_banner`

**Content**
| Control | Type |
|---------|------|
| Banner text | TEXT |
| Parallax expression | TEXT |
| Hide on mobile | SWITCHER |

**Style**
| Control | Type |
|---------|------|
| Text colour | COLOR |
| Typography | Typography |

---

### 15. `pet_studio_contact`

**Content**
| Control | Type |
|---------|------|
| Page heading | TEXT |
| Phone | TEXT |
| Phone label sections | TEXT ×3 (Get in Touch, Enquiry Form, Visit Us) |
| Form shortcode / Elementor template | TEXT or SELECT |
| Address | WYSIWYG |
| Google Maps button text + URL | TEXT + URL |
| Sticky side image | MEDIA |
| Mobile inline image | MEDIA |
| Map lat / lng / zoom | TEXT ×3 |
| Map marker title | TEXT |
| Map height | SLIDER |

**Style**
| Control | Type |
|---------|------|
| Heading typography | Typography |
| Label colour (primary) | COLOR |
| Map border-radius | SLIDER |

---

## Widget control requirements (legacy summary)

See **Elementor controls spec** above for the authoritative full list.

### 1. Header
- Logo default + inverse (for overlay)
- Nav repeater: label, subtitle, URL, active state
- Social links repeater: network, URL
- Sticky / transparent toggle
- Mobile breakpoint behaviour

### 2. Footer
- Phone, email (tel/mailto)
- Logo image
- Tagline text
- Address (multiline)
- Opening hours
- Privacy policy URL
- Copyright text

### 3. Hero Home
- Desktop video, mobile video (optional)
- Desktop logo SVG, mobile logo SVG
- 3 headline words (repeater)
- Opening hours heading + text
- Parallax intensity toggles (or CSS-only v1)

### 4. Hero Inner
- Video source
- Viewport height offset (header overlap)

### 5. Services Cards
- Section heading (“Our Services” + accent span)
- 3 cards repeater: image, title, link, parallax delay offset

### 6. About Intro
- Heading, body (WYSIWYG), CTA text + URL
- Badge image (City & Guilds)
- Main portrait image
- Signature SVG toggle + image
- Signature position controls

### 7. Page Intro
- Heading + accent word (split styling)
- Blockquote (optional)
- Body WYSIWYG
- Signature toggle
- Badge image (optional)
- Primary image + secondary image (optional)

### 8. Content Split *(most flexible — critical)*
- Style preset: `standard` | `tile-left`
- Heading
- Blockquote (optional)
- Body WYSIWYG
- Bullet list repeater (optional)
- Image layout: `1-right` | `2-stacked-right` | `1-left-1-right-below`
- Up to 3 media controls
- Background section tone: default | muted

### 9. Dog Icon Divider
- Icon image select (8 presets from media)
- Parallax direction/speed

### 10. Courses Tabs
- Section background color (secondary/dark)
- Tabs repeater: tab label, badge image, title, duration meta, content (WYSIWYG), bullet list

### 11. Testimonials
- Autoplay, speed, slides per view
- Items repeater: icon, title, quote, author

### 12. Team Member
- First name, accent name/line, role
- Bio (WYSIWYG)
- Portrait image
- Show signature toggle
- Reverse column order (optional)

### 13. Est Banner
- Text, color, parallax speed
- Desktop-only toggle

### 14. Contact
- Sticky side image (desktop) + inline image (mobile)
- Phone, address
- Form: Elementor form selector OR shortcode field
- Map: embed URL or lat/lng + marker title
- Google Maps external link

### 15. Cookie Consent
- Banner text
- Accept / Reject / Manage labels
- OpenStreetMap category label
- Link to privacy policy

---

## Page → widget assembly matrix

| Page | Widget stack (top to bottom) |
|------|------------------------------|
| **Home** | Header → Hero Home → Services Cards → About Intro → Testimonials → Footer |
| **Dog Grooming** | Header → Hero Inner → Page Intro → Divider → Content Split ×6 (with dividers) → Testimonials → Footer |
| **Grooming Academy** | Header → Hero Inner → Page Intro → Divider → Content Split ×2 → Divider → Courses Tabs → Testimonials → Footer |
| **Dog Training** | Same as Academy (update placeholder content) |
| **Team** | Header → Hero Inner → Est Banner → Team Member ×8 → Footer |
| **Contact** | Header → Hero Inner → Contact → Testimonials → Footer |

---

## Assets & design tokens (from mirror)

| Token | Value |
|-------|-------|
| Primary | `#FF90AA` |
| Secondary | `#B4ADA7` |
| Success | `#80B485` |
| Font heading | Manrope |
| Font body | Noto Sans |

Media already organised in `wordpress-export/media/`.

---

## JS dependencies to port

| Feature | Original | Widget approach |
|---------|----------|-----------------|
| Sticky header | UIkit `uk-sticky` | CSS `position:sticky` + small JS |
| Parallax scroll | UIkit `uk-parallax` | Intersection Observer or GSAP ScrollTrigger (light) |
| Services card scroll | UIkit sticky + parallax | Dedicated JS in `services-cards.js` |
| Testimonials carousel | UIkit `uk-slider` | Swiper.js (Elementor-friendly) or native CSS scroll-snap v1 |
| Tabs | UIkit `uk-switcher` | Vanilla tabs JS |
| Off-canvas mobile nav | UIkit `uk-offcanvas` | CSS + toggle JS |
| Map | Leaflet + consent gate | Leaflet enqueue + consent check |
| Signature stroke animation | UIkit `uk-svg` + scrollspy | Inline SVG + CSS stroke animation |

---

## Build order (recommended)

1. Plugin scaffold + design tokens CSS
2. Header + Footer (unlock all pages)
3. Hero Inner + Hero Home
4. Page Intro + Content Split (unlock inner pages)
5. Testimonials
6. Services Cards + About Intro (finish Home)
7. Dog Divider + Courses Tabs
8. Team Member + Est Banner
9. Contact + Cookie Consent
10. Parallax/scroll polish pass

---

## API-ready content layer (schemas + fixtures)

Configure **now** — API endpoints **later**.

### Plugin folder layout

```
pet-studio-elementor-widgets/
├── pet-studio-elementor-widgets.php
├── includes/
│   ├── class-plugin.php
│   ├── class-widget-base.php
│   ├── class-content-normalizer.php   ← settings ↔ API shape
│   └── helpers.php
├── widgets/                           ← 15 Elementor widgets
├── schemas/                           ← JSON Schema per widget (API contract)
│   ├── _common.json                   ← shared types: media, link, nav_item…
│   └── {widget}.json
├── fixtures/
│   ├── site.json                      ← global site + nav
│   └── widgets/{widget}.json          ← mirror content as API examples
└── assets/css/
```

### Data flow

```
Mirror content → fixtures/*.json (defaults)
                      ↓
API (later) ──→ pet_studio_widget_settings filter
                      ↓
Content_Normalizer::normalize()
                      ↓
Widget render() — same HTML/CSS as mirror
```

### Hooks (available now)

| Hook | Purpose |
|------|---------|
| `pet_studio_fixture_defaults` | Override fixture JSON before control defaults |
| `pet_studio_widget_settings` | Inject API payload before render |

### Schema ↔ widget map

| Fixture / schema file | Elementor widget name |
|-----------------------|----------------------|
| `header.json` | `pet_studio_header` |
| `footer.json` | `pet_studio_footer` |
| `cookie-consent.json` | `pet_studio_cookie_consent` |
| `hero-home.json` | `pet_studio_hero_home` |
| `hero-inner.json` | `pet_studio_hero_inner` |
| `services-cards.json` | `pet_studio_services_cards` |
| `about-intro.json` | `pet_studio_about_intro` |
| `page-intro.json` | `pet_studio_page_intro` |
| `content-split.json` | `pet_studio_content_split` |
| `dog-divider.json` | `pet_studio_dog_divider` |
| `courses-tabs.json` | `pet_studio_courses_tabs` |
| `testimonials.json` | `pet_studio_testimonials` |
| `team-member.json` | `pet_studio_team_member` |
| `est-banner.json` | `pet_studio_est_banner` |
| `contact.json` | `pet_studio_contact` |

### Deferred to API phase

- REST endpoints / webhook receiver
- API keys, admin “Connect API” settings
- Cron sync + media sideload from remote URLs
- Conflict resolution (API vs manual Elementor edits)

---

## Out of scope / use existing tools

- **Contact form** — wire to Elementor Pro Form or WPForms via selector/shortcode, don’t rebuild ConvertForms
- **Privacy/cookie legal** — content editable in widget, logic minimal
- **Blog/search** — not in current site
- **Dog Training placeholder content** — client to supply final copy

---

## Summary

| Metric | Count |
|--------|-------|
| Pages | 6 |
| Unique visual section *types* | ~15 |
| **Custom Elementor widgets to build** | **15** |
| Repeat widget placements (approx) | ~45 |
| Global Theme Builder templates | 2 (header, footer) + optional cookie |
