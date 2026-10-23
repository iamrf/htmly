<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Tag archive — used via main--tag.html.php
 */
$posts = isset($posts) ? $posts : array();
$cardStyle = mb_card_style();
$page = isset($page) ? (int)$page : 1;
$tagObj = isset($tag) ? $tag : (isset($taxonomy) ? $taxonomy : null);
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<header class="animate-fade-up mb-10">
    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Tags'); ?></p>
    <div class="flex flex-wrap items-center gap-3">
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl">
            #<?php echo $tagObj && isset($tagObj->title) ? $tagObj->title : ''; ?>
        </h1>
        <?php if ($tagObj && !empty($tagObj->url)): ?>
        <a class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-zinc-100 hover:text-orange-500 dark:hover:bg-zinc-800" href="<?php echo $tagObj->url; ?>/feed" aria-label="RSS">
            <?php echo mb_icon('rss', 'h-4 w-4'); ?>
        </a>
        <?php endif; ?>
    </div>
    <?php if ($tagObj && !empty($tagObj->count)): ?>
    <p class="mt-2 text-sm text-zinc-500"><?php echo mb_digits((string)$tagObj->count); ?> <?php echo mb_lang_code() === 'fa' ? 'نوشته' : 'posts'; ?></p>
    <?php endif; ?>
    <?php if ($tagObj && !empty($tagObj->body)): ?>
    <div class="prose prose-zinc mt-4 max-w-2xl dark:prose-invert"><?php echo $tagObj->body; ?></div>
    <?php endif; ?>
</header>

<?php if (!empty($posts)): ?>
    <?php if ($cardStyle === 'grid'): ?>
    <div class="grid gap-6 sm:grid-cols-2">
        <?php foreach ($posts as $p): echo mb_render_card($p, 'grid'); endforeach; ?>
    </div>
    <?php else: ?>
    <div><?php foreach ($posts as $p): echo mb_render_card($p, 'list'); endforeach; ?></div>
    <?php endif; ?>

    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <nav class="mb-pagination mt-12 flex items-center justify-between border-t border-zinc-200 pt-8 dark:border-zinc-800">
        <?php if (!empty($pagination['prev'])): ?>
        <a rel="next" href="?page=<?php echo $page - 1; ?>"><?php echo mb_icon('arrow-left', 'h-4 w-4 rtl:rotate-180'); ?> <?php echo i18n('next'); ?></a>
        <?php else: ?><span class="is-disabled"><?php echo i18n('next'); ?></span><?php endif; ?>
        <?php if (!empty($pagination['pagenum'])): ?><span class="text-sm text-zinc-500"><?php echo mb_digits($pagination['pagenum']); ?></span><?php endif; ?>
        <?php if (!empty($pagination['next'])): ?>
        <a rel="prev" href="?page=<?php echo $page + 1; ?>"><?php echo i18n('prev'); ?> <?php echo mb_icon('arrow-right', 'h-4 w-4 rtl:rotate-180'); ?></a>
        <?php else: ?><span class="is-disabled"><?php echo i18n('prev'); ?></span><?php endif; ?>
    </nav>
    <?php endif; ?>
<?php else: ?>
<div class="rounded-2xl border border-dashed border-zinc-300 px-6 py-16 text-center dark:border-zinc-700">
    <p class="text-zinc-500"><?php echo i18n('No_posts_found'); ?></p>
</div>
<?php endif; ?>
