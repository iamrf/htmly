<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<section class="animate-fade-up mx-auto max-w-lg py-16 text-center">
    <p class="text-7xl font-bold tracking-tighter text-zinc-200 dark:text-zinc-800">404</p>
    <h1 class="mt-4 text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl">
        <?php echo mb_lang_code() === 'fa' ? 'صفحه پیدا نشد' : "This page doesn't exist"; ?>
    </h1>
    <p class="mt-3 text-zinc-500 dark:text-zinc-400">
        <?php echo mb_lang_code() === 'fa'
            ? 'جستجو کنید یا به صفحهٔ نخست برگردید.'
            : "Search for what you need, or head back home."; ?>
    </p>

    <form role="search" class="mx-auto mt-8 flex max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900" action="<?php echo site_url(); ?>" method="get">
        <input type="search" name="search" class="h-12 flex-1 bg-transparent px-4 text-sm focus:outline-none" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off">
        <button type="submit" class="bg-zinc-900 px-4 text-sm font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
            <i data-lucide="search" class="h-4 w-4"></i>
        </button>
    </form>

    <a href="<?php echo site_url(); ?>" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
        <i data-lucide="home" class="h-4 w-4"></i>
        <?php echo mb_lang_code() === 'fa' ? 'صفحهٔ نخست' : 'Back to homepage'; ?>
    </a>
</section>
