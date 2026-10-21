<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="animate-fade-up">
    <header class="mb-10 border-b border-sand pb-8 text-center sm:text-right">
        <div class="mb-4 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm text-mute sm:justify-start">
            <time datetime="<?php echo date('c', $p->date); ?>"><?php echo jalali_date($p->date, 'full'); ?></time>
            <?php if (!empty($p->category)): ?>
            <span class="text-sand">|</span>
            <span class="text-teal"><?php echo $p->category; ?></span>
            <?php endif; ?>
            <?php if (authorized($p)): ?>
            <span class="text-sand">|</span>
            <a class="text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a>
            <?php endif; ?>
        </div>

        <?php if (!empty($p->link)): ?>
        <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-ink sm:text-4xl md:text-5xl">
            <a href="<?php echo $p->link; ?>" target="_blank" rel="noopener"><?php echo $p->title; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="mr-1 inline-block align-middle opacity-40" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>
            </a>
        </h1>
        <?php else: ?>
        <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-ink sm:text-4xl md:text-5xl"><?php echo $p->title; ?></h1>
        <?php endif; ?>
    </header>

    <div class="grid gap-10 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <div class="prose-blogfa max-w-none">
                <?php echo blogfa_post_media($p, false); ?>
                <?php echo $p->body; ?>
            </div>

            <?php if (theme_config('post_tag') && !empty($p->tag)): ?>
            <div class="mt-10 border-t border-sand pt-6">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('tags'); ?></h2>
                <div class="tags flex flex-wrap gap-2"><?php echo $p->tag; ?></div>
            </div>
            <?php endif; ?>

            <div class="related-posts prose-blogfa mt-10 border-t border-sand pt-8">
                <h2 class="mb-4 text-xl font-bold text-ink"><?php echo i18n('related_posts'); ?></h2>
                <?php echo get_related($p->related); ?>
            </div>

            <?php if (facebook() || disqus()): ?>
            <div class="mt-10 border-t border-sand pt-8" id="comment">
                <?php if (facebook()): ?>
                    <div class="fb-comments" data-href="<?php echo $p->url; ?>" data-numposts="<?php echo config('fb.num'); ?>" data-colorscheme="<?php echo config('fb.color'); ?>"></div>
                <?php endif; ?>
                <?php if (disqus()): ?>
                    <?php echo disqus($p->title, $p->url); ?>
                    <div id="disqus_thread"></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <aside class="space-y-8 lg:col-span-4">
            <div class="rounded-2xl bg-paper p-5 ring-1 ring-sand">
                <div class="flex items-center gap-3">
                    <img class="h-12 w-12 rounded-full object-cover ring-2 ring-teal-faint" src="<?php echo $p->authorAvatar; ?>" width="48" height="48" alt="<?php echo $p->authorName; ?>">
                    <div>
                        <p class="font-semibold text-ink"><?php echo $p->authorName; ?></p>
                        <a class="text-sm text-teal hover:text-teal-deep" href="<?php echo $p->authorUrl; ?>"><?php echo i18n('post_by_author'); ?></a>
                    </div>
                </div>
            </div>

            <?php if (theme_config('post_navigation')): ?>
            <div class="space-y-4">
                <?php if (!empty($next)): ?>
                <div class="rounded-2xl bg-paper p-5 ring-1 ring-sand transition hover:ring-teal/30">
                    <p class="mb-1 text-xs font-medium text-mute"><?php echo i18n('next_post'); ?></p>
                    <a class="font-semibold text-ink hover:text-teal" href="<?php echo $next['url']; ?>"><?php echo $next['title']; ?></a>
                </div>
                <?php endif; ?>
                <?php if (!empty($prev)): ?>
                <div class="rounded-2xl bg-paper p-5 ring-1 ring-sand transition hover:ring-teal/30">
                    <p class="mb-1 text-xs font-medium text-mute"><?php echo i18n('prev_post'); ?></p>
                    <a class="font-semibold text-ink hover:text-teal" href="<?php echo $prev['url']; ?>"><?php echo $prev['title']; ?></a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('recent_posts')): ?>
            <div class="recent-posts rounded-2xl bg-paper p-5 ring-1 ring-sand">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('recent_posts'); ?></h2>
                <?php echo recent_posts(); ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('popular_posts')): ?>
            <div class="recent-posts rounded-2xl bg-paper p-5 ring-1 ring-sand">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('popular_posts'); ?></h2>
                <?php echo popular_posts(); ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('tagcloud')): ?>
            <div class="rounded-2xl bg-paper p-5 ring-1 ring-sand">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('tags'); ?></h2>
                <div class="tags flex flex-wrap gap-2"><?php echo tag_cloud(); ?></div>
            </div>
            <?php endif; ?>

            <?php if (config('blog.enable') === 'true'): ?>
            <a class="inline-flex items-center gap-2 text-sm font-semibold text-teal hover:text-teal-deep" href="<?php echo site_url() . blog_path(); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                <?php echo i18n('back_to'); ?> <?php echo blog_string(); ?>
            </a>
            <?php else: ?>
            <a class="inline-flex items-center gap-2 text-sm font-semibold text-teal hover:text-teal-deep" href="<?php echo site_url(); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                <?php echo i18n('back_to'); ?> <?php echo i18n('homepage'); ?>
            </a>
            <?php endif; ?>
        </aside>
    </div>
</article>
