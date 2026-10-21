<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="animate-fade-up">
    <header class="mb-8 border-b border-sand pb-6">
        <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl md:text-5xl"><?php echo $static->title; ?></h1>
        <?php if (authorized($p)): ?>
        <p class="mt-3 text-sm"><a class="text-teal hover:text-teal-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></p>
        <?php endif; ?>
    </header>
    <div class="prose-blogfa max-w-none">
        <?php echo $static->body; ?>
    </div>
</article>
