<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="animate-fade-up py-8 text-center sm:text-right">
    <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl"><?php echo i18n('search_results_not_found'); ?></h1>
    <p class="mt-4">
        <a class="inline-flex items-center gap-2 text-sm font-semibold text-teal hover:text-teal-deep" href="<?php echo site_url(); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            <?php echo i18n('back_to'); ?> <?php echo i18n('homepage'); ?>
        </a>
    </p>
    <div class="relative mx-auto mt-8 max-w-md sm:mx-0">
        <form role="search">
            <input type="search" name="search" placeholder="<?php echo i18n('type_to_search'); ?>" class="block w-full rounded-xl border border-sand bg-paper px-4 py-3 pl-11 text-ink placeholder:text-mute/60 focus:border-teal focus:outline-none focus:ring-2 focus:ring-teal/20">
        </form>
        <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-mute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
</div>
