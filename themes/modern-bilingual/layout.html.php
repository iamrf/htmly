<?php if (!defined('HTMLY')) die('HTMLy');
$dir = mb_dir();
$lang = mb_html_lang();
$langCode = mb_lang_code();
$isRtl = mb_is_rtl();
$fontClass = $isRtl ? 'font-vazir' : 'font-sans';
$stickyNav = mb_enabled('sticky_navbar', true);
$sidebarPos = mb_sidebar_position();
$cta = mb_cta();
$colorMode = mb_default_color_mode();
$referrer = mb_referrer_param();
$bodyclass = isset($bodyclass) ? $bodyclass : '';
$isPost = (strpos($bodyclass, 'in-post') !== false);
$showSidebar = ($sidebarPos !== 'hidden');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>" dir="<?php echo $dir; ?>" class="scroll-smooth<?php echo $colorMode === 'dark' ? ' dark' : ''; ?>" id="html-root" data-default-theme="<?php echo htmlspecialchars($colorMode, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php echo head_contents(); ?>
    <?php echo isset($metatags) ? $metatags : ''; ?>
    <meta name="color-scheme" content="light dark">
    <script>
    (function () {
        var root = document.documentElement;
        var def = root.getAttribute('data-default-theme') || 'system';
        var stored = null;
        var storedDef = null;
        try {
            stored = localStorage.getItem('mb-theme');
            storedDef = localStorage.getItem('mb-theme-default');
        } catch (e) {}
        // If admin default_color_mode changed, drop stale user preference
        if (storedDef !== def) {
            try {
                localStorage.setItem('mb-theme-default', def);
                if (storedDef !== null) {
                    localStorage.removeItem('mb-theme');
                    stored = null;
                }
            } catch (e) {}
        }
        var mode = stored || def;
        var dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        root.classList.toggle('dark', dark);
    })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if ($isRtl): ?>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php else: ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        vazir: ['Vazirmatn', 'Tahoma', 'ui-sans-serif', 'sans-serif']
                    },
                    boxShadow: {
                        glass: '0 1px 0 0 rgba(255,255,255,0.06) inset, 0 8px 32px rgba(0,0,0,0.08)'
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?php echo mb_asset('css/style.css'); ?>">
</head>
<body class="<?php echo htmlspecialchars($bodyclass . ' ' . $fontClass, ENT_QUOTES, 'UTF-8'); ?> bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" data-referrer-param="<?php echo htmlspecialchars($referrer, ENT_QUOTES, 'UTF-8'); ?>">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>

<?php if (mb_enabled('reading_progress', true) && $isPost): ?>
<div id="reading-progress" class="pointer-events-none fixed inset-x-0 top-0 z-[60] h-0.5 origin-left scale-x-0 bg-zinc-900 transition-transform dark:bg-zinc-100 rtl:origin-right" aria-hidden="true"></div>
<?php endif; ?>

<div class="mb-atmosphere" aria-hidden="true"></div>

<div class="mb-page relative min-h-screen flex flex-col overflow-x-clip">
    <header class="<?php echo $stickyNav ? 'sticky top-0 z-40' : 'relative z-40'; ?> border-b border-zinc-200/80 bg-white/80 shadow-sm backdrop-blur-md dark:border-zinc-800/80 dark:bg-zinc-900/80">
        <div class="mx-auto flex w-full max-w-6xl min-w-0 items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <a href="<?php echo site_url(); ?>" class="group flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-zinc-900 text-sm font-bold text-white transition group-hover:scale-105 dark:bg-zinc-100 dark:text-zinc-900">
                    <?php echo mb_substr(blog_title(), 0, 1, 'UTF-8'); ?>
                </span>
                <span class="min-w-0">
                    <?php if (is_index()): ?>
                    <span class="block truncate text-base font-semibold tracking-tight sm:text-lg"><?php echo blog_title(); ?></span>
                    <?php else: ?>
                    <span class="block truncate text-base font-semibold tracking-tight sm:text-lg"><?php echo blog_title(); ?></span>
                    <?php endif; ?>
                    <?php if (blog_tagline()): ?>
                    <span class="hidden truncate text-xs text-zinc-500 dark:text-zinc-400 sm:block"><?php echo blog_tagline(); ?></span>
                    <?php endif; ?>
                </span>
            </a>

            <nav class="nav-desktop hidden items-center gap-1 lg:flex" aria-label="Primary">
                <?php echo menu('nav-menu'); ?>
            </nav>

            <div class="flex items-center gap-1 sm:gap-2">
                <div class="nav-social hidden items-center gap-1 xl:flex" aria-hidden="false">
                    <?php
                    $navSocial = mb_social_links();
                    $navShown = 0;
                    foreach ($navSocial as $ns) {
                        if ($ns['id'] === 'rss') {
                            continue;
                        }
                        if ($navShown >= 3) {
                            break;
                        }
                        $navShown++;
                        echo '<a class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" href="' . htmlspecialchars($ns['url'], ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="me noopener noreferrer" title="' . htmlspecialchars($ns['label'], ENT_QUOTES, 'UTF-8') . '" aria-label="' . htmlspecialchars($ns['label'], ENT_QUOTES, 'UTF-8') . '">' . mb_brand_icon($ns['icon'], 'mb-brand-icon h-3.5 w-3.5') . '</a>';
                    }
                    ?>
                </div>
                <button type="button" class="search-open inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" aria-label="Search" title="Search (Ctrl/Cmd+K)">
                    <i data-lucide="search" class="h-4 w-4"></i>
                </button>

                <button type="button" id="theme-toggle" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" aria-label="Toggle theme">
                    <i data-lucide="sun" class="theme-icon-sun hidden h-4 w-4 dark:block"></i>
                    <i data-lucide="moon" class="theme-icon-moon block h-4 w-4 dark:hidden"></i>
                </button>

                <?php if (!empty($cta['enabled'])): ?>
                <?php
                    $ctaClass = $cta['style'] === 'outline'
                        ? 'border border-zinc-300 bg-transparent text-zinc-900 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-100 dark:hover:bg-zinc-800'
                        : 'bg-zinc-900 text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white';
                ?>
                <a href="<?php echo htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo $cta['target']; ?>"<?php echo $cta['target'] === '_blank' ? ' rel="noopener"' : ''; ?> class="hidden items-center rounded-lg px-3 py-1.5 text-sm font-medium transition sm:inline-flex <?php echo $ctaClass; ?>">
                    <?php echo htmlspecialchars($cta['text'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <?php endif; ?>

                <button type="button" class="menu-open inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 lg:hidden" aria-label="Open menu" id="menu-toggle">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile drawer -->
    <div class="menu-drawer" role="dialog" aria-modal="true" aria-label="Mobile menu" tabindex="-1">
        <div class="menu-drawer-backdrop absolute inset-0 bg-zinc-950/40 backdrop-blur-sm" data-drawer-close></div>
        <div class="menu-drawer-panel absolute inset-y-0 z-10 flex w-full max-w-xs flex-col bg-white shadow-2xl dark:bg-zinc-900 end-0">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                <span class="font-semibold"><?php echo blog_title(); ?></span>
                <button type="button" class="menu-close inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="Close menu" data-drawer-close>
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <nav class="nav-mobile flex-1 overflow-y-auto px-5 py-6" aria-label="Mobile">
                <?php echo menu('nav-menu-mobile'); ?>
            </nav>
            <?php if (!empty($cta['enabled'])): ?>
            <div class="border-t border-zinc-200 p-5 dark:border-zinc-800">
                <a href="<?php echo htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo $cta['target']; ?>" class="flex w-full items-center justify-center rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white dark:bg-zinc-100 dark:text-zinc-900">
                    <?php echo htmlspecialchars($cta['text'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <main class="mb-content mx-auto w-full max-w-6xl min-w-0 flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <?php if ($showSidebar): ?>
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="mb-content-col min-w-0 lg:col-span-8">
                <?php echo content(); ?>
            </div>
            <aside class="mb-content-col min-w-0 space-y-6 lg:col-span-4 <?php echo $sidebarPos === 'start' ? 'lg:order-first' : ''; ?>">
                <?php include __DIR__ . '/partials/sidebar.html.php'; ?>
            </aside>
        </div>
        <?php else: ?>
            <div class="mb-content-col min-w-0">
                <?php echo content(); ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="mt-auto border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex w-full max-w-6xl min-w-0 flex-col items-center gap-5 px-4 py-10 text-center sm:px-6 lg:px-8">
            <?php if (mb_enabled('footer_social', true)): ?>
            <div class="social-links flex flex-wrap items-center justify-center gap-3">
                <?php echo mb_render_social('justify-center', 'md'); ?>
            </div>
            <?php endif; ?>
            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php
                $customCopy = mb_config('footer_copyright', '');
                if ($customCopy !== '') {
                    echo htmlspecialchars($customCopy, ENT_QUOTES, 'UTF-8');
                } else {
                    echo copyright();
                }
                ?>
            </div>
        </div>
    </footer>
</div>

<!-- Search modal -->
<div class="search-modal fixed inset-0 z-50 hidden items-start justify-center bg-zinc-950/50 p-4 pt-[15vh] backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Search">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center gap-3 border-b border-zinc-100 px-4 py-3 dark:border-zinc-800">
            <i data-lucide="search" class="h-5 w-5 shrink-0 text-zinc-400"></i>
            <form role="search" class="w-full" action="<?php echo site_url(); ?>" method="get">
                <input name="search" type="search" class="h-10 w-full bg-transparent text-zinc-900 placeholder:text-zinc-400 focus:outline-none dark:text-zinc-100" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off" data-search-input>
            </form>
            <kbd class="hidden rounded border border-zinc-200 px-1.5 py-0.5 text-[10px] font-medium text-zinc-400 dark:border-zinc-700 sm:inline">ESC</kbd>
            <button type="button" class="search-close shrink-0 rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800" aria-label="Close">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <p class="px-4 py-3 text-xs text-zinc-400">Press <kbd class="rounded bg-zinc-100 px-1 dark:bg-zinc-800">⌘</kbd>/<kbd class="rounded bg-zinc-100 px-1 dark:bg-zinc-800">Ctrl</kbd>+<kbd class="rounded bg-zinc-100 px-1 dark:bg-zinc-800">K</kbd> to open search</p>
    </div>
</div>

<button type="button" id="scroll-top" class="fixed bottom-6 z-30 hidden h-10 w-10 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white end-6" aria-label="Back to top">
    <i data-lucide="arrow-up" class="h-4 w-4"></i>
</button>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="<?php echo mb_asset('js/app.js'); ?>"></script>
<script>if (window.lucide) lucide.createIcons();</script>
<?php if (analytics()): ?><?php echo analytics(); ?><?php endif; ?>
</body>
</html>
