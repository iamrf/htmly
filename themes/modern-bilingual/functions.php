<?php if (!defined('HTMLY')) die('HTMLy');

/**
 * Modern Bilingual theme helpers.
 * Prefix: mb_ (modern-bilingual) to avoid collisions with core / other themes.
 *
 * HTMLy persists theme settings via json_encode() into config/themes/<theme>.ini.
 * Values must be normalized; missing keys fall back to theme.json "value" defaults.
 */

/**
 * Defaults from theme.json settings[].value (checkbox true/false → 1/0).
 */
if (!function_exists('mb_setting_defaults')) {
    function mb_setting_defaults()
    {
        static $defaults = null;
        if ($defaults !== null) {
            return $defaults;
        }
        $defaults = array();
        $path = __DIR__ . '/theme.json';
        if (!file_exists($path)) {
            return $defaults;
        }
        $json = json_decode(file_get_contents($path), true);
        if (empty($json['settings']) || !is_array($json['settings'])) {
            return $defaults;
        }
        foreach ($json['settings'] as $setting) {
            if (empty($setting['name'])) {
                continue;
            }
            $name = $setting['name'];
            $value = array_key_exists('value', $setting) ? $setting['value'] : '';
            if (isset($setting['type']) && $setting['type'] === 'checkbox') {
                $defaults[$name] = ($value === 'true' || $value === '1' || $value === 1 || $value === true) ? '1' : '0';
            } else {
                $defaults[$name] = is_bool($value) ? ($value ? '1' : '0') : (string)$value;
            }
        }
        return $defaults;
    }
}

/**
 * Normalize values read from HTMLy theme INI (JSON-encoded strings, booleans, ints).
 */
if (!function_exists('mb_normalize_config_value')) {
    function mb_normalize_config_value($val)
    {
        if ($val === null) {
            return null;
        }
        if (is_bool($val)) {
            return $val ? '1' : '0';
        }
        if (is_int($val) || is_float($val)) {
            return (string)$val;
        }
        if (is_array($val)) {
            return $val;
        }
        $val = trim((string)$val);
        // Raw JSON string still quoted in memory (edge cases / RAW scanner)
        if (strlen($val) >= 2 && $val[0] === '"' && substr($val, -1) === '"') {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return mb_normalize_config_value($decoded);
            }
            $val = substr($val, 1, -1);
        }
        return $val;
    }
}

/**
 * Raw theme_config with normalization (null if unset).
 */
if (!function_exists('mb_raw_config')) {
    function mb_raw_config($key)
    {
        if (!function_exists('theme_config')) {
            return null;
        }
        // Avoid colliding with theme_config('source', …) internal API
        if ($key === 'source') {
            return null;
        }
        $val = theme_config($key);
        if ($val === null) {
            return null;
        }
        return mb_normalize_config_value($val);
    }
}

/**
 * Theme config with theme.json default fallback.
 * Explicit empty string is preserved (does not fall back).
 */
if (!function_exists('mb_config')) {
    function mb_config($key, $default = null)
    {
        $val = mb_raw_config($key);
        if ($val === null) {
            if ($default !== null) {
                return $default;
            }
            $defaults = mb_setting_defaults();
            return array_key_exists($key, $defaults) ? $defaults[$key] : null;
        }
        return $val;
    }
}

/**
 * Checkbox helper. HTMLy stores "1"/"0" (JSON-encoded in INI).
 * Unset keys use $default, then theme.json default.
 */
if (!function_exists('mb_enabled')) {
    function mb_enabled($key, $default = null)
    {
        $val = mb_raw_config($key);
        if ($val === null) {
            if ($default !== null) {
                return (bool)$default;
            }
            $defaults = mb_setting_defaults();
            if (array_key_exists($key, $defaults)) {
                $val = $defaults[$key];
            } else {
                return false;
            }
        }
        if (is_bool($val)) {
            return $val;
        }
        $val = strtolower(trim((string)$val));
        if (in_array($val, array('0', 'false', 'off', 'no', 'none', ''), true)) {
            return false;
        }
        return in_array($val, array('1', 'true', 'on', 'yes'), true);
    }
}

/**
 * Seed config/themes/modern-bilingual.ini with any missing keys from theme.json
 * (same json_encode format HTMLy admin uses) and reload theme_config.
 */
if (!function_exists('mb_ensure_theme_ini')) {
    function mb_ensure_theme_ini()
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        $views = config('views.root');
        if (empty($views) || strpos($views, 'modern-bilingual') === false) {
            return;
        }

        $defaults = mb_setting_defaults();
        if (empty($defaults)) {
            return;
        }

        $dir = 'config/themes';
        $file = $dir . '/modern-bilingual.ini';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $existing = array();
        if (file_exists($file)) {
            $parsed = @parse_ini_file($file, true);
            if (is_array($parsed)) {
                $existing = $parsed;
            }
        }

        $lines = file_exists($file) ? rtrim((string)file_get_contents($file)) : '';
        $added = false;
        foreach ($defaults as $key => $value) {
            if (array_key_exists($key, $existing)) {
                continue;
            }
            $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $lines .= "\n" . $key . ' = ' . $encoded;
            $added = true;
        }

        if ($added) {
            @file_put_contents($file, ltrim($lines) . "\n", LOCK_EX);
        }

        if (file_exists($file) && function_exists('theme_config')) {
            theme_config('source', $file);
        }

        // Bust HTMLy page cache when theme settings INI changes (admin save)
        mb_bust_cache_if_settings_changed($file);
    }
}

/**
 * Clear cache/page when theme INI or key theme assets are newer than last stamp.
 * Ensures post/comments/TOC markup + CSS/JS updates are not stuck behind HTML cache.
 */
