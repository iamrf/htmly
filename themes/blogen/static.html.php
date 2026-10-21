<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="animate-fade-up">
    <header class="mb-6 border-b border-line pb-5 sm:mb-8 sm:pb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink xs:text-3xl sm:text-4xl md:text-5xl"><?php echo $static->title; ?></h1>
        <?php if (authorized($p)): ?>
        <p class="mt-3 text-sm"><a class="text-sky hover:text-sky-deep" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a></p>
        <?php endif; ?>
    </header>
    <div class="prose-blogen max-w-none">
        <?php echo $static->body; ?>
    </div>
</article>
