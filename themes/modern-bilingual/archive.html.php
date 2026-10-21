<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Date archive — used via main--archive.html.php
 */
$posts = isset($posts) ? $posts : array();
$cardStyle = mb_card_style();
$page = isset($page) ? (int)$page : 1;
$arch = isset($archive) ? $archive : (isset($taxonomy) ? $taxonomy : null);
$archiveTitle = ($arch && isset($arch->title)) ? $arch->title : i18n('Archives');
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<header class="animate-fade-up mb-10">
    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Archives'); ?></p>
    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl">
        <?php echo $archiveTitle; ?>
    </h1>
    <?php if ($arch && !empty($arch->count)): ?>
    <p class="mt-2 text-sm text-zinc-500"><?php echo mb_digits((string)$arch->count); ?> <?php echo mb_lang_code() === 'fa' ? 'نوشته' : 'posts'; ?></p>
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
