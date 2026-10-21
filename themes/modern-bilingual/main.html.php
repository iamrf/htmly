<?php if (!defined('HTMLY')) die('HTMLy');
$hero = mb_hero();
$cardStyle = mb_card_style();
$posts = isset($posts) ? $posts : array();
$page = isset($page) ? (int)$page : 1;
$isFront = !empty($is_front);
$isTaxonomy = !empty($is_taxonomy);
$teaserType = config('teaser.type');
$readMore = config('read.more');

$featured = null;
$feedPosts = $posts;
if ($isFront && $page <= 1) {
    $featured = mb_featured_post($posts);
    if ($featured && mb_enabled('featured_post', true) && !empty($feedPosts)) {
        // Remove featured from grid to avoid duplicate
        $feedPosts = array_values(array_filter($feedPosts, function ($p) use ($featured) {
            return !(isset($p->url) && isset($featured->url) && $p->url === $featured->url);
        }));
    }
}
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<?php if ($isFront && $page <= 1 && !empty($hero['enabled'])): ?>
<?php
    $heroTitle = $hero['title'] !== '' ? $hero['title'] : blog_title();
    $heroSub = $hero['subtitle'] !== '' ? $hero['subtitle'] : (blog_tagline() ? blog_tagline() : blog_description());
    $bgClass = 'hero-' . (in_array($hero['bg'], array('gradient', 'minimal', 'mesh'), true) ? $hero['bg'] : 'gradient');
?>
<section class="animate-fade-up mb-12 overflow-hidden rounded-3xl border border-zinc-200 <?php echo $bgClass; ?> px-6 py-12 dark:border-zinc-800 sm:px-10 sm:py-16">
    <p class="mb-3 text-sm font-medium text-zinc-500 dark:text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'وبلاگ' : 'Blog'; ?></p>
    <h1 class="max-w-2xl text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl md:text-5xl"><?php echo $heroTitle; ?></h1>
    <?php if ($heroSub): ?>
    <p class="mt-4 max-w-xl text-base leading-relaxed text-zinc-600 dark:text-zinc-400 sm:text-lg"><?php echo $heroSub; ?></p>
    <?php endif; ?>
    <?php if (!empty($hero['search'])): ?>
    <form role="search" class="mt-8 flex max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900" action="<?php echo site_url(); ?>" method="get">
        <input type="search" name="search" class="h-12 flex-1 bg-transparent px-4 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none dark:text-zinc-100" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off">
        <button type="submit" class="inline-flex items-center gap-2 bg-zinc-900 px-4 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
            <i data-lucide="search" class="h-4 w-4"></i>
            <span class="hidden sm:inline"><?php echo mb_lang_code() === 'fa' ? 'جستجو' : 'Search'; ?></span>
        </button>
    </form>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($isTaxonomy && !$isFront && !empty($taxonomy)): ?>
<header class="animate-fade-up mb-10">
    <div class="flex flex-wrap items-center gap-3">
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl"><?php echo $taxonomy->title; ?></h1>
        <?php if (!empty($taxonomy->rss)): ?>
        <a class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-zinc-100 hover:text-orange-500 dark:hover:bg-zinc-800" href="<?php echo $taxonomy->rss; ?>" aria-label="RSS" title="RSS">
            <i data-lucide="rss" class="h-4 w-4"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php if (!empty($taxonomy->count)): ?>
    <p class="mt-2 text-sm text-zinc-500"><?php echo mb_digits((string)$taxonomy->count); ?> <?php echo mb_lang_code() === 'fa' ? 'نوشته' : 'posts'; ?></p>
    <?php endif; ?>
    <?php if (!empty($taxonomy->body)): ?>
    <div class="prose prose-zinc mt-4 max-w-2xl dark:prose-invert"><?php echo $taxonomy->body; ?></div>
    <?php endif; ?>
</header>
<?php elseif (!empty($category) && empty($is_front)): ?>
<header class="animate-fade-up mb-10">
    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl"><?php echo $category->title; ?></h1>
    <?php if (!empty($category->body)): ?>
    <div class="prose prose-zinc mt-4 max-w-2xl dark:prose-invert"><?php echo $category->body; ?></div>
    <?php endif; ?>
</header>
<?php endif; ?>

<?php if ($featured && $isFront && $page <= 1): ?>
<section class="animate-fade-up mb-12">
    <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'ویژه' : 'Featured'; ?></p>
    <?php
    $fImg = mb_post_image($featured);
    $fUrl = !empty($featured->link) ? $featured->link : $featured->url;
    $fExt = !empty($featured->link);
    ?>
    <article class="group overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 lg:grid lg:grid-cols-2">
        <?php if ($fImg): ?>
        <a class="block overflow-hidden" href="<?php echo $fUrl; ?>"<?php echo $fExt ? ' target="_blank" rel="noopener"' : ''; ?>>
            <img class="aspect-[16/10] h-full w-full object-cover transition duration-500 group-hover:scale-105 lg:aspect-auto lg:min-h-[280px]" src="<?php echo htmlspecialchars($fImg, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($featured->title, ENT_QUOTES, 'UTF-8'); ?>">
        </a>
        <?php endif; ?>
        <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-10">
            <div class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500">
                <?php if (!empty($featured->category)): ?><span class="rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"><?php echo $featured->category; ?></span><?php endif; ?>
                <time datetime="<?php echo date('c', $featured->date); ?>"><?php echo mb_date($featured->date, 'long'); ?></time>
                <span><?php echo mb_reading_label($featured); ?></span>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl">
                <a class="transition hover:text-zinc-600 dark:hover:text-zinc-300" href="<?php echo $fUrl; ?>"<?php echo $fExt ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo $featured->title; ?></a>
            </h2>
            <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400"><?php echo mb_excerpt($featured, 160); ?>…</p>
            <div class="mt-6 flex items-center gap-3">
                <?php if (!empty($featured->authorAvatar)): ?>
                <img class="h-8 w-8 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-700" src="<?php echo htmlspecialchars($featured->authorAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="32" height="32">
                <?php endif; ?>
                <a class="text-sm font-medium" href="<?php echo $featured->authorUrl; ?>"><?php echo $featured->authorName; ?></a>
                <a class="ms-auto inline-flex items-center gap-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100" href="<?php echo $fUrl; ?>">
                    <?php echo $readMore ? $readMore : (mb_lang_code() === 'fa' ? 'ادامه مطلب' : 'Read more'); ?>
                    <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i>
                </a>
            </div>
        </div>
    </article>
</section>
<?php endif; ?>

<?php if (!empty($feedPosts)): ?>
<?php if ($isFront): ?>
<p class="mb-6 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Recent_posts'); ?></p>
<?php endif; ?>

<?php if ($cardStyle === 'grid'): ?>
<div class="grid gap-6 sm:grid-cols-2">
    <?php foreach ($feedPosts as $p): ?>
        <?php echo mb_render_card($p, 'grid'); ?>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="divide-y-0">
    <?php foreach ($feedPosts as $p): ?>
        <?php echo mb_render_card($p, 'list'); ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php else: ?>
<div class="rounded-2xl border border-dashed border-zinc-300 px-6 py-16 text-center dark:border-zinc-700">
    <p class="text-zinc-500"><?php echo i18n('No_posts_found'); ?></p>
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

<?php if (disqus_count()): ?>
    <?php echo disqus_count(); ?>
<?php endif; ?>
