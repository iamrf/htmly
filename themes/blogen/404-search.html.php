<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="animate-fade-up py-6 sm:py-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-ink xs:text-3xl sm:text-4xl"><?php echo i18n('search_results_not_found'); ?></h1>
    <p class="mt-4">
        <a class="inline-flex items-center gap-2 text-sm font-semibold text-sky hover:text-sky-deep" href="<?php echo site_url(); ?>">
            ← <?php echo i18n('back_to'); ?> <?php echo i18n('homepage'); ?>
        </a>
    </p>
    <div class="relative mt-6 max-w-md sm:mt-8">
        <form role="search">
            <input type="search" name="search" placeholder="<?php echo i18n('type_to_search'); ?>" class="block w-full rounded-xl border border-line bg-paper px-4 py-3 pr-11 text-base text-ink placeholder:text-mute/60 focus:border-sky focus:outline-none focus:ring-2 focus:ring-sky/20 sm:text-sm">
        </form>
        <svg class="pointer-events-none absolute right-3 top-3.5 h-5 w-5 text-mute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
</div>
