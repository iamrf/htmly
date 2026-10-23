<?php if (!defined('HTMLY')) die('HTMLy');
$showToc = mb_enabled('post_toc', true);
$showShare = mb_enabled('post_share', true);
$showNav = mb_enabled('post_navigation', true);
$showRelated = mb_enabled('related_posts', true);
$showTags = mb_enabled('post_tag', true);
// Side TOC only when site sidebar is hidden (avoids double sidebars); otherwise inline TOC
$tocAside = $showToc && (mb_sidebar_position() === 'hidden');
$tocInline = $showToc && !$tocAside;
?>

<?php if (!empty($breadcrumb)): ?>
<nav class="mb-breadcrumb" aria-label="Breadcrumb"><?php echo $breadcrumb; ?></nav>
<?php endif; ?>

<article class="animate-fade-up min-w-0 max-w-full" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
    <?php if (login()) { echo tab($p); } ?>

    <header class="mb-8 border-b border-zinc-200 pb-8 dark:border-zinc-800">
        <div class="mb-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
            <time datetime="<?php echo date('c', $p->date); ?>" itemprop="datePublished"><?php echo mb_date($p->date, 'full'); ?></time>
            <?php if (!empty($p->category)): ?>
            <span class="text-zinc-300 dark:text-zinc-600">·</span>
            <span itemprop="articleSection"><?php echo $p->category; ?></span>
            <?php endif; ?>
            <span class="text-zinc-300 dark:text-zinc-600">·</span>
            <span><?php echo mb_reading_label($p); ?></span>
            <?php if (authorized($p)): ?>
            <span class="text-zinc-300 dark:text-zinc-600">·</span>
            <a class="hover:text-zinc-900 dark:hover:text-zinc-100" href="<?php echo $p->url; ?>/edit?destination=post"><?php echo i18n('edit'); ?></a>
            <?php endif; ?>
        </div>

        <?php if (!empty($p->link)): ?>
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl md:text-5xl" itemprop="headline">
            <a href="<?php echo $p->link; ?>" target="_blank" rel="noopener"><?php echo $p->title; ?>
                <?php echo mb_icon('external-link', 'inline h-6 w-6 opacity-40'); ?>
            </a>
        </h1>
        <?php else: ?>
        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl md:text-5xl" itemprop="headline"><?php echo $p->title; ?></h1>
        <?php endif; ?>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <?php if (!empty($p->authorAvatar)): ?>
                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-zinc-100 dark:ring-zinc-800" src="<?php echo htmlspecialchars($p->authorAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="40" height="40">
                <?php endif; ?>
                <div>
                    <a class="font-medium text-zinc-900 dark:text-zinc-100" href="<?php echo $p->authorUrl; ?>" itemprop="author"><?php echo $p->authorName; ?></a>
                    <p class="text-xs text-zinc-500"><?php echo i18n('Posted_in'); ?> <?php echo isset($p->category) ? $p->category : ''; ?></p>
                </div>
            </div>

            <?php if ($showShare): ?>
                <?php echo mb_share_buttons($p, 'compact'); ?>
            <?php endif; ?>
        </div>
    </header>

    <div class="grid min-w-0 gap-10 <?php echo $tocAside ? 'lg:grid-cols-12' : ''; ?>">
        <div class="min-w-0 <?php echo $tocAside ? 'lg:col-span-8' : ''; ?>">
            <div class="mb-8">
                <?php echo mb_post_media($p, false); ?>
            </div>

            <?php if ($tocInline): ?>
            <div class="mb-toc-panel mb-8 rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'فهرست مطالب' : 'On this page'; ?></h2>
                <div id="mb-toc" class="mb-toc-nav hidden"></div>
                <p class="toc-empty text-xs text-zinc-400" id="toc-fallback"><?php echo mb_lang_code() === 'fa' ? 'سرفصلی یافت نشد' : 'No headings found'; ?></p>
            </div>
            <?php endif; ?>

            <div class="prose prose-zinc prose-mb max-w-none dark:prose-invert prose-headings:scroll-mt-24 prose-a:text-zinc-900 dark:prose-a:text-zinc-100" data-article-body itemprop="articleBody">
                <?php echo $p->body; ?>
            </div>

            <?php if ($showTags && !empty($p->tag)): ?>
            <div class="mt-10 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo i18n('Tags'); ?></h2>
                <div class="tags flex flex-wrap gap-2"><?php echo $p->tag; ?></div>
            </div>
            <?php endif; ?>

            <?php if ($showShare): ?>
                <?php echo mb_share_buttons($p, 'bar'); ?>
            <?php endif; ?>

            <!-- Author box -->
            <div class="mt-10 flex gap-4 rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <?php if (!empty($p->authorAvatar)): ?>
                <img class="h-14 w-14 shrink-0 rounded-full object-cover ring-2 ring-zinc-100 dark:ring-zinc-800" src="<?php echo htmlspecialchars($p->authorAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="56" height="56">
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'نویسنده' : 'Author'; ?></p>
                    <a class="text-lg font-semibold text-zinc-900 dark:text-zinc-50" href="<?php echo $p->authorUrl; ?>"><?php echo $p->authorName; ?></a>
                    <?php if (!empty($p->authorAbout)): ?>
                    <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400"><?php echo $p->authorAbout; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($showNav && (!empty($next) || !empty($prev))): ?>
            <nav class="mt-10 grid gap-4 sm:grid-cols-2">
                <?php if (!empty($next)): ?>
                <a class="group rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-600" href="<?php echo $next['url']; ?>" rel="next">
                    <p class="mb-1 flex items-center gap-1 text-xs font-medium text-zinc-400">
                        <?php echo mb_icon('arrow-left', 'h-3.5 w-3.5 rtl:rotate-180'); ?>
                        <?php echo i18n('Next'); ?>
                    </p>
                    <p class="font-semibold text-zinc-900 group-hover:underline dark:text-zinc-100"><?php echo $next['title']; ?></p>
                </a>
                <?php else: ?>
                <div></div>
                <?php endif; ?>
                <?php if (!empty($prev)): ?>
                <a class="group rounded-2xl border border-zinc-200 bg-white p-5 text-end transition hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-600" href="<?php echo $prev['url']; ?>" rel="prev">
                    <p class="mb-1 flex items-center justify-end gap-1 text-xs font-medium text-zinc-400">
                        <?php echo i18n('Prev'); ?>
                        <?php echo mb_icon('arrow-right', 'h-3.5 w-3.5 rtl:rotate-180'); ?>
                    </p>
                    <p class="font-semibold text-zinc-900 group-hover:underline dark:text-zinc-100"><?php echo $prev['title']; ?></p>
                </a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

            <?php if ($showRelated && !empty($p->related)): ?>
            <?php $related = get_related($p->related, true, config('related.count')); ?>
            <?php if (!empty($related) && is_array($related)): ?>
            <section class="mt-12 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                <h2 class="mb-6 text-xl font-bold tracking-tight"><?php echo i18n('Related_posts'); ?></h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($related as $rl): ?>
                        <?php echo mb_render_card($rl, 'grid'); ?>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (local()): ?>
            <div class="mt-12 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                <?php mb_comments_section($p->url); ?>
            </div>
            <?php elseif (facebook() || disqus()): ?>
            <section class="mt-12 border-t border-zinc-200 pt-10 dark:border-zinc-800" id="comments">
                <h2 class="mb-6 text-xl font-bold tracking-tight"><?php echo i18n('Comments'); ?></h2>
                <?php if (facebook()): ?>
                    <div class="fb-comments" data-href="<?php echo $p->url; ?>" data-numposts="<?php echo config('fb.num'); ?>" data-colorscheme="<?php echo config('fb.color'); ?>"></div>
                <?php endif; ?>
                <?php if (disqus()): ?>
                    <?php echo disqus($p->title, $p->url); ?>
                    <div id="disqus_thread"></div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>

        <?php if ($tocAside): ?>
        <aside class="hidden lg:col-span-4 lg:block">
            <div class="mb-toc mb-toc-panel sticky top-24 rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-400"><?php echo mb_lang_code() === 'fa' ? 'فهرست مطالب' : 'On this page'; ?></h2>
                <div id="mb-toc" class="mb-toc-nav hidden"></div>
                <p class="toc-empty text-xs text-zinc-400" id="toc-fallback"><?php echo mb_lang_code() === 'fa' ? 'سرفصلی یافت نشد' : 'No headings found'; ?></p>
            </div>
        </aside>
        <?php endif; ?>
    </div>
</article>

<?php if (disqus_count()): ?>
    <?php echo disqus_count(); ?>
<?php endif; ?>

<script>
(function () {
  var toc = document.getElementById('mb-toc');
  var fallback = document.getElementById('toc-fallback');
  if (!toc) return;
  // Hide fallback once TOC is populated by app.js
  var obs = new MutationObserver(function () {
    if (toc.children.length && fallback) fallback.classList.add('hidden');
  });
  obs.observe(toc, { childList: true });
})();
</script>
