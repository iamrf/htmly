<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="animate-fade-up">
    <header class="mb-8 border-b border-line pb-6 sm:mb-10 sm:pb-8">
        <div class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-mute sm:mb-4">
            <time datetime="<?php echo date('c', $p->date); ?>"><?php echo format_date($p->date); ?></time>
            <?php if (!empty($p->category)): ?>
            <span class="text-line">·</span>
            <span class="text-sky"><?php echo $p->category; ?></span>
            <?php endif; ?>
            <?php if (authorized($p)): ?>
            <span class="text-line">·</span>
            <a class="text-sky hover:text-sky-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a>
            <?php endif; ?>
        </div>

        <?php if (!empty($p->link)): ?>
        <h1 class="text-2xl font-extrabold leading-tight tracking-tight text-ink xs:text-3xl sm:text-4xl md:text-5xl">
            <a href="<?php echo $p->link; ?>" target="_blank" rel="noopener"><?php echo $p->title; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="ml-1 inline-block align-middle opacity-40" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>
            </a>
        </h1>
        <?php else: ?>
        <h1 class="text-2xl font-extrabold leading-tight tracking-tight text-ink xs:text-3xl sm:text-4xl md:text-5xl"><?php echo $p->title; ?></h1>
        <?php endif; ?>
    </header>

    <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
        <div class="min-w-0 lg:col-span-8">
            <div class="prose-blogen max-w-none">
                <?php echo blogen_post_media($p, false); ?>
                <?php echo $p->body; ?>
            </div>

            <?php if (theme_config('post_tag') && !empty($p->tag)): ?>
            <div class="mt-8 border-t border-line pt-5 sm:mt-10 sm:pt-6">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('tags'); ?></h2>
                <div class="tags flex flex-wrap gap-2"><?php echo $p->tag; ?></div>
            </div>
            <?php endif; ?>

            <div class="related-posts prose-blogen mt-8 border-t border-line pt-6 sm:mt-10 sm:pt-8">
                <h2 class="mb-4 text-lg font-bold text-ink sm:text-xl"><?php echo i18n('related_posts'); ?></h2>
                <?php echo get_related($p->related); ?>
            </div>

            <?php if (facebook() || disqus()): ?>
            <div class="mt-8 border-t border-line pt-6 sm:mt-10 sm:pt-8" id="comment">
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

        <aside class="space-y-5 sm:space-y-6 lg:col-span-4">
            <div class="rounded-2xl bg-paper p-4 ring-1 ring-line sm:p-5">
                <div class="flex items-center gap-3">
                    <img class="h-11 w-11 rounded-full object-cover ring-2 ring-sky-faint sm:h-12 sm:w-12" src="<?php echo $p->authorAvatar; ?>" width="48" height="48" alt="<?php echo $p->authorName; ?>">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-ink"><?php echo $p->authorName; ?></p>
                        <a class="text-sm text-sky hover:text-sky-deep" href="<?php echo $p->authorUrl; ?>"><?php echo i18n('post_by_author'); ?></a>
                    </div>
                </div>
            </div>

            <?php if (theme_config('post_navigation')): ?>
            <div class="space-y-3 sm:space-y-4">
                <?php if (!empty($prev)): ?>
                <div class="rounded-2xl bg-paper p-4 ring-1 ring-line transition hover:ring-sky/30 sm:p-5">
                    <p class="mb-1 text-xs font-medium text-mute"><?php echo i18n('prev_post'); ?></p>
                    <a class="font-semibold text-ink hover:text-sky" href="<?php echo $prev['url']; ?>"><?php echo $prev['title']; ?></a>
                </div>
                <?php endif; ?>
                <?php if (!empty($next)): ?>
                <div class="rounded-2xl bg-paper p-4 ring-1 ring-line transition hover:ring-sky/30 sm:p-5">
                    <p class="mb-1 text-xs font-medium text-mute"><?php echo i18n('next_post'); ?></p>
                    <a class="font-semibold text-ink hover:text-sky" href="<?php echo $next['url']; ?>"><?php echo $next['title']; ?></a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('recent_posts')): ?>
            <div class="recent-posts rounded-2xl bg-paper p-4 ring-1 ring-line sm:p-5">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('recent_posts'); ?></h2>
                <?php echo recent_posts(); ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('popular_posts')): ?>
            <div class="recent-posts rounded-2xl bg-paper p-4 ring-1 ring-line sm:p-5">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('popular_posts'); ?></h2>
                <?php echo popular_posts(); ?>
            </div>
            <?php endif; ?>

            <?php if (theme_config('tagcloud')): ?>
            <div class="rounded-2xl bg-paper p-4 ring-1 ring-line sm:p-5">
                <h2 class="mb-3 text-sm font-semibold text-mute"><?php echo i18n('tags'); ?></h2>
                <div class="tags flex flex-wrap gap-2"><?php echo tag_cloud(); ?></div>
            </div>
            <?php endif; ?>

            <?php if (config('blog.enable') === 'true'): ?>
            <a class="inline-flex items-center gap-2 text-sm font-semibold text-sky hover:text-sky-deep" href="<?php echo site_url() . blog_path(); ?>">
                ← <?php echo i18n('back_to'); ?> <?php echo blog_string(); ?>
            </a>
            <?php else: ?>
            <a class="inline-flex items-center gap-2 text-sm font-semibold text-sky hover:text-sky-deep" href="<?php echo site_url(); ?>">
                ← <?php echo i18n('back_to'); ?> <?php echo i18n('homepage'); ?>
            </a>
            <?php endif; ?>
        </aside>
    </div>
</article>
