<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<header class="mb-8 animate-fade-up sm:mb-10">
    <h1 class="text-2xl font-extrabold tracking-tight text-ink xs:text-3xl sm:text-4xl">
        <?php echo $author->title; ?>
        <a class="ml-2 inline-flex align-middle text-sky hover:text-sky-deep" aria-label="RSS" title="RSS" href="<?php echo $author->rss; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-3-8.5a1 1 0 0 1 1-1c5.523 0 10 4.477 10 10a1 1 0 1 1-2 0 8 8 0 0 0-8-8 1 1 0 0 1-1-1m0 4a1 1 0 0 1 1-1 6 6 0 0 1 6 6 1 1 0 1 1-2 0 4 4 0 0 0-4-4 1 1 0 0 1-1-1"/></svg>
        </a>
    </h1>
    <?php if (!empty($author->body)): ?>
    <div class="prose-blogen mt-3 max-w-prose text-mute leading-7">
        <?php echo $author->body; ?>
    </div>
    <?php endif; ?>
</header>

<?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
<?php if (!empty($posts)): ?>
<div class="divide-y divide-line">
    <?php foreach ($posts as $p): ?>
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
            </div>
            <div class="md:col-span-9">
                <h2 class="text-xl font-bold leading-snug tracking-tight xs:text-2xl sm:text-3xl">
                    <a class="text-ink transition hover:text-sky" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a>
                </h2>
                <?php if (!empty($p->tag)): ?>
                <div class="mt-2 tags text-sm"><?php echo $p->tag; ?></div>
                <?php endif; ?>
                <div class="prose-blogen mt-4 text-mute leading-7 sm:leading-8">
                    <?php echo get_teaser($p->body, $p->url); ?>
                </div>
                <?php if ($teaserType === 'trimmed'): ?>
                <div class="mt-5">
                    <a class="inline-flex items-center gap-1.5 text-sm font-semibold text-sky hover:text-sky-deep" href="<?php echo $p->url; ?>">
                        <?php echo $readMore ? $readMore : 'Read more'; ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

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