if (!function_exists('mb_bust_cache_if_settings_changed')) {
    function mb_bust_cache_if_settings_changed($iniFile = null)
    {
        $watch = array(
            $iniFile,
            __DIR__ . '/functions.php',
            __DIR__ . '/post.html.php',
            __DIR__ . '/layout.html.php',
            __DIR__ . '/css/style.css',
            __DIR__ . '/js/app.js',
            __DIR__ . '/partials/comments.html.php',
        );
        $mtime = 0;
        foreach ($watch as $file) {
            if (!empty($file) && is_file($file)) {
                $mtime = max($mtime, (int)filemtime($file));
            }
        }
        if ($mtime <= 0) {
            return;
        }
        $stampFile = 'cache/page/.mb-theme-stamp';
        $prev = 0;
        if (file_exists($stampFile)) {
            $prev = (int)trim((string)@file_get_contents($stampFile));
        }
        if ($mtime <= $prev) {
            return;
        }
        if (!is_dir('cache/page')) {
            @mkdir('cache/page', 0775, true);
        }
        foreach (glob('cache/page/*.cache') ?: array() as $cacheFile) {
            @unlink($cacheFile);
        }
        @file_put_contents($stampFile, (string)$mtime, LOCK_EX);
    }
}

// Boot settings as soon as theme functions load
mb_ensure_theme_ini();
// Also watch theme source files (not only INI) for page-cache invalidation
mb_bust_cache_if_settings_changed(null);

/**
 * Active language code (e.g. fa, en, ar).
 */
if (!function_exists('mb_lang_code')) {
    function mb_lang_code()
    {
        $lang = config('language');
        if (empty($lang)) {
            return 'en';
        }
        $parts = explode('_', str_replace('-', '_', $lang));
        return strtolower($parts[0]);
    }
}

/**
 * HTML lang attribute (e.g. fa-IR, en-US).
 */
if (!function_exists('mb_html_lang')) {
    function mb_html_lang()
    {
        return blog_language();
    }
}

/**
 * Resolve text direction: auto from language, or theme override.
 */
if (!function_exists('mb_dir')) {
    function mb_dir()
    {
        $override = mb_config('text_direction', 'auto');
        if ($override === 'rtl' || $override === 'ltr') {
            return $override;
        }
        $rtl = array('fa', 'ar', 'he', 'ur');
        return in_array(mb_lang_code(), $rtl, true) ? 'rtl' : 'ltr';
    }
}

/**
 * Whether current locale is RTL.
 */
if (!function_exists('mb_is_rtl')) {
    function mb_is_rtl()
    {
        return mb_dir() === 'rtl';
    }
}

/**
 * Whether Jalali calendar should be used.
 */
if (!function_exists('mb_use_jalali')) {
    function mb_use_jalali()
    {
        if (mb_enabled('jalali_date', false)) {
            return true;
        }
        return mb_lang_code() === 'fa';
    }
}

/**
 * Whether to convert digits to Persian.
 */
if (!function_exists('mb_use_persian_digits')) {
    function mb_use_persian_digits()
    {
        if (mb_enabled('persian_digits', false)) {
            return true;
        }
        return mb_is_rtl() && mb_lang_code() === 'fa';
    }
}

/**
 * Convert Latin digits to Persian digits.
 */
if (!function_exists('mb_persian_digits')) {
    function mb_persian_digits($str)
    {
        $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        return str_replace($en, $fa, (string)$str);
    }
}

/**
 * Optionally apply Persian digits.
 */
if (!function_exists('mb_digits')) {
    function mb_digits($str)
    {
        if (mb_use_persian_digits()) {
            return mb_persian_digits($str);
        }
        return (string)$str;
    }
}

/**
 * Pure-PHP Gregorian → Jalali conversion.
 * Returns array($year, $month, $day).
 */
if (!function_exists('mb_gregorian_to_jalali')) {
    function mb_gregorian_to_jalali($gy, $gm, $gd)
    {
        $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
        $jy = -1595 + (33 * ((int)($days / 12053)));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return array($jy, $jm, $jd);
    }
}

/**
 * Jalali month names.
 */
if (!function_exists('mb_jalali_month_name')) {
    function mb_jalali_month_name($month)
    {
        $months = array(
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        );
        return isset($months[(int)$month]) ? $months[(int)$month] : '';
    }
}

/**
 * Format date for the theme (Jalali or Gregorian).
 *
 * Formats: long | full | short | month | iso
 *
 * @param int|string $date Unix timestamp or parseable date
 * @param string $format
 * @return string
 */
if (!function_exists('mb_date')) {
    function mb_date($date, $format = 'long')
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        if (empty($date)) {
            return '';
        }

        if (!mb_use_jalali()) {
            switch ($format) {
                case 'short':
                    $out = date('Y-m-d', $date);
                    break;
                case 'month':
                    $out = date('F Y', $date);
                    break;
                case 'full':
                    $out = date('l, F j, Y', $date);
                    break;
                case 'iso':
                    return date('c', $date);
                case 'long':
                default:
                    $out = format_date($date);
                    break;
            }
            return mb_digits($out);
        }

        // Prefer IntlDateFormatter with Persian calendar when available
        if (class_exists('IntlDateFormatter') && extension_loaded('intl')) {
            try {
                $patterns = array(
                    'short' => 'yyyy/MM/dd',
                    'month' => 'MMMM yyyy',
                    'full'  => 'EEEE d MMMM yyyy',
                    'long'  => 'd MMMM yyyy',
                    'iso'   => "yyyy-MM-dd'T'HH:mm:ssxxx",
                );
                $pattern = isset($patterns[$format]) ? $patterns[$format] : $patterns['long'];
                $fmt = new IntlDateFormatter(
                    'fa_IR@calendar=persian',
                    IntlDateFormatter::FULL,
                    IntlDateFormatter::NONE,
                    date_default_timezone_get(),
                    IntlDateFormatter::TRADITIONAL,
                    $pattern
                );
                $out = $fmt->format($date);
                if ($out !== false && $out !== '') {
                    return mb_use_persian_digits() ? mb_persian_digits($out) : $out;
                }
            } catch (Exception $e) {
                // fall through to pure-PHP
            }
        }

        $gy = (int)date('Y', $date);
        $gm = (int)date('n', $date);
        $gd = (int)date('j', $date);
        list($jy, $jm, $jd) = mb_gregorian_to_jalali($gy, $gm, $gd);

        $weekdays = array('یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه');
        $weekday = $weekdays[(int)date('w', $date)];

        switch ($format) {
            case 'short':
                $out = sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
                break;
            case 'month':
                $out = mb_jalali_month_name($jm) . ' ' . $jy;
                break;
            case 'full':
                $out = $weekday . ' ' . $jd . ' ' . mb_jalali_month_name($jm) . ' ' . $jy;
                break;
            case 'iso':
                $out = sprintf('%04d-%02d-%02d', $jy, $jm, $jd);
                break;
            case 'long':
            default:
                $out = $jd . ' ' . mb_jalali_month_name($jm) . ' ' . $jy;
                break;
        }

        return mb_digits($out);
    }
}

