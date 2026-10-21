<?php if (!defined('HTMLY')) die('HTMLy');

/**
 * Modern Bilingual theme helpers.
 * Prefix: mb_ (modern-bilingual) to avoid collisions with core / other themes.
 */

/**
 * Theme config with default fallback.
 */
if (!function_exists('mb_config')) {
    function mb_config($key, $default = null)
    {
        $val = theme_config($key);
        if ($val === null || $val === '') {
            return $default;
        }
        return $val;
    }
}

/**
 * Checkbox helpers (HTMLy stores "1" / "0").
 */
if (!function_exists('mb_enabled')) {
    function mb_enabled($key, $default = false)
    {
        $val = theme_config($key);
        if ($val === null || $val === '') {
            return (bool)$default;
        }
        return ($val === '1' || $val === 1 || $val === true || $val === 'true');
    }
}

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
                $html = '<a class="mb-media relative block overflow-hidden rounded-xl" href="' . $url . '"><img class="aspect-[16/10] w-full object-cover" src="//img.youtube.com/vi/' . $vid . '/sddefault.jpg" alt="' . $title . '" loading="lazy"><span class="absolute inset-0 flex items-center justify-center bg-black/30"><i data-lucide="play" class="h-10 w-10 text-white"></i></span></a>';
            } else {
                $html = '<div class="mb-media relative overflow-hidden rounded-2xl" style="padding-top:56.25%"><iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/' . $vid . '" frameborder="0" allowfullscreen loading="lazy"></iframe></div>';
            }
        } elseif (!empty($p->audio)) {
            if ($teaser) {
                $cover = theme_path() . 'img/audio-cover.svg';
                $html = '<a class="mb-media relative block overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800" href="' . $url . '"><div class="flex aspect-[16/10] items-center justify-center"><i data-lucide="music" class="h-12 w-12 text-zinc-400"></i></div></a>';
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
                        <a class="transition hover:text-zinc-600 dark:hover:text-zinc-300" href="<?php echo $url; ?>"<?php echo $target; ?>><?php echo $title; ?><?php if ($external): ?> <i data-lucide="external-link" class="inline h-4 w-4 opacity-50"></i><?php endif; ?></a>
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
                        <a class="transition hover:text-zinc-600 dark:hover:text-zinc-300" href="<?php echo $url; ?>"<?php echo $target; ?>><?php echo $title; ?><?php if ($external): ?> <i data-lucide="external-link" class="inline h-4 w-4 opacity-50"></i><?php endif; ?></a>
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
