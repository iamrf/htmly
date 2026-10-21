<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<?php if (mb_enabled('author_bio', true)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('About'); ?></h2>
    <div class="prose prose-sm prose-zinc max-w-none dark:prose-invert">
        <?php echo blog_description(); ?>
    </div>
    <div class="social-links mt-4">
        <?php echo mb_render_social('sidebar-social', 'sm'); ?>
    </div>
</section>
<?php endif; ?>

<?php if (mb_enabled('recent_posts', true)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-4 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Recent_posts'); ?></h2>
    <ul class="space-y-4">
        <?php $recent = recent_posts(true); ?>
        <?php if (!empty($recent) && is_array($recent)): ?>
            <?php foreach ($recent as $rc): ?>
                <?php $thumb = mb_post_image($rc); ?>
                <li class="flex gap-3">
                    <?php if ($thumb): ?>
                    <a class="h-14 w-14 shrink-0 overflow-hidden rounded-lg" href="<?php echo $rc->url; ?>">
                        <img class="h-full w-full object-cover" src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy">
                    </a>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <a class="line-clamp-2 text-sm font-medium text-zinc-900 transition hover:text-zinc-600 dark:text-zinc-100 dark:hover:text-zinc-300" href="<?php echo $rc->url; ?>"><?php echo $rc->title; ?></a>
                        <time class="mt-0.5 block text-xs text-zinc-400"><?php echo mb_date($rc->date, 'short'); ?></time>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</section>
<?php endif; ?>

<?php if (mb_enabled('popular_posts', false)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-4 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Popular_posts'); ?></h2>
    <ul class="space-y-4">
        <?php $popular = popular_posts(true); ?>
        <?php if (!empty($popular) && is_array($popular)): ?>
            <?php foreach ($popular as $pp): ?>
                <?php $thumb = mb_post_image($pp); ?>
                <li class="flex gap-3">
                    <?php if ($thumb): ?>
                    <a class="h-14 w-14 shrink-0 overflow-hidden rounded-lg" href="<?php echo $pp->url; ?>">
                        <img class="h-full w-full object-cover" src="<?php echo htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy">
                    </a>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <a class="line-clamp-2 text-sm font-medium text-zinc-900 transition hover:text-zinc-600 dark:text-zinc-100 dark:hover:text-zinc-300" href="<?php echo $pp->url; ?>"><?php echo $pp->title; ?></a>
                        <time class="mt-0.5 block text-xs text-zinc-400"><?php echo mb_date($pp->date, 'short'); ?></time>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</section>
<?php endif; ?>

<?php if (mb_enabled('category_list', true)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Category'); ?></h2>
    <div class="category-widget text-sm">
        <?php echo category_list(); ?>
    </div>
</section>
<?php endif; ?>

<?php if (mb_enabled('tagcloud', true)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Tags'); ?></h2>
    <div class="tagcloud flex flex-wrap gap-2 text-sm">
        <?php echo tag_cloud(); ?>
    </div>
</section>
<?php endif; ?>

<?php if (mb_enabled('archive', false)): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Archives'); ?></h2>
    <div class="archive-widget text-sm">
        <?php echo archive_list(); ?>
    </div>
</section>
<?php endif; ?>

<?php if (mb_enabled('newsletter_widget', false)): ?>
<section class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-zinc-900 to-zinc-700 p-5 text-white dark:from-zinc-100 dark:to-zinc-300 dark:text-zinc-900">
    <h2 class="mb-2 text-sm font-semibold"><?php echo mb_lang_code() === 'fa' ? 'خبرنامه' : 'Newsletter'; ?></h2>
    <div class="newsletter-promo text-sm opacity-90">
        <?php
        $promo = mb_config('newsletter_html', '');
        if ($promo !== '') {
            echo $promo;
        } else {
            echo '<p>' . (mb_lang_code() === 'fa' ? 'برای دریافت نوشته‌های جدید عضو شوید.' : 'Subscribe for new posts.') . '</p>';
        }
        ?>
    </div>
</section>
<?php endif; ?>

<?php if (disqus()): ?>
<section class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Comments'); ?></h2>
    <?php echo recent_comments(); ?>
</section>
<?php endif; ?>