/**
 * Reading time in minutes (uses $p->readTime when available).
 */
if (!function_exists('mb_reading_time')) {
    function mb_reading_time($p)
    {
        if (is_object($p) && isset($p->readTime) && $p->readTime > 0) {
            return (int)$p->readTime;
        }
        $body = '';
        if (is_object($p) && isset($p->body)) {
            $body = $p->body;
        } elseif (is_string($p)) {
            $body = $p;
        }
        $text = trim(strip_tags($body));
        if ($text === '') {
            return 1;
        }
        // Approximate word count for CJK/RTL scripts by characters/5 when Latin words are scarce
        $words = str_word_count($text);
        if ($words < 5 && function_exists('mb_strlen')) {
            $words = (int)ceil(mb_strlen($text, 'UTF-8') / 5);
        }
        $minutes = (int)ceil($words / 200);
        return max(1, $minutes);
    }
}

/**
 * Reading time label with i18n-friendly text.
 */
if (!function_exists('mb_reading_label')) {
    function mb_reading_label($p)
    {
        $mins = mb_reading_time($p);
        $n = mb_digits((string)$mins);
        if (mb_lang_code() === 'fa') {
            return $n . ' دقیقه مطالعه';
        }
        if (mb_lang_code() === 'ar') {
            return $n . ' دقيقة قراءة';
        }
        return $n . ' min read';
    }
}

/**
 * Build / append referrer query param to a URL.
 */
if (!function_exists('mb_referrer_url')) {
    function mb_referrer_url($url)
    {
        $param = trim((string)mb_config('referrer_param', ''));
        if ($param === '' || empty($url)) {
            return $url;
        }
        // Allow raw query fragment like "ref=myblog" or "?ref=myblog" or "utm_source=x&utm_medium=y"
        $param = ltrim($param, '?&');
        if ($param === '') {
            return $url;
        }
        $sep = (strpos($url, '?') !== false) ? '&' : '?';
        return $url . $sep . $param;
    }
}

/**
 * Escaped referrer param for data attributes / JS.
 */
if (!function_exists('mb_referrer_param')) {
    function mb_referrer_param()
    {
        return ltrim(trim((string)mb_config('referrer_param', '')), '?&');
    }
}

/**
 * Resolve featured image URL for a post.
 */
if (!function_exists('mb_post_image')) {
    function mb_post_image($p)
    {
        if (!empty($p->image)) {
            return $p->image;
        }
        if (!empty($p->video)) {
            return '//img.youtube.com/vi/' . get_video_id($p->video) . '/sddefault.jpg';
        }
        if (!empty($p->body) && function_exists('get_image')) {
            $img = get_image($p->body);
            if (!empty($img)) {
                return $img;
            }
        }
        return '';
    }
}

/**
 * Render post media block (image / video / audio / quote).
 */
if (!function_exists('mb_post_media')) {
    function mb_post_media($p, $teaser = false)
    {
        $html = '';
        $title = htmlspecialchars(isset($p->title) ? $p->title : '', ENT_QUOTES, 'UTF-8');
        $url = isset($p->url) ? $p->url : '#';

        if (!empty($p->image)) {
            if ($teaser) {
                $html = '<a class="mb-media block overflow-hidden rounded-xl" href="' . $url . '"><img class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105" src="' . htmlspecialchars($p->image, ENT_QUOTES, 'UTF-8') . '" alt="' . $title . '" loading="lazy"></a>';
            } else {
                $html = '<div class="mb-media overflow-hidden rounded-2xl"><img class="aspect-[16/9] w-full object-cover" src="' . htmlspecialchars($p->image, ENT_QUOTES, 'UTF-8') . '" alt="' . $title . '" itemprop="image"></div>';
            }
        } elseif (!empty($p->video)) {
            $vid = get_video_id($p->video);
            if ($teaser) {
                $html = '<a class="mb-media relative block overflow-hidden rounded-xl" href="' . $url . '"><img class="aspect-[16/10] w-full object-cover" src="//img.youtube.com/vi/' . $vid . '/sddefault.jpg" alt="' . $title . '" loading="lazy"><span class="absolute inset-0 flex items-center justify-center bg-black/30">' . mb_icon('play', 'h-10 w-10 text-white') . '</span></a>';
            } else {
                $html = '<div class="mb-media relative overflow-hidden rounded-2xl" style="padding-top:56.25%"><iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/' . $vid . '" frameborder="0" allowfullscreen loading="lazy"></iframe></div>';
            }
        } elseif (!empty($p->audio)) {
            if ($teaser) {
                $cover = theme_path() . 'img/audio-cover.svg';
                $html = '<a class="mb-media relative block overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800" href="' . $url . '"><div class="flex aspect-[16/10] items-center justify-center">' . mb_icon('music', 'h-12 w-12 text-zinc-400') . '</div></a>';
            } else {
                $html = '<iframe class="mb-media w-full rounded-2xl" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . htmlspecialchars($p->audio, ENT_QUOTES, 'UTF-8') . '&amp;auto_play=false&amp;visual=true"></iframe>';
            }
        } elseif (!empty($p->quote)) {
            $html = '<blockquote class="mb-quote border-s-4 border-zinc-900 ps-5 text-xl font-medium leading-relaxed text-zinc-700 dark:border-zinc-100 dark:text-zinc-200 md:text-2xl">' . $p->quote . '</blockquote>';
        }

        return $html;
    }
}

/**
 * Sidebar position: end | start | hidden
 */
if (!function_exists('mb_sidebar_position')) {
    function mb_sidebar_position()
    {
        $pos = mb_config('sidebar_position', 'end');
        if (!in_array($pos, array('end', 'start', 'hidden'), true)) {
            return 'end';
        }
        return $pos;
    }
}

/**
 * Post card style: grid | list
 */
