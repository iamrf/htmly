<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Static frontpage content (when config uses a static front page).
 */
include __DIR__ . '/static.html.php';

if (mb_enabled('frontpage_posts', false) || theme_config('frontpage_posts')) {
    $recent = recent_posts(true);
    if (!empty($recent) && is_array($recent)) {
        echo '<section class="mt-16 border-t border-zinc-200 pt-12 dark:border-zinc-800">';
        echo '<h2 class="mb-6 text-xl font-bold tracking-tight">' . i18n('Recent_posts') . '</h2>';
        echo '<div class="grid gap-6 sm:grid-cols-2">';
        foreach ($recent as $p) {
            echo mb_render_card($p, 'grid');
        }
        echo '</div></section>';
    }
}
