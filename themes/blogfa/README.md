# Blogfa

Modern Persian (Farsi) RTL theme for HTMLy.

## Features

- **RTL** layout with `lang="fa"` and `dir="rtl"`
- **Vazirmatn** font (Google Fonts)
- **Tailwind CSS** (CDN) with a Persian turquoise palette
- **Jalali (Shamsi) dates** with Persian digits — e.g. `۲۱ مهر ۱۴۰۳`
- Magazine-style post list, sticky header, mobile drawer, search overlay

## Requirements

- Set blog language to `fa_IR` in HTMLy config for Persian UI strings
- Internet access for Vazirmatn and Tailwind CDN (or self-host as needed)

## Jalali helper

In theme templates use:

```php
<?php echo jalali_date($p->date); ?>          // ۲۱ مهر ۱۴۰۳
<?php echo jalali_date($p->date, 'full'); ?>  // دوشنبه ۲۱ مهر ۱۴۰۳
<?php echo jalali_date($p->date, 'short'); ?> // ۱۴۰۳/۰۷/۲۱
```

## Activate

Admin → Config → Blog theme → **Blogfa**