if (!function_exists('mb_card_style')) {
    function mb_card_style()
    {
        $style = mb_config('post_card_style', 'grid');
        return ($style === 'list') ? 'list' : 'grid';
    }
}

/**
 * Default color mode: system | dark | light
 */
if (!function_exists('mb_default_color_mode')) {
    function mb_default_color_mode()
    {
        $mode = mb_config('default_color_mode', 'system');
        if (!in_array($mode, array('system', 'dark', 'light'), true)) {
            return 'system';
        }
        return $mode;
    }
}

/**
 * CTA button config.
 */
if (!function_exists('mb_cta')) {
    function mb_cta()
    {
        return array(
            'enabled' => mb_enabled('cta_enable', false),
            'text'    => mb_config('cta_text', 'Get Started'),
            'url'     => mb_config('cta_url', '#'),
            'target'  => mb_config('cta_target', '_self') === '_blank' ? '_blank' : '_self',
            'style'   => mb_config('cta_style', 'primary') === 'outline' ? 'outline' : 'primary',
        );
    }
}

/**
 * Hero config.
 */
if (!function_exists('mb_hero')) {
    function mb_hero()
    {
        return array(
            'enabled'  => mb_enabled('hero_enable', true),
            'title'    => mb_config('hero_title', ''),
            'subtitle' => mb_config('hero_subtitle', ''),
            'bg'       => mb_config('hero_bg_style', 'gradient'),
            'search'   => mb_enabled('hero_search', true),
        );
    }
}

/**
 * Safe excerpt from post body.
 */
if (!function_exists('mb_excerpt')) {
    function mb_excerpt($p, $length = 140)
    {
        if (!isset($p->body)) {
            return '';
        }
        $text = shorten($p->body, $length);
        return trim($text);
    }
}

/**
 * Render a single post card (grid or list).
 */
if (!function_exists('mb_render_card')) {
    function mb_render_card($p, $style = null)
    {
        if ($style === null) {
            $style = mb_card_style();
        }
        $img = mb_post_image($p);
        $title = isset($p->title) ? $p->title : '';
        $url = !empty($p->link) ? $p->link : (isset($p->url) ? $p->url : '#');
        $external = !empty($p->link);
        $target = $external ? ' target="_blank" rel="noopener"' : '';
        $date = isset($p->date) ? mb_date($p->date, 'long') : '';
        $datetime = isset($p->date) ? date('c', $p->date) : '';
        $author = isset($p->authorName) ? $p->authorName : '';
        $authorUrl = isset($p->authorUrl) ? $p->authorUrl : '#';
        $avatar = isset($p->authorAvatar) ? $p->authorAvatar : '';
        $category = isset($p->category) ? $p->category : '';
        $excerpt = mb_excerpt($p, $style === 'list' ? 180 : 120);
        $read = mb_reading_label($p);

        ob_start();
        if ($style === 'list') {
            ?>
            <article class="group flex flex-col gap-5 border-b border-zinc-200 py-8 last:border-0 dark:border-zinc-800 sm:flex-row sm:items-start" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                <?php if ($img): ?>
                <a class="block w-full shrink-0 overflow-hidden rounded-xl sm:w-56" href="<?php echo $url; ?>"<?php echo $target; ?>>
                    <img class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105" src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" itemprop="image">
                </a>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <div class="mb-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                        <?php if ($category): ?><span class="rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300" itemprop="articleSection"><?php echo $category; ?></span><?php endif; ?>
                        <time datetime="<?php echo $datetime; ?>" itemprop="datePublished"><?php echo $date; ?></time>
                        <span><?php echo $read; ?></span>
                    </div>
                    <h2 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50" itemprop="headline">
                        <a class="transition hover:text-zinc-600 dark:hover:text-zinc-300" href="<?php echo $url; ?>"<?php echo $target; ?>><?php echo $title; ?><?php if ($external): ?> <?php echo mb_icon('external-link', 'inline h-4 w-4 opacity-50'); ?><?php endif; ?></a>
                    </h2>
                    <?php if ($excerpt): ?>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400" itemprop="description"><?php echo $excerpt; ?>…</p>
                    <?php endif; ?>
                    <div class="mt-4 flex items-center gap-2">
                        <?php if ($avatar): ?><img class="h-7 w-7 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-700" src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="28" height="28"><?php endif; ?>
                        <a class="text-sm font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100" href="<?php echo $authorUrl; ?>" itemprop="author"><?php echo $author; ?></a>
                        <?php if (function_exists('authorized') && authorized($p)): ?>
                        <a class="ms-auto text-xs text-zinc-400 hover:text-zinc-700" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php
        } else {
            ?>
            <article class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                <?php if ($img): ?>
                <a class="block overflow-hidden" href="<?php echo $url; ?>"<?php echo $target; ?>>
                    <img class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105" src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" itemprop="image">
                </a>
                <?php endif; ?>
                <div class="flex flex-1 flex-col p-5">
                    <div class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                        <?php if ($category): ?><span class="rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300" itemprop="articleSection"><?php echo $category; ?></span><?php endif; ?>
                        <time datetime="<?php echo $datetime; ?>" itemprop="datePublished"><?php echo $date; ?></time>
                    </div>
                    <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-50" itemprop="headline">
                        <a class="transition hover:text-zinc-600 dark:hover:text-zinc-300" href="<?php echo $url; ?>"<?php echo $target; ?>><?php echo $title; ?><?php if ($external): ?> <?php echo mb_icon('external-link', 'inline h-4 w-4 opacity-50'); ?><?php endif; ?></a>
                    </h2>
                    <?php if ($excerpt): ?>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400"><?php echo $excerpt; ?>…</p>
                    <?php endif; ?>
                    <div class="mt-4 flex items-center gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <?php if ($avatar): ?><img class="h-7 w-7 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-700" src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="28" height="28"><?php endif; ?>
                        <div class="min-w-0 flex-1">
                            <a class="block truncate text-sm font-medium text-zinc-700 dark:text-zinc-300" href="<?php echo $authorUrl; ?>"><?php echo $author; ?></a>
                            <span class="text-xs text-zinc-400"><?php echo $read; ?></span>
                        </div>
                    </div>
                </div>
            </article>
            <?php
        }
        return ob_get_clean();
    }
}

/**
 * Featured / pinned post: first post on homepage, or URL from settings.
 */
