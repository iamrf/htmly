# AGENTS.md — HTMLy

Guidance for AI coding agents working in this repository.

## Project

| Item | Value |
|------|--------|
| Product | [HTMLy](https://www.htmly.com/) — flat-file blogging CMS |
| Stack | PHP 7.2+, Markdown content, no SQL database |
| Version constant | `HTMLY_VERSION` in `index.php` |
| License | GPL-2.0-or-later |

## Repo map

```
index.php                 # bootstrap, HTMLY / HTMLY_VERSION
system/htmly.php          # HTTP routes (get/post)
system/includes/
  dispatch.php            # router, render(), theme_path(), theme_config()
  functions.php           # core helpers (menu, posts, i18n, …)
system/admin/views/       # admin UI
themes/<name>/            # public themes (views.root)
content/                  # posts, pages, data (runtime)
config/                   # config.ini, themes/<name>.ini
```

## Theme contract

HTMLy loads views from `config('views.root')` (example: `themes/modern-bilingual`).

### Required / common files

| File | Role |
|------|------|
| `layout.html.php` | Master HTML shell; call `content()` for the view |
| `main.html.php` | Post lists (home fallback for archives) |
| `post.html.php` | Single post |
| `static.html.php` | Static pages |
| `profile.html.php` | Author archive |
| `404.html.php` / `404-search.html.php` | Errors |
| `no-posts.html.php` | Empty blog |
| `functions.php` | Theme helpers (optional; included every render) |
| `theme.json` | Admin theme settings schema |

### Optional view overrides (checked by core)

- `main--front.html.php`, `main--category.html.php`, `main--tag.html.php`, `main--archive.html.php`, `main--search.html.php`
- `static--front.html.php`
- `layout--category.html.php`, `layout--tag.html.php`, etc.
- Per-slug: `main--category--{slug}.html.php`

Semantic aliases such as `category.html.php` are **not** auto-loaded unless a `main--category.html.php` includes them.

### Settings

1. Define fields in `theme.json` → `settings[]`.
2. Supported types in admin UI: `checkbox`, `select` (+ `options`), and HTML `input` types (`text`, `url`, …).
3. Values persist to `config/themes/<theme-folder-name>.ini`.
4. Read with `theme_config('name')`. Prefer a theme helper with defaults (e.g. `mb_config()` in Modern Bilingual).

### First line of every template

```php
<?php if (!defined('HTMLY')) die('HTMLy'); ?>
```

## Core APIs agents should use

```php
config('key')
theme_config('key')
site_url()
theme_path()
blog_title() / blog_tagline() / blog_description() / blog_language()
menu($class)
social($class)
copyright()
i18n('Key')
format_date($timestamp)
get_teaser($body, $url)
get_related($tags, true, $count)
recent_posts(true) / popular_posts(true)   // true ⇒ array of post objects
category_list() / tag_cloud() / archive_list()
head_contents()
is_index()
login() / toolbar() / authorized($p)
analytics() / facebook() / disqus() / disqus_count()
shorten($html, $len)
get_image($body)
get_video_id($url)
content()   // only in layout
```

## Post object fields (typical)

`$p->title`, `$p->url`, `$p->body`, `$p->date`, `$p->category`, `$p->tag`, `$p->related`, `$p->authorName`, `$p->authorUrl`, `$p->authorAvatar`, `$p->image`, `$p->video`, `$p->audio`, `$p->quote`, `$p->link`, `$p->readTime`, `$p->slug` (when present).

Always guard missing media/fields with `!empty()` / `isset()`.

## Modern Bilingual theme

Path: `themes/modern-bilingual/`

- Tailwind CDN + `package.json` / `tailwind.config.js` for local builds
- Lucide icons, Inter / Vazirmatn
- Direction from `config('language')` (`fa|ar|he|ur` → RTL) or `text_direction` setting
- Jalali dates: `mb_date()` via Intl + pure-PHP fallback
- Helpers prefixed `mb_` in `functions.php`
- Referrer param: `referrer_param` setting + `js/app.js` outbound link rewriter

When extending this theme, keep logical Tailwind utilities and `mb_*` naming.

## Agent do / don't

**Do**

- Match existing theme patterns in `themes/blog` before inventing new template names.
- Use `theme.json` (not a custom admin form) for theme options.
- Keep RTL-safe spacing (logical properties).
- Defend against empty `$posts`, missing images, unset `theme_config` keys.

**Don't**

- Assume WordPress / Laravel template APIs.
- Write SQL or introduce a DB layer for theme features.
- Commit unless the user asks.
- Force-push `master` / `main`.
- Skip the `HTMLY` guard on templates.

## Quick verify

1. Set theme in admin (or `views.root = "themes/modern-bilingual"` in config).
2. Hit `/`, a post URL, `/category/...`, `/tag/...`, `/search/...`, a bad path (404).
3. Toggle language to `fa_IR` and confirm `dir="rtl"`, Vazirmatn, Jalali dates.
4. Open Theme Settings and save CTA / hero / sidebar options.
