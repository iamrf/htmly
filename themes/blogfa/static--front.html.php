<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="mb-12 animate-fade-up">
    <header class="mb-6">
        <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl md:text-5xl"><?php echo $static->title; ?></h1>
        <?php if (authorized($p)): ?>
        <p class="mt-3 text-sm"><a class="text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></p>
        <?php endif; ?>
    </header>
    <div class="prose-blogfa max-w-prose text-lg leading-8 text-mute">
        <?php echo $static->body; ?>
    </div>
</article>

<?php if (theme_config('frontpage_posts')): ?>
<?php $posts = recent_posts(true); ?>
<?php if (!empty($posts)): ?>
<section class="animate-fade-up">
    <h2 class="mb-8 text-2xl font-bold tracking-tight text-ink"><?php echo i18n('recent_posts'); ?></h2>
    <?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
    <div class="divide-y divide-sand">
        <?php foreach ($posts as $p): ?>
        <?php $img = get_image($p->body); ?>
        <article class="group py-8 first:pt-0">
            <div class="grid gap-4 md:grid-cols-12 md:items-start">
                <div class="md:col-span-3">
                    <time class="inline-flex items-center gap-2 text-sm font-medium text-mute" datetime="<?php echo date('c', $p->date); ?>">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal"></span>
                        <?php echo jalali_date($p->date, 'long'); ?>
                    </time>
                </div>
                <div class="md:col-span-9">
                    <h3 class="text-xl font-bold tracking-tight sm:text-2xl">
                        <a class="text-ink transition hover:text-teal" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a>
                    </h3>
                    <div class="prose-blogfa mt-3 text-mute leading-7">
                        <?php echo get_teaser($p->body, $p->url); ?>
                    </div>
                    <?php if ($teaserType === 'trimmed'): ?>
                    <a class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>">
                        <?php echo $readMore ? $readMore : 'ادامه مطلب'; ?>
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