if (!function_exists('mb_featured_post')) {
    function mb_featured_post($posts = null)
    {
        if (!mb_enabled('featured_post', true)) {
            return null;
        }
        $slug = trim((string)mb_config('featured_post_url', ''));
        if ($slug !== '' && is_array($posts)) {
            foreach ($posts as $p) {
                if (isset($p->url) && (strpos($p->url, $slug) !== false || (isset($p->slug) && $p->slug === $slug))) {
                    return $p;
                }
            }
        }
        if (is_array($posts) && !empty($posts)) {
            return $posts[0];
        }
        return null;
    }
}

/* -------------------------------------------------------------------------- */
/* Brand icons (Simple Icons CC0) + social / share UI                         */
/* -------------------------------------------------------------------------- */

if (!function_exists('mb_load_brands')) {
    function mb_load_brands()
    {
        static $loaded = false;
        if (!$loaded) {
            $file = __DIR__ . '/icons/brands.php';
            if (file_exists($file)) {
                include_once $file;
            }
            $loaded = true;
        }
    }
}

if (!function_exists('mb_load_lucide')) {
    function mb_load_lucide()
    {
        static $loaded = false;
        if (!$loaded) {
            $file = __DIR__ . '/icons/lucide.php';
            if (file_exists($file)) {
                include_once $file;
            }
            $loaded = true;
        }
    }
}

/**
 * Render an inline brand SVG (Simple Icons).
 *
 * @param string $name Icon slug (x, facebook, telegram, …)
 * @param string $class Extra CSS classes
 * @return string
 */
if (!function_exists('mb_brand_icon')) {
    function mb_brand_icon($name, $class = 'mb-brand-icon')
    {
        mb_load_brands();
        if (!function_exists('mb_brand_icons_data')) {
            return '';
        }
        $all = mb_brand_icons_data();
        $key = strtolower(trim((string)$name));
        if ($key === 'twitter') {
            $key = 'x';
        }
        if (!isset($all[$key])) {
            return '';
        }
        $icon = $all[$key];
        $label = htmlspecialchars($icon['label'], ENT_QUOTES, 'UTF-8');
        $path = $icon['path'];
        $class = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
        return '<svg class="' . $class . '" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
            . '<title>' . $label . '</title>'
            . '<path fill="currentColor" d="' . $path . '"/>'
            . '</svg>';
    }
}

/**
 * Render a local Lucide UI icon (stroke SVG). No CDN / unpkg.
 *
 * @param string $name Icon slug (search, moon, arrow-left, …)
 * @param string $class Extra CSS classes
 * @return string
 */
if (!function_exists('mb_icon')) {
    function mb_icon($name, $class = 'mb-icon')
    {
        mb_load_lucide();
        if (!function_exists('mb_lucide_icons_data')) {
            return '';
        }
        $all = mb_lucide_icons_data();
        $key = strtolower(trim((string)$name));
        if (!isset($all[$key])) {
            return '';
        }
        $icon = $all[$key];
        $label = htmlspecialchars($icon['label'], ENT_QUOTES, 'UTF-8');
        $class = trim('mb-icon ' . (string)$class);
        $class = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
        // Inner markup is trusted static SVG from icons/lucide.php
        return '<svg class="' . $class . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" role="img" aria-hidden="true" focusable="false">'
            . '<title>' . $label . '</title>'
            . $icon['inner']
            . '</svg>';
    }
}

/**
 * Network definitions: theme setting key, core config key, icon, label.
 * Theme settings override core config when both are set.
 */
if (!function_exists('mb_social_network_defs')) {
    function mb_social_network_defs()
    {
        return array(
            'x'         => array('theme' => 'social_x',         'core' => 'social.twitter',   'icon' => 'x',         'label' => 'X'),
            'bluesky'   => array('theme' => 'social_bluesky',   'core' => 'social.bluesky',   'icon' => 'bluesky',   'label' => 'Bluesky'),
            'facebook'  => array('theme' => 'social_facebook',  'core' => 'social.facebook',  'icon' => 'facebook',  'label' => 'Facebook'),
            'instagram' => array('theme' => 'social_instagram', 'core' => 'social.instagram', 'icon' => 'instagram', 'label' => 'Instagram'),
            'linkedin'  => array('theme' => 'social_linkedin',  'core' => 'social.linkedin',  'icon' => 'linkedin',  'label' => 'LinkedIn'),
            'github'    => array('theme' => 'social_github',    'core' => 'social.github',    'icon' => 'github',    'label' => 'GitHub'),
            'youtube'   => array('theme' => 'social_youtube',   'core' => 'social.youtube',   'icon' => 'youtube',   'label' => 'YouTube'),
            'mastodon'  => array('theme' => 'social_mastodon',  'core' => 'social.mastodon',  'icon' => 'mastodon',  'label' => 'Mastodon'),
            'tiktok'    => array('theme' => 'social_tiktok',    'core' => 'social.tiktok',    'icon' => 'tiktok',    'label' => 'TikTok'),
            'telegram'  => array('theme' => 'social_telegram',  'core' => null,               'icon' => 'telegram',  'label' => 'Telegram'),
            'whatsapp'  => array('theme' => 'social_whatsapp',  'core' => null,               'icon' => 'whatsapp',  'label' => 'WhatsApp'),
            'discord'   => array('theme' => 'social_discord',   'core' => null,               'icon' => 'discord',   'label' => 'Discord'),
            'threads'   => array('theme' => 'social_threads',   'core' => null,               'icon' => 'threads',   'label' => 'Threads'),
            'medium'    => array('theme' => 'social_medium',    'core' => null,               'icon' => 'medium',    'label' => 'Medium'),
            'reddit'    => array('theme' => 'social_reddit',    'core' => null,               'icon' => 'reddit',    'label' => 'Reddit'),
            'pinterest' => array('theme' => 'social_pinterest', 'core' => null,               'icon' => 'pinterest', 'label' => 'Pinterest'),
            'dribbble'  => array('theme' => 'social_dribbble',  'core' => null,               'icon' => 'dribbble',  'label' => 'Dribbble'),
            'behance'   => array('theme' => 'social_behance',   'core' => null,               'icon' => 'behance',   'label' => 'Behance'),
            'email'     => array('theme' => 'social_email',     'core' => null,               'icon' => 'email',     'label' => 'Email'),
            'website'   => array('theme' => 'social_website',   'core' => null,               'icon' => 'website',   'label' => 'Website'),
        );
    }
}

