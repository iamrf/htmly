# Modern Bilingual

Production-ready bilingual (LTR/RTL) theme for [HTMLy](https://www.htmly.com/) v3.x.

## Features

- Tailwind CSS (CDN + optional local build)
- Automatic LTR/RTL from `config('language')` with theme override
- Inter (LTR) / Vazirmatn (RTL) typography
- Dark / light mode with `localStorage` persistence
- Jalali calendar via `IntlDateFormatter` + pure-PHP fallback
- Persian digit conversion
- Lucide icons (UI) + **Simple Icons** (CC0 brand pack) for social / share
- Theme social URLs (Telegram, Discord, Threads, WhatsApp, …) + HTMLy core social fallback
- Dashboard settings via `theme.json`
- Referrer/affiliate param injection on external links
- Hero, featured post, TOC, reading progress, share bar, related posts

## Activate

1. Ensure the theme folder is at `themes/modern-bilingual/`
2. In HTMLy admin → Themes → select **Modern Bilingual**
3. Open Theme Settings to configure CTA, hero, sidebar, Jalali, etc.
4. Click **Save Config** — page cache is cleared automatically so guests see changes immediately.

## Local Tailwind build (optional)

```bash
cd themes/modern-bilingual
npm install
npm run build
```

The theme ships with Tailwind CDN for zero-build use. `npm run build` outputs `css/style.build.css` for production hardening.

## Key helpers (`functions.php`)

| Function | Purpose |
|----------|---------|
| `mb_dir()` / `mb_lang_code()` | Direction & language |
| `mb_date($ts, $format)` | Localized / Jalali dates |
| `mb_reading_time($p)` | Reading time |
| `mb_referrer_url($url)` | Append affiliate param |
| `mb_render_card($p)` | Post card markup |
| `mb_brand_icon($slug)` | Inline Simple Icons SVG |
| `mb_render_social()` | Profile social icon nav |
| `mb_share_buttons($p)` | Post share actions |

## Social / brands

Theme Settings → fill `Social:*` URLs (Telegram, Discord, Threads, WhatsApp, etc.). Empty fields fall back to HTMLy **Config → Widgets** social URLs. Brand SVGs live in `icons/brands.php` ([Simple Icons](https://simpleicons.org/), CC0).

## License

Theme: GPL-2.0-or-later (same as HTMLy). Brand paths: CC0 (Simple Icons).
