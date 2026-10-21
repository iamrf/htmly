<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (isset($is_taxonomy)): ?>
<header class="mb-8 animate-fade-up sm:mb-10">
    <h1 class="text-2xl font-extrabold tracking-tight text-ink xs:text-3xl sm:text-4xl">
        <?php echo $taxonomy->title; ?>
        <a class="ml-2 inline-flex align-middle text-sky hover:text-sky-deep" aria-label="RSS" title="RSS" href="<?php echo $taxonomy->rss; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-3-8.5a1 1 0 0 1 1-1c5.523 0 10 4.477 10 10a1 1 0 1 1-2 0 8 8 0 0 0-8-8 1 1 0 0 1-1-1m0 4a1 1 0 0 1 1-1 6 6 0 0 1 6 6 1 1 0 1 1-2 0 4 4 0 0 0-4-4 1 1 0 0 1-1-1"/></svg>
        </a>
    </h1>
    <?php if (!empty($taxonomy->body)): ?>
    <div class="prose-blogen mt-3 max-w-prose text-mute leading-7">
        <?php echo $taxonomy->body; ?>
    </div>
    <?php endif; ?>
    <?php if (isset($is_blog)): ?>
    <div class="category mt-5 flex flex-wrap gap-2 sm:mt-6">
        <?php $categories = category_list(true); ?>
        <?php foreach ($categories as $k => $v): ?>
            <?php if ($v['2'] !== 0): ?>
            <a class="rounded-lg bg-sky px-3 py-1.5 text-sm font-medium text-white transition hover:bg-sky-deep" href="<?php echo site_url() . 'category/' . $v[0]; ?>"><?php echo $v[1]; ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</header>
<?php elseif (isset($is_front)): ?>
<header class="mb-10 animate-fade-up sm:mb-12">
    <p class="mb-2 text-sm font-medium text-sky">Latest posts</p>
    <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl md:text-5xl"><?php echo blog_title(); ?></h1>
    <?php if (blog_description()): ?>
    <p class="mt-3 max-w-prose text-base leading-7 text-mute sm:text-lg sm:leading-8"><?php echo blog_description(); ?></p>
    <?php endif; ?>
</header>
<?php endif; ?>

<?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
<div class="divide-y divide-line">
    <?php foreach ($posts as $p): ?>
    <?php $img = get_image($p->body); ?>
    <article class="group py-8 animate-fade-up first:pt-0 sm:py-10">
        <div class="grid gap-5 sm:gap-6 md:grid-cols-12 md:items-start">
            <div class="md:col-span-3">
                <time class="inline-flex items-center gap-2 text-sm font-medium text-mute" datetime="<?php echo date('c', $p->date); ?>">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-sky"></span>
                    <?php echo format_date($p->date); ?>
                </time>
                <?php if (authorized($p)): ?>
                <div class="mt-2 text-sm"><a class="text-sky hover:text-sky-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></div>
                <?php endif; ?>

                <?php if ($teaserType === 'trimmed'): ?>
                <div class="mt-4 hidden md:block">
                    <?php if (!empty($p->image) || !empty($p->video) || !empty($p->audio)): ?>
                        <?php echo blogen_post_media($p, true); ?>
                    <?php elseif (!empty($img)): ?>
                        <a class="block overflow-hidden rounded-2xl" href="<?php echo $p->url; ?>"><img class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-[1.02]" src="<?php echo $img; ?>" alt="<?php echo $p->title; ?>" loading="lazy"></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="md:col-span-9">
                <?php if (!empty($p->link)): ?>
                <h2 class="text-xl font-bold leading-snug tracking-tight xs:text-2xl sm:text-3xl">
                    <a class="text-ink transition hover:text-sky" href="<?php echo $p->link; ?>" target="_blank" rel="noopener"><?php echo $p->title; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="ml-1 inline-block align-middle opacity-50" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>
                    </a>
                </h2>
                <?php else: ?>
                <h2 class="text-xl font-bold leading-snug tracking-tight xs:text-2xl sm:text-3xl">
                    <a class="text-ink transition hover:text-sky" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a>
                </h2>
                <?php endif; ?>

                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-mute">
                    <?php if (!empty($p->category)): ?>
                    <span class="text-sky"><?php echo $p->category; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($p->tag)): ?>
                    <span class="tags"><?php echo $p->tag; ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($teaserType === 'trimmed' && (!empty($p->image) || !empty($p->video) || !empty($p->audio) || !empty($img))): ?>
                <div class="mt-4 md:hidden">
                    <?php if (!empty($p->image) || !empty($p->video) || !empty($p->audio)): ?>
                        <?php echo blogen_post_media($p, true); ?>
                    <?php elseif (!empty($img)): ?>
                        <a class="block overflow-hidden rounded-2xl" href="<?php echo $p->url; ?>"><img class="aspect-[16/10] w-full object-cover" src="<?php echo $img; ?>" alt="<?php echo $p->title; ?>" loading="lazy"></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="prose-blogen mt-4 max-w-none text-mute leading-7 sm:leading-8">
                    <?php if ($teaserType !== 'trimmed'): ?>
                        <?php echo blogen_post_media($p, false); ?>
                    <?php elseif (!empty($p->quote)): ?>
                        <?php echo blogen_post_media($p, true); ?>
                    <?php endif; ?>

                    <?php echo get_teaser($p->body, $p->url); ?>
                </div>

                <?php if ($teaserType === 'trimmed'): ?>
                <div class="mt-5">
                    <a class="inline-flex items-center gap-1.5 text-sm font-semibold text-sky transition hover:text-sky-deep" href="<?php echo $p->url; ?>">
                        <?php echo $readMore ? $readMore : 'Read more'; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<nav class="mt-8 flex items-center justify-between gap-3 border-t border-line pt-6 text-sm font-medium sm:mt-10 sm:pt-8" aria-label="Pagination">
    <?php if (!empty($pagination['prev'])): ?>
    <a class="rounded-xl bg-paper px-3 py-2 text-sky shadow-sm ring-1 ring-line transition hover:ring-sky/30 sm:px-4" rel="next" href="?page=<?php echo $page - 1; ?>"><?php echo i18n('next'); ?></a>
    <?php else: ?>
    <span class="rounded-xl px-3 py-2 text-mute/40 sm:px-4"><?php echo i18n('next'); ?></span>
    <?php endif; ?>

    <span class="shrink-0 text-mute"><?php echo $pagination['pagenum']; ?></span>

    <?php if (!empty($pagination['next'])): ?>
    <a class="rounded-xl bg-paper px-3 py-2 text-sky shadow-sm ring-1 ring-line transition hover:ring-sky/30 sm:px-4" rel="prev" href="?page=<?php echo $page + 1; ?>"><?php echo i18n('prev'); ?></a>
    <?php else: ?>
    <span class="rounded-xl px-3 py-2 text-mute/40 sm:px-4"><?php echo i18n('prev'); ?></span>
    <?php endif; ?>
</nav>
<?php endif; ?>