/**
 * Resolved social profile links (theme settings first, then HTMLy core config).
 *
 * @return array<int, array{id:string,url:string,icon:string,label:string}>
 */
if (!function_exists('mb_social_links')) {
    function mb_social_links()
    {
        $links = array();
        foreach (mb_social_network_defs() as $id => $def) {
            $url = trim((string)mb_config($def['theme'], ''));
            if ($url === '' && !empty($def['core'])) {
                $url = trim((string)config($def['core']));
            }
            if ($url === '') {
                continue;
            }
            if ($id === 'email' && strpos($url, 'mailto:') !== 0 && strpos($url, '@') !== false) {
                $url = 'mailto:' . $url;
            }
            $links[] = array(
                'id'    => $id,
                'url'   => $url,
                'icon'  => $def['icon'],
                'label' => $def['label'],
            );
        }
        if (mb_enabled('social_show_rss', true)) {
            $links[] = array(
                'id'    => 'rss',
                'url'   => site_url() . 'feed/rss',
                'icon'  => 'rss',
                'label' => 'RSS',
            );
        }
        return $links;
    }
}

/**
 * Render social profile icon list (footer / sidebar / navbar).
 *
 * @param string $class Wrapper class
 * @param string $size  sm|md|lg
 * @return string
 */
if (!function_exists('mb_render_social')) {
    function mb_render_social($class = '', $size = 'md')
    {
        $links = mb_social_links();
        if (empty($links)) {
            return '';
        }
        $sizeClass = array(
            'sm' => 'h-8 w-8',
            'md' => 'h-9 w-9',
            'lg' => 'h-10 w-10',
        );
        $box = isset($sizeClass[$size]) ? $sizeClass[$size] : $sizeClass['md'];
        $iconSize = ($size === 'sm') ? 'h-3.5 w-3.5' : (($size === 'lg') ? 'h-5 w-5' : 'h-4 w-4');

        $html = '<nav class="mb-social-links flex flex-wrap items-center gap-2 ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" aria-label="Social links">';
        foreach ($links as $item) {
            $url = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
            $rel = (strpos($item['url'], 'mailto:') === 0) ? '' : ' rel="me noopener noreferrer"';
            $target = (strpos($item['url'], 'mailto:') === 0) ? '' : ' target="_blank"';
            $html .= '<a class="mb-social-btn inline-flex ' . $box . ' items-center justify-center rounded-xl border border-zinc-200 bg-white text-zinc-600 transition hover:border-zinc-400 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-zinc-500 dark:hover:text-white" href="' . $url . '"' . $target . $rel . ' title="' . $label . '" aria-label="' . $label . '">';
            $html .= mb_brand_icon($item['icon'], 'mb-brand-icon ' . $iconSize);
            $html .= '</a>';
        }
        $html .= '</nav>';
        return $html;
    }
}

/**
 * Share action definitions for a given URL/title.
 *
 * @return array<int, array{id:string,url:string,icon:string,label:string,type:string}>
 */
