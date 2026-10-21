<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<header class="mb-10 animate-fade-up">
    <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">
        <?php echo $author->title; ?>
        <a class="mr-2 inline-flex align-middle text-teal hover:text-teal-deep" aria-label="RSS" title="RSS" href="<?php echo $author->rss; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-3-8.5a1 1 0 0 1 1-1c5.523 0 10 4.477 10 10a1 1 0 1 1-2 0 8 8 0 0 0-8-8 1 1 0 0 1-1-1m0 4a1 1 0 0 1 1-1 6 6 0 0 1 6 6 1 1 0 1 1-2 0 4 4 0 0 0-4-4 1 1 0 0 1-1-1"/></svg>
        </a>
    </h1>
    <?php if (!empty($author->body)): ?>
    <div class="prose-blogfa mt-3 max-w-prose text-mute leading-7">
        <?php echo $author->body; ?>
    </div>
    <?php endif; ?>
</header>

<?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
<?php if (!empty($posts)): ?>
<div class="divide-y divide-sand">
    <?php foreach ($posts as $p): ?>
    <article class="group py-10 animate-fade-up first:pt-0">
        <div class="grid gap-6 md:grid-cols-12 md:items-start">
            <div class="md:col-span-3">
                <time class="inline-flex items-center gap-2 text-sm font-medium text-mute" datetime="<?php echo date('c', $p->date); ?>">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal"></span>
                    <?php echo jalali_date($p->date, 'long'); ?>
                </time>
                <?php if (authorized($p)): ?>
                <div class="mt-2 text-sm"><a class="text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></div>
                <?php endif; ?>
            </div>
            <div class="md:col-span-9">
                <h2 class="text-2xl font-bold leading-snug tracking-tight sm:text-3xl">
                    <a class="text-ink transition hover:text-teal" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a>
                </h2>
                <?php if (!empty($p->tag)): ?>
                <div class="mt-2 tags text-sm"><?php echo $p->tag; ?></div>
                <?php endif; ?>
                <div class="prose-blogfa mt-4 text-mute leading-8">
                    <?php echo get_teaser($p->body, $p->url); ?>
                </div>
                <?php if ($teaserType === 'trimmed'): ?>
                <div class="mt-5">
                    <a class="inline-flex items-center gap-1 text-sm font-semibold text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>">
                        <?php echo $readMore ? $readMore : 'ادامه مطلب'; ?>
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
<nav class="mt-10 flex items-center justify-between border-t border-sand pt-8 text-sm font-medium" aria-label="صفحه‌بندی">
    <?php if (!empty($pagination['prev'])): ?>
    <a class="rounded-xl bg-paper px-4 py-2 text-teal shadow-sm ring-1 ring-sand transition hover:ring-teal/30" rel="next" href="?page=<?php echo $page - 1; ?>"><?php echo i18n('next'); ?></a>
    <?php else: ?>
    <span class="rounded-xl px-4 py-2 text-mute/40"><?php echo i18n('next'); ?></span>
    <?php endif; ?>
    <span class="text-mute"><?php echo blogfa_persian_digits($pagination['pagenum']); ?></span>
    <?php if (!empty($pagination['next'])): ?>
    <a class="rounded-xl bg-paper px-4 py-2 text-teal shadow-sm ring-1 ring-sand transition hover:ring-teal/30" rel="prev" href="?page=<?php echo $page + 1; ?>"><?php echo i18n('prev'); ?></a>
    <?php else: ?>
    <span class="rounded-xl px-4 py-2 text-mute/40"><?php echo i18n('prev'); ?></span>
    <?php endif; ?>
</nav>
<?php endif; ?>
