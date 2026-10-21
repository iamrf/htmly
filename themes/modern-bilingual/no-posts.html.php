<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="animate-fade-up rounded-3xl border border-zinc-200 bg-white px-6 py-20 text-center dark:border-zinc-800 dark:bg-zinc-900">
    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
        <i data-lucide="file-text" class="h-7 w-7 text-zinc-400"></i>
    </div>
    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl">
        <?php echo mb_lang_code() === 'fa' ? 'هنوز نوشته‌ای نیست' : 'No posts yet'; ?>
    </h1>
    <p class="mx-auto mt-3 max-w-md text-zinc-500 dark:text-zinc-400">
        <?php echo mb_lang_code() === 'fa'
            ? 'اولین پست را منتشر کنید یا صفحهٔ نخست ایستا را فعال کنید.'
            : 'Publish your first post, or enable a static frontpage.'; ?>
    </p>
    <?php if (login()): ?>
    <a href="<?php echo site_url(); ?>admin/content" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
        <i data-lucide="plus" class="h-4 w-4"></i>
        <?php echo mb_lang_code() === 'fa' ? 'نوشتهٔ جدید' : 'New post'; ?>
    </a>
    <?php endif; ?>
</div>