if (!function_exists('mb_share_actions')) {
    function mb_share_actions($url, $title = '')
    {
        $u = rawurlencode($url);
        $t = rawurlencode(strip_tags((string)$title));
        $actions = array(
            array('id' => 'x',        'icon' => 'x',        'label' => 'X',        'type' => 'link', 'url' => 'https://twitter.com/intent/tweet?url=' . $u . '&text=' . $t),
            array('id' => 'facebook', 'icon' => 'facebook', 'label' => 'Facebook', 'type' => 'link', 'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $u),
            array('id' => 'linkedin', 'icon' => 'linkedin', 'label' => 'LinkedIn', 'type' => 'link', 'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u),
            array('id' => 'telegram', 'icon' => 'telegram', 'label' => 'Telegram', 'type' => 'link', 'url' => 'https://t.me/share/url?url=' . $u . '&text=' . $t),
            array('id' => 'whatsapp', 'icon' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'link', 'url' => 'https://api.whatsapp.com/send?text=' . $t . '%20' . $u),
            array('id' => 'reddit',   'icon' => 'reddit',   'label' => 'Reddit',   'type' => 'link', 'url' => 'https://www.reddit.com/submit?url=' . $u . '&title=' . $t),
            array('id' => 'bluesky',  'icon' => 'bluesky',  'label' => 'Bluesky',  'type' => 'link', 'url' => 'https://bsky.app/intent/compose?text=' . $t . '%20' . $u),
            array('id' => 'email',    'icon' => 'email',    'label' => 'Email',    'type' => 'link', 'url' => 'mailto:?subject=' . $t . '&body=' . $u),
            array('id' => 'copy',     'icon' => 'copy',     'label' => (mb_lang_code() === 'fa' ? 'کپی لینک' : 'Copy link'), 'type' => 'copy', 'url' => $url),
        );

        $enabled = mb_config('share_networks', 'x,facebook,linkedin,telegram,whatsapp,reddit,bluesky,email,copy');
        $enabled = array_filter(array_map('trim', explode(',', strtolower((string)$enabled))));
        if (empty($enabled)) {
            return $actions;
        }
        $out = array();
        foreach ($actions as $a) {
            if (in_array($a['id'], $enabled, true)) {
                $out[] = $a;
            }
        }
        return $out;
    }
}

/**
 * Render share buttons for a post/page.
 *
 * @param object|string $p Post object or URL string
 * @param string $variant compact|bar
 * @return string
 */
if (!function_exists('mb_share_buttons')) {
    function mb_share_buttons($p, $variant = 'compact')
    {
        if (is_object($p)) {
            $url = isset($p->url) ? $p->url : site_url();
            $title = isset($p->title) ? $p->title : '';
        } else {
            $url = (string)$p;
            $title = '';
        }
        $actions = mb_share_actions($url, $title);
        if (empty($actions)) {
            return '';
        }

        if ($variant === 'bar') {
            $html = '<div class="mb-share-bar mt-8 flex flex-wrap items-center gap-2 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">';
            $html .= '<span class="me-1 text-sm font-medium text-zinc-600 dark:text-zinc-300">' . (mb_lang_code() === 'fa' ? 'اشتراک‌گذاری' : 'Share') . '</span>';
            foreach ($actions as $a) {
                $label = htmlspecialchars($a['label'], ENT_QUOTES, 'UTF-8');
                if ($a['type'] === 'copy') {
                    $html .= '<button type="button" data-copy-link="' . htmlspecialchars($a['url'], ENT_QUOTES, 'UTF-8') . '" class="mb-share-btn inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm ring-1 ring-zinc-200 transition hover:ring-zinc-400 dark:bg-zinc-800 dark:text-zinc-200 dark:ring-zinc-700">';
                    $html .= '' . mb_icon('copy', 'h-3.5 w-3.5') . '<span>' . $label . '</span></button>';
                } else {
                    $html .= '<a class="mb-share-btn inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm ring-1 ring-zinc-200 transition hover:ring-zinc-400 dark:bg-zinc-800 dark:text-zinc-200 dark:ring-zinc-700" href="' . htmlspecialchars($a['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer" title="' . $label . '" aria-label="' . $label . '">';
                    $html .= mb_brand_icon($a['icon'], 'mb-brand-icon h-3.5 w-3.5');
                    $html .= '<span>' . $label . '</span></a>';
                }
            }
            $html .= '</div>';
            return $html;
        }

        // compact icon row
        $html = '<div class="mb-share-compact flex flex-wrap items-center gap-1">';
        foreach ($actions as $a) {
            $label = htmlspecialchars($a['label'], ENT_QUOTES, 'UTF-8');
            if ($a['type'] === 'copy') {
                $html .= '<button type="button" data-copy-link="' . htmlspecialchars($a['url'], ENT_QUOTES, 'UTF-8') . '" class="mb-share-btn inline-flex h-8 items-center gap-1.5 rounded-lg border border-zinc-200 px-2.5 text-xs font-medium text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800" title="' . $label . '" aria-label="' . $label . '">';
                $html .= '' . mb_icon('link', 'h-3.5 w-3.5') . '<span class="hidden sm:inline">' . $label . '</span></button>';
            } else {
                $html .= '<a class="mb-share-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 text-zinc-500 transition hover:bg-zinc-50 hover:text-zinc-900 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" href="' . htmlspecialchars($a['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer" title="' . $label . '" aria-label="' . $label . '">';
                $html .= mb_brand_icon($a['icon'], 'mb-brand-icon h-3.5 w-3.5');
                $html .= '</a>';
            }
        }
        $html .= '</div>';
        return $html;
    }
}

/**
 * Theme asset URL with filemtime cache-bust so CSS/JS updates always apply.
 *
 * @param string $relative Path relative to theme root, e.g. css/style.css
 * @return string
 */
if (!function_exists('mb_asset')) {
    function mb_asset($relative)
    {
        $relative = ltrim(str_replace('\\', '/', (string)$relative), '/');
        $file = __DIR__ . '/' . $relative;
        $v = is_file($file) ? (string)filemtime($file) : (string)time();
        return theme_path() . $relative . '?v=' . rawurlencode($v);
    }
}

/**
 * Initials for comment avatar (1–2 chars, Unicode-safe).
 */
if (!function_exists('mb_comment_initials')) {
    function mb_comment_initials($name)
    {
        $name = trim((string)$name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($parts) || empty($parts)) {
            return '?';
        }
        $first = $parts[0];
        $second = (count($parts) > 1) ? $parts[count($parts) - 1] : '';
        $a = function_exists('mb_substr') ? mb_substr($first, 0, 1, 'UTF-8') : substr($first, 0, 1);
        $b = $second !== ''
            ? (function_exists('mb_substr') ? mb_substr($second, 0, 1, 'UTF-8') : substr($second, 0, 1))
            : '';
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($a . $b, 'UTF-8');
        }
        return strtoupper($a . $b);
    }
}

/**
 * Deterministic avatar tone class from author name.
 */
if (!function_exists('mb_comment_avatar_tone')) {
    function mb_comment_avatar_tone($name)
    {
        $tones = array('tone-1', 'tone-2', 'tone-3', 'tone-4', 'tone-5', 'tone-6');
        $hash = sprintf('%u', crc32((string)$name));
        return $tones[(int)$hash % count($tones)];
    }
}

/**
 * Relative-friendly comment date (Jalali-aware via mb_date).
 */
if (!function_exists('mb_comment_date')) {
    function mb_comment_date($timestamp)
    {
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        if (empty($timestamp)) {
            return '';
        }
        return mb_date($timestamp, 'long');
    }
}

/**
 * Comment form markup (main composer or reply). Compatible with POST /comments/submit.
 *
 * @param string $urlpath Post URL path (no leading slash preferred)
 * @param string|null $parentId Parent comment id for replies
 * @return string
 */
if (!function_exists('mb_comment_form')) {
    function mb_comment_form($urlpath, $parentId = null)
    {
        $isReply = !empty($parentId);
        $formId = $isReply ? 'mb-reply-form-' . preg_replace('/[^a-zA-Z0-9_\-.]/', '', (string)$parentId) : 'mb-comment-form';
        $submitUrl = site_url() . 'comments/submit';
        $fa = (mb_lang_code() === 'fa');

        $nameLabel = i18n('Name');
        $emailLabel = i18n('Email');
        $commentLabel = i18n('Comment');
        $emailHint = i18n('Email_not_published');
        $formatHint = i18n('Comment_formatting_help');
        $notifyLabel = i18n('Notify_new_comments');
        $submitLabel = $isReply ? i18n('Post_Reply') : i18n('Post_Comment');
        $cancelLabel = i18n('Cancel');
        $placeholder = $fa ? 'دیدگاه خود را بنویسید…' : 'Write your comment…';

        ob_start();
        ?>
<form id="<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>"
      class="mb-cform<?php echo $isReply ? ' mb-cform--reply' : ''; ?>"
      method="POST"
      action="<?php echo htmlspecialchars($submitUrl, ENT_QUOTES, 'UTF-8'); ?>"
      novalidate>
    <input type="hidden" name="url" value="<?php echo htmlspecialchars($urlpath, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($isReply): ?>
    <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars((string)$parentId, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <div class="mb-cform__honeypot" aria-hidden="true">
        <label><?php echo $fa ? 'وب‌سایت' : 'Website'; ?>
            <input type="text" name="website" tabindex="-1" value="" autocomplete="off">
        </label>
        <label><?php echo $fa ? 'شرکت' : 'Company'; ?>
            <input type="text" name="company" tabindex="-1" value="" autocomplete="off" data-mb-antispam>
        </label>
    </div>

    <div class="mb-cform__field mb-cform__field--comment">
        <label class="mb-cform__label" for="comment-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($commentLabel, ENT_QUOTES, 'UTF-8'); ?>
            <span class="mb-cform__req" aria-hidden="true">*</span>
        </label>
        <textarea class="mb-cform__input mb-cform__textarea"
                  id="comment-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>"
                  name="comment"
                  rows="4"
                  required
                  placeholder="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"></textarea>
        <p class="mb-cform__hint"><?php echo htmlspecialchars($formatHint, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <div class="mb-cform__row">
        <div class="mb-cform__field">
            <label class="mb-cform__label" for="name-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($nameLabel, ENT_QUOTES, 'UTF-8'); ?>
                <span class="mb-cform__req" aria-hidden="true">*</span>
            </label>
            <input class="mb-cform__input"
                   type="text"
                   id="name-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>"
                   name="name"
                   required
                   autocomplete="name"
                   minlength="2">
        </div>
        <div class="mb-cform__field">
            <label class="mb-cform__label" for="email-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($emailLabel, ENT_QUOTES, 'UTF-8'); ?>
                <span class="mb-cform__req" aria-hidden="true">*</span>
            </label>
            <input class="mb-cform__input"
                   type="email"
                   id="email-<?php echo htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'); ?>"
                   name="email"
                   required
                   autocomplete="email">
            <p class="mb-cform__hint"><?php echo htmlspecialchars($emailHint, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <div class="mb-cform__footer">
        <label class="mb-cform__check">
            <input type="checkbox" name="notify" value="1">
            <span><?php echo htmlspecialchars($notifyLabel, ENT_QUOTES, 'UTF-8'); ?></span>
        </label>
        <div class="mb-cform__actions">
            <?php if ($isReply): ?>
            <button type="button" class="mb-cform__btn mb-cform__btn--ghost" data-mb-cancel-reply>
                <?php echo htmlspecialchars($cancelLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <?php endif; ?>
            <button type="submit" class="mb-cform__btn mb-cform__btn--primary">
                <?php echo mb_icon($isReply ? 'corner-down-left' : 'send', 'h-4 w-4'); ?>
                <span><?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </button>
        </div>
    </div>
</form>
        <?php
        return ob_get_clean();
    }
}

/**
 * Render one comment (and nested children) as list item HTML.
 *
 * @param array $comment Comment node from buildCommentTree()
 * @param string $urlpath Post URL path
 * @return string
 */
if (!function_exists('mb_render_comment')) {
    function mb_render_comment($comment, $urlpath)
    {
        if (empty($comment) || !is_array($comment)) {
            return '';
        }
        $id = isset($comment['id']) ? (string)$comment['id'] : '';
        $name = isset($comment['name']) ? (string)$comment['name'] : '';
        $ts = isset($comment['timestamp']) ? $comment['timestamp'] : 0;
        $level = isset($comment['level']) ? (int)$comment['level'] : 0;
        $body = isset($comment['comment']) ? formatCommentText($comment['comment']) : '';
        $initials = mb_comment_initials($name);
        $tone = mb_comment_avatar_tone($name);
        $date = mb_comment_date($ts);
        $iso = $ts ? date('c', (int)$ts) : '';
        $replyLabel = i18n('Reply');
        $fa = (mb_lang_code() === 'fa');
        $replyTo = $fa
            ? ('پاسخ به ' . $name)
            : ('Reply to ' . $name);

        ob_start();
        ?>
<li class="mb-comment" id="comment-<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" data-level="<?php echo $level; ?>">
    <article class="mb-comment__card">
        <div class="mb-comment__avatar mb-comment__avatar--<?php echo htmlspecialchars($tone, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true">
            <?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <div class="mb-comment__main">
            <header class="mb-comment__meta">
                <span class="mb-comment__author"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if ($date !== ''): ?>
                <time class="mb-comment__date" datetime="<?php echo htmlspecialchars($iso, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></time>
                <?php endif; ?>
            </header>
            <div class="mb-comment__body"><?php echo $body; ?></div>
            <footer class="mb-comment__footer">
                <button type="button"
                        class="mb-comment__reply"
                        data-mb-reply="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"
                        aria-expanded="false"
                        aria-controls="mb-reply-slot-<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo mb_icon('reply', 'h-3.5 w-3.5'); ?>
                    <span><?php echo htmlspecialchars($replyLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </button>
            </footer>
            <div class="mb-comment__reply-slot"
                 id="mb-reply-slot-<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"
                 hidden
                 data-reply-title="<?php echo htmlspecialchars($replyTo, ENT_QUOTES, 'UTF-8'); ?>"></div>
        </div>
    </article>
    <?php if (!empty($comment['children']) && is_array($comment['children'])): ?>
    <ol class="mb-comments__list mb-comments__list--nested">
        <?php foreach ($comment['children'] as $child): ?>
            <?php echo mb_render_comment($child, $urlpath); ?>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>
</li>
        <?php
        return ob_get_clean();
    }
}

/**
 * Render full local comments section for a post/page URL.
 *
 * @param string $url Full post URL or path
 * @return void
 */
if (!function_exists('mb_comments_section')) {
    function mb_comments_section($url)
    {
        if (!function_exists('local') || !local()) {
            return;
        }

        $urlpath = ltrim((string)parse_url($url, PHP_URL_PATH), '/');
        if ($urlpath === '' && is_string($url)) {
            $urlpath = ltrim($url, '/');
        }

        $comments = function_exists('getComments') ? getComments($urlpath, null) : array();
        if (!is_array($comments)) {
            $comments = array();
        }
        $tree = function_exists('buildCommentTree') ? buildCommentTree($comments) : array();
        $count = count($comments);

        include __DIR__ . '/partials/comments.html.php';
    }
}
