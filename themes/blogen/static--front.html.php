<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="mb-10 animate-fade-up sm:mb-12">
    <header class="mb-5 sm:mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink xs:text-3xl sm:text-4xl md:text-5xl"><?php echo $static->title; ?></h1>
        <?php if (authorized($p)): ?>
        <p class="mt-3 text-sm"><a class="text-sky hover:text-sky-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></p>
        <?php endif; ?>
    </header>
    <div class="prose-blogen max-w-prose text-base leading-7 text-mute sm:text-lg sm:leading-8">
        <?php echo $static->body; ?>
    </div>
</article>

<?php if (theme_config('frontpage_posts')): ?>
<?php $posts = recent_posts(true); ?>
<?php if (!empty($posts)): ?>
<section class="animate-fade-up">
    <h2 class="mb-6 text-xl font-bold tracking-tight text-ink sm:mb-8 sm:text-2xl"><?php echo i18n('recent_posts'); ?></h2>
    <?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
    <div class="divide-y divide-line">
        <?php foreach ($posts as $p): ?>
        <article class="group py-6 first:pt-0 sm:py-8">
            <div class="grid gap-3 sm:gap-4 md:grid-cols-12 md:items-start">
                <div class="md:col-span-3">
                    <time class="inline-flex items-center gap-2 text-sm font-medium text-mute" datetime="<?php echo date('c', $p->date); ?>">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-sky"></span>
                        <?php echo format_date($p->date); ?>
                    </time>
                </div>
                <div class="md:col-span-9">
                    <h3 class="text-lg font-bold tracking-tight sm:text-xl md:text-2xl">
                        <a class="text-ink transition hover:text-sky" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a>
                    </h3>
                    <div class="prose-blogen mt-2 text-mute leading-7 sm:mt-3">
                        <?php echo get_teaser($p->body, $p->url); ?>
                    </div>
                    <?php if ($teaserType === 'trimmed'): ?>
                    <a class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-sky hover:text-sky-deep sm:mt-4" href="<?php echo $p->url; ?>">
                        <?php echo $readMore ? $readMore : 'Read more'; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>
