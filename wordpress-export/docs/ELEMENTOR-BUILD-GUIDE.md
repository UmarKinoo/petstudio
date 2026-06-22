# The Pet Studio → WordPress Elementor build guide

## What’s in this folder

```
wordpress-export/
├── media/           # Upload these to WP Media Library
│   ├── logos/
│   ├── photos/
│   ├── icons/
│   └── video/
├── content/
│   └── site-content.json   # Nav, contact, testimonials, services
├── docs/
│   └── design-tokens.json  # Colors & fonts for Elementor globals
└── pages-html/      # Visual reference only (do not paste into HTML widgets)
```

## Fastest approach (no custom widgets)

### 1. Stack
- WordPress + **Hello Elementor** theme (minimal)
- **Elementor Pro** (Theme Builder, forms, carousel)
- Optional: **FileBird** or **HappyFiles** to mirror media folders

### 2. One-time setup (~30 min)
1. Install fonts: **Manrope** + **Noto Sans** (Google Fonts plugin or Elementor)
2. Elementor → Site Settings → Global Colors (from `design-tokens.json`)
3. Theme Builder → Header (logo + Nav Menu) + Footer (contact, logo, hours)
4. Upload everything in `media/` to Media Library

### 3. Build 6 pages with native widgets

| Page | Elementor widgets to use |
|------|--------------------------|
| **Home** | Video background section, Heading, 3× Image Box (services), Image + Text (team), Testimonial Carousel, CTA |
| **Dog Grooming** | Inner Hero (Image/Video), Text Editor, Image Gallery, Icon List, Button |
| **Grooming Academy** | Same pattern |
| **Dog Training** | Same pattern |
| **Team** | Image Box grid (3 cols) or Loop Grid if using CPT later |
| **Contact** | Form, Icon List (phone/email/hours), Google Maps |

### 4. Reusable sections (save as Templates)
- `Section: Testimonials` → Testimonial Carousel (14 items from JSON)
- `Section: Service Cards` → 3-column Image Box
- `Section: Contact CTA` → Heading + Button + Icon List
- `Section: Footer`

Duplicate Home sections onto inner pages where layouts repeat.

### 5. What to skip
- Do **not** import Joomla/YOOtheme CSS
- Do **not** paste full pages into HTML widgets
- Do **not** recreate parallax/sticky scroll exactly on v1 — use static sections first, add motion later if needed

## Estimated effort
- Setup + globals: 0.5 day
- Header/footer: 0.5 day
- Home page: 1 day
- 5 inner pages: 1–1.5 days
- QA + mobile: 0.5 day

**Total: ~3–4 days** vs 2+ weeks for custom widgets.

## Optional speed-ups
- **Elementor Template Kit** (pet/wellness) as layout starter — swap colors to `#FF90AA` and replace images
- **ACF + CPT** for Team/Testimonials only if client needs to edit them often (adds dev time)
- Build Home first, get sign-off, then clone structure for inner pages
