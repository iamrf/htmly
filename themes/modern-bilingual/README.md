# Modern Bilingual

Production-ready bilingual (LTR/RTL) theme for [HTMLy](https://www.htmly.com/) v3.x.

## Features

- Tailwind CSS (CDN + optional local build)
- Automatic LTR/RTL from `config('language')` with theme override
- Inter (LTR) / Vazirmatn (RTL) typography
- Dark / light mode with `localStorage` persistence
- Jalali calendar via `IntlDateFormatter` + pure-PHP fallback
- Persian digit conversion
- Lucide icons
- Dashboard settings via `theme.json`
- Referrer/affiliate param injection on external links
- Hero, featured post, TOC, reading progress, share bar, related posts

## Activate

1. Ensure the theme folder is at `themes/modern-bilingual/`
2. In HTMLy admin → Themes → select **Modern Bilingual**
3. Open Theme Settings to configure CTA, hero, sidebar, Jalali, etc.

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

## License

GPL-2.0-or-later (same as HTMLy)
