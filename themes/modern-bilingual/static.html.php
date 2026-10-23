<?php if (!defined('HTMLY')) die('HTMLy');
$page = isset($static) ? $static : (isset($p) ? $p : null);
if (!$page) {
    echo '<p>' . i18n('No_posts_found') . '</p>';
    return;
}
?>
<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<article class="animate-fade-up mx-auto max-w-3xl">
    <?php if (login() && isset($p)) { echo tab($p); } ?>
    <header class="mb-8 border-b border-zinc-200 pb-6 dark:border-zinc-800">
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl md:text-5xl"><?php echo $page->title; ?></h1>
        <?php if (isset($p) && authorized($p)): ?>
        <p class="mt-3 text-sm">
            <a class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a>
        </p>
        <?php endif; ?>
    </header>
    <div class="prose prose-zinc prose-mb max-w-none dark:prose-invert" data-article-body>
        <?php echo $page->body; ?>
    </div>

    <?php if ((!empty($next) || !empty($prev))): ?>
    <nav class="mt-12 flex items-center justify-between border-t border-zinc-200 pt-8 dark:border-zinc-800">
        <?php if (!empty($next)): ?>
        <a class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100" href="<?php echo $next['url']; ?>" rel="next">
            <?php echo mb_icon('arrow-left', 'h-4 w-4 rtl:rotate-180'); ?>
            <?php echo $next['title']; ?>
        </a>
        <?php else: ?><span></span><?php endif; ?>
        <?php if (!empty($prev)): ?>
        <a class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100" href="<?php echo $prev['url']; ?>" rel="prev">
            <?php echo $prev['title']; ?>
            <?php echo mb_icon('arrow-right', 'h-4 w-4 rtl:rotate-180'); ?>
        </a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
</article>
