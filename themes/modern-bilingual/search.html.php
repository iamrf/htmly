<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Search results — used via main--search.html.php
 */
$posts = isset($posts) ? $posts : array();
$cardStyle = mb_card_style();
$page = isset($page) ? (int)$page : 1;
$searchObj = null;
if (isset($search) && is_object($search)) {
    $searchObj = $search;
} elseif (isset($taxonomy) && is_object($taxonomy)) {
    $searchObj = $taxonomy;
}
$searchTitle = $searchObj && isset($searchObj->title) ? $searchObj->title : (is_string($search ?? null) ? $search : '');
$count = ($searchObj && !empty($searchObj->count)) ? $searchObj->count : (!empty($posts) ? count($posts) : 0);
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<header class="animate-fade-up mb-10">
    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'جستجو' : 'Search'; ?></p>
    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl">
        <?php echo htmlspecialchars((string)$searchTitle, ENT_QUOTES, 'UTF-8'); ?>
    </h1>
    <p class="mt-2 text-sm text-zinc-500">
        <?php echo mb_digits((string)$count); ?>
        <?php echo mb_lang_code() === 'fa' ? 'نتیجه' : 'results'; ?>
    </p>

    <form role="search" class="mt-6 flex max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900" action="<?php echo site_url(); ?>" method="get">
        <input type="search" name="search" value="<?php echo htmlspecialchars((string)$searchTitle, ENT_QUOTES, 'UTF-8'); ?>" class="h-11 flex-1 bg-transparent px-4 text-sm focus:outline-none" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off">
        <button type="submit" class="bg-zinc-900 px-4 text-sm font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
            <i data-lucide="search" class="h-4 w-4"></i>
        </button>
    </form>
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
        <a rel="next" href="?page=<?php echo $page - 1; ?>"><i data-lucide="arrow-left" class="h-4 w-4 rtl:rotate-180"></i> <?php echo i18n('next'); ?></a>
        <?php else: ?><span class="is-disabled"><?php echo i18n('next'); ?></span><?php endif; ?>
        <?php if (!empty($pagination['pagenum'])): ?><span class="text-sm text-zinc-500"><?php echo mb_digits($pagination['pagenum']); ?></span><?php endif; ?>
        <?php if (!empty($pagination['next'])): ?>
        <a rel="prev" href="?page=<?php echo $page + 1; ?>"><?php echo i18n('prev'); ?> <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i></a>
        <?php else: ?><span class="is-disabled"><?php echo i18n('prev'); ?></span><?php endif; ?>
    </nav>
    <?php endif; ?>
<?php else: ?>
<div class="rounded-2xl border border-dashed border-zinc-300 px-6 py-16 text-center dark:border-zinc-700">
    <p class="text-zinc-500"><?php echo i18n('No_posts_found'); ?></p>
</div>
<?php endif; ?>
