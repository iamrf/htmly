<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<section class="animate-fade-up mx-auto max-w-lg py-16 text-center">
    <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
        <i data-lucide="search-x" class="h-7 w-7 text-zinc-400"></i>
    </div>
    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl">
        <?php echo mb_lang_code() === 'fa' ? 'نتیجه‌ای یافت نشد' : 'No search results'; ?>
    </h1>
    <p class="mt-3 text-zinc-500 dark:text-zinc-400">
        <?php echo mb_lang_code() === 'fa'
            ? 'عبارت دیگری را امتحان کنید یا به صفحهٔ نخست بروید.'
            : 'Try a different query, or visit the homepage.'; ?>
    </p>

    <form role="search" class="mx-auto mt-8 flex max-w-md overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900" action="<?php echo site_url(); ?>" method="get">
        <input type="search" name="search" class="h-12 flex-1 bg-transparent px-4 text-sm focus:outline-none" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off" autofocus>
        <button type="submit" class="bg-zinc-900 px-4 text-sm font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
            <i data-lucide="search" class="h-4 w-4"></i>
        </button>
    </form>

    <a href="<?php echo site_url(); ?>" class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
        <i data-lucide="arrow-left" class="h-4 w-4 rtl:rotate-180"></i>
        <?php echo mb_lang_code() === 'fa' ? 'بازگشت به صفحهٔ نخست' : 'Back to homepage'; ?>
    </a>
</section>
