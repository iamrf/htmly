<?php if (!defined('HTMLY')) die('HTMLy');

/**
 * Render post media (image / video / audio / quote) for list or single views.
 */
if (!function_exists('blogen_post_media')) {
    function blogen_post_media($p, $teaser = false)
    {
        $html = '';
        $title = htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8');
        if (!empty($p->image)) {
            $html = '<a class="block overflow-hidden rounded-2xl" href="' . $p->url . '"><img class="aspect-[16/10] w-full object-cover transition duration-500 hover:scale-[1.02]" src="' . $p->image . '" alt="' . $title . '" loading="lazy"></a>';
        } elseif (!empty($p->video)) {
            if ($teaser) {
                $html = '<a class="thumbnail relative block overflow-hidden rounded-2xl" href="' . $p->url . '"><img class="aspect-[16/10] w-full object-cover" src="//img.youtube.com/vi/' . get_video_id($p->video) . '/sddefault.jpg" alt="' . $title . '" loading="lazy"><span class="thumb-icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z"/></svg></span></a>';
            } else {
                $html = '<div class="relative overflow-hidden rounded-2xl" style="padding-top:56.25%"><iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/' . get_video_id($p->video) . '" frameborder="0" allowfullscreen></iframe></div>';
            }
        } elseif (!empty($p->audio)) {
            if ($teaser) {
                $html = '<a class="thumbnail relative block overflow-hidden rounded-2xl" href="' . $p->url . '"><img class="aspect-[16/10] w-full object-cover" src="' . theme_path() . 'img/soundcloud.jpg" alt="' . $title . '" loading="lazy"><span class="thumb-icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/><path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/><path d="M10.025 8a4.5 4.5 0 0 1-1.318 3.182L8 10.475A3.5 3.5 0 0 0 9.025 8c0-.966-.392-1.841-1.025-2.475l.707-.707A4.5 4.5 0 0 1 10.025 8M7 4a.5.5 0 0 0-.812-.39L3.825 5.5H1.5A.5.5 0 0 0 1 6v4a.5.5 0 0 0 .5.5h2.325l2.363 1.89A.5.5 0 0 0 7 12zM4.312 6.39 6 5.04v5.92L4.312 9.61A.5.5 0 0 0 4 9.5H2v-3h2a.5.5 0 0 0 .312-.11"/></svg></span></a>';
            } else {
                $html = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . $p->audio . '&amp;auto_play=false&amp;visual=true"></iframe>';
            }
        } elseif (!empty($p->quote)) {
            $html = '<blockquote class="blogen-quote text-xl font-medium leading-relaxed text-ink md:text-2xl">' . $p->quote . '</blockquote>';
        }
        return $html;
    }
}
