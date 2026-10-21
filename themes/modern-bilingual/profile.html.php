<?php if (!defined('HTMLY')) die('HTMLy');
$posts = isset($posts) ? $posts : array();
$author = isset($author) ? $author : null;
$cardStyle = mb_card_style();
$page = isset($page) ? (int)$page : 1;
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<?php if ($author): ?>
<header class="animate-fade-up mb-10 overflow-hidden rounded-3xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8" itemtype="http://schema.org/Person" itemscope itemprop="Person">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
        <?php if (!empty($author->avatar)): ?>
        <img class="h-20 w-20 rounded-2xl object-cover ring-2 ring-zinc-100 dark:ring-zinc-800" src="<?php echo htmlspecialchars($author->avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="80" height="80" itemprop="image">
        <?php endif; ?>
        <div class="min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl" itemprop="name"><?php echo $author->name; ?></h1>
            <?php if (!empty($author->about)): ?>
            <div class="prose prose-sm prose-zinc mt-2 max-w-2xl dark:prose-invert" itemprop="description"><?php echo $author->about; ?></div>
            <?php endif; ?>
            <?php if (!empty($posts)): ?>
            <p class="mt-3 text-sm text-zinc-500"><?php echo mb_digits((string)count($posts)); ?>+ <?php echo mb_lang_code() === 'fa' ? 'نوشته' : 'posts'; ?></p>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php endif; ?>

<h2 class="mb-6 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Post_by_author'); ?></h2>

<?php if (!empty($posts)): ?>
    <?php if ($cardStyle === 'grid'): ?>
    <div class="grid gap-6 sm:grid-cols-2">
        <?php foreach ($posts as $p): ?>
            <?php echo mb_render_card($p, 'grid'); ?>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div>
        <?php foreach ($posts as $p): ?>
            <?php echo mb_render_card($p, 'list'); ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <nav class="mb-pagination mt-12 flex items-center justify-between border-t border-zinc-200 pt-8 dark:border-zinc-800" aria-label="Pagination">
        <?php if (!empty($pagination['prev'])): ?>
        <a rel="next" href="?page=<?php echo $page - 1; ?>">
            <i data-lucide="arrow-left" class="h-4 w-4 rtl:rotate-180"></i>
            <?php echo i18n('next'); ?>
        </a>
        <?php else: ?>
        <span class="is-disabled"><?php echo i18n('next'); ?></span>
        <?php endif; ?>
        <?php if (!empty($pagination['pagenum'])): ?>
        <span class="text-sm text-zinc-500"><?php echo mb_digits($pagination['pagenum']); ?></span>
        <?php endif; ?>
        <?php if (!empty($pagination['next'])): ?>
        <a rel="prev" href="?page=<?php echo $page + 1; ?>">
            <?php echo i18n('prev'); ?>
            <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i>
        </a>
        <?php else: ?>
        <span class="is-disabled"><?php echo i18n('prev'); ?></span>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
<?php else: ?>
<div class="rounded-2xl border border-dashed border-zinc-300 px-6 py-16 text-center dark:border-zinc-700">
    <p class="text-zinc-500"><?php echo i18n('No_posts_found'); ?>!</p>
</div>
<?php endif; ?>
