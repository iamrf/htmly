<?php if (!defined('HTMLY')) die('HTMLy');

/**
 * Convert Gregorian date to Jalali (Persian).
 * Returns array($year, $month, $day).
 */
if (!function_exists('blogfa_gregorian_to_jalali')) {
    function blogfa_gregorian_to_jalali($gy, $gm, $gd)
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
 * Convert Latin digits to Persian digits.
 */
if (!function_exists('blogfa_persian_digits')) {
    function blogfa_persian_digits($str)
    {
        $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        return str_replace($en, $fa, (string)$str);
    }
}

/**
 * Persian month names (Jalali).
 */
if (!function_exists('blogfa_month_name')) {
    function blogfa_month_name($month)
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
 * Format a Unix timestamp as a Jalali Persian date.
 *
 * Formats:
 *   'long'  => ۲۱ مهر ۱۴۰۳
 *   'full'  => دوشنبه ۲۱ مهر ۱۴۰۳
 *   'short' => ۱۴۰۳/۰۷/۲۱
 *   'month' => مهر ۱۴۰۳
 *
 * @param int|string $date Unix timestamp
 * @param string $format long|full|short|month
 * @return string
 */
if (!function_exists('jalali_date')) {
    function jalali_date($date, $format = 'long')
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        if (empty($date)) {
            return '';
        }

        $gy = (int)date('Y', $date);
        $gm = (int)date('n', $date);
        $gd = (int)date('j', $date);
        list($jy, $jm, $jd) = blogfa_gregorian_to_jalali($gy, $gm, $gd);

        $weekdays = array('یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه');
        $weekday = $weekdays[(int)date('w', $date)];

        switch ($format) {
            case 'short':
                $out = sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
                break;
            case 'month':
                $out = blogfa_month_name($jm) . ' ' . $jy;
                break;
            case 'full':
                $out = $weekday . ' ' . $jd . ' ' . blogfa_month_name($jm) . ' ' . $jy;
                break;
            case 'long':
            default:
                $out = $jd . ' ' . blogfa_month_name($jm) . ' ' . $jy;
                break;
        }

        return blogfa_persian_digits($out);
    }
}

/**
 * Render post media (image / video / audio / quote) for list or single views.
 */
if (!function_exists('blogfa_post_media')) {
    function blogfa_post_media($p, $teaser = false)
    {
        $html = '';
        $title = htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8');
        if (!empty($p->image)) {
            $html = '<a class="block overflow-hidden rounded-xl" href="' . $p->url . '"><img class="w-full object-cover aspect-[16/10] transition duration-500 hover:scale-[1.02]" src="' . $p->image . '" alt="' . $title . '"></a>';
        } elseif (!empty($p->video)) {
            if ($teaser) {
                $html = '<a class="thumbnail relative block overflow-hidden rounded-xl" href="' . $p->url . '"><img class="w-full object-cover aspect-[16/10]" src="//img.youtube.com/vi/' . get_video_id($p->video) . '/sddefault.jpg" alt="' . $title . '"><span class="thumb-icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z"/></svg></span></a>';
            } else {
                $html = '<div class="relative overflow-hidden rounded-xl" style="padding-top:56.25%"><iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/' . get_video_id($p->video) . '" frameborder="0" allowfullscreen></iframe></div>';
            }
        } elseif (!empty($p->audio)) {
            if ($teaser) {
                $html = '<a class="thumbnail relative block overflow-hidden rounded-xl" href="' . $p->url . '"><img class="w-full object-cover aspect-[16/10]" src="' . theme_path() . 'img/soundcloud.jpg" alt="' . $title . '"><span class="thumb-icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/><path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/><path d="M10.025 8a4.5 4.5 0 0 1-1.318 3.182L8 10.475A3.5 3.5 0 0 0 9.025 8c0-.966-.392-1.841-1.025-2.475l.707-.707A4.5 4.5 0 0 1 10.025 8M7 4a.5.5 0 0 0-.812-.39L3.825 5.5H1.5A.5.5 0 0 0 1 6v4a.5.5 0 0 0 .5.5h2.325l2.363 1.89A.5.5 0 0 0 7 12zM4.312 6.39 6 5.04v5.92L4.312 9.61A.5.5 0 0 0 4 9.5H2v-3h2a.5.5 0 0 0 .312-.11"/></svg></span></a>';
            } else {
                $html = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . $p->audio . '&amp;auto_play=false&amp;visual=true"></iframe>';
            }
        } elseif (!empty($p->quote)) {
            $html = '<blockquote class="blogfa-quote text-xl font-medium leading-relaxed text-ink/80 md:text-2xl">' . $p->quote . '</blockquote>';
        }
        return $html;
    }
}
