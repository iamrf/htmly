<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth" id="html-id">
<head>
    <?php echo head_contents(); ?>
    <?php echo $metatags; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#1A2332',
                        mist: '#F3F6F5',
                        paper: '#FAFBFA',
                        sand: '#E4E9E7',
                        teal: {
                            DEFAULT: '#0E7C7B',
                            soft: '#1A9A98',
                            deep: '#0A5C5B',
                            faint: '#D8EFEE'
                        },
                        mute: '#5C6670'
                    },
                    fontFamily: {
                        vazir: ['Vazirmatn', 'Tahoma', 'sans-serif']
                    },
                    maxWidth: {
                        prose: '42rem'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?php echo theme_path(); ?>css/style.css">
</head>
<body class="bg-mist font-vazir text-ink antialiased">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>

<div class="blogfa-bg" aria-hidden="true"></div>

<div class="relative min-h-screen">
    <header class="border-b border-sand/80 bg-paper/80 backdrop-blur-md sticky top-0 z-40">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="<?php echo site_url(); ?>" class="group flex items-center gap-3 min-w-0">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal text-white text-lg font-bold shadow-sm transition group-hover:bg-teal-deep">ب</span>
                <span class="min-w-0">
                    <span class="block truncate text-lg font-bold tracking-tight text-ink sm:text-xl"><?php echo blog_title(); ?></span>
                    <?php if (blog_tagline()): ?>
                    <span class="hidden truncate text-xs text-mute sm:block"><?php echo blog_tagline(); ?></span>
                    <?php endif; ?>
                </span>
            </a>

            <div class="flex items-center gap-1 sm:gap-3">
                <nav class="nav-top hidden items-center gap-1 md:flex" aria-label="منوی اصلی">
                    <?php echo menu('nav-menu'); ?>
                </nav>

                <button type="button" class="search-open inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute transition hover:bg-sand hover:text-ink" aria-label="جستجو">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </button>

                <button type="button" class="menu-open inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute transition hover:bg-sand hover:text-ink md:hidden" aria-label="منو" id="menu-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="menu-mobile" role="dialog" aria-modal="true" tabindex="-1">
        <div class="fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm"></div>
        <div class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xs flex-col bg-paper shadow-2xl">
            <div class="flex items-center justify-between border-b border-sand px-5 py-4">
                <span class="font-bold text-ink"><?php echo blog_title(); ?></span>
                <button type="button" class="menu-close inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute hover:bg-sand" aria-label="بستن منو">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <nav class="nav-mobile flex-1 overflow-y-auto px-5 py-6" aria-label="منوی موبایل">
                <?php echo menu('nav-menu-mobile'); ?>
            </nav>
        </div>
    </div>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <?php echo content(); ?>
    </main>

    <footer class="mt-8 border-t border-sand bg-paper">
        <div class="mx-auto flex max-w-5xl flex-col items-center gap-4 px-4 py-10 text-center sm:px-6 lg:px-8">
            <div class="social-links flex flex-wrap items-center justify-center gap-3">
                <?php echo social(); ?>
            </div>
            <div class="text-sm text-mute">
                <?php echo copyright(); ?>
            </div>
            <p class="text-xs text-mute/70">قالب بلاگفا · فونت وزیرمتن</p>
        </div>
    </footer>
</div>

<div class="search-form fixed inset-0 z-50 hidden items-start justify-center bg-ink/40 p-4 pt-[18vh] backdrop-blur-sm">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-sand bg-paper shadow-2xl">
        <div class="flex items-center gap-3 px-4 py-3">
            <svg class="h-5 w-5 shrink-0 text-mute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <form role="search" class="w-full">
                <input name="search" type="search" class="h-10 w-full bg-transparent text-ink placeholder:text-mute/60 focus:outline-none" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off" autofocus>
            </form>
            <button type="button" class="search-close shrink-0 rounded-lg px-2 py-1 text-sm text-mute hover:bg-sand hover:text-ink" aria-label="بستن">بستن</button>
        </div>
    </div>
</div>

<button type="button" id="scroll-up" class="fixed bottom-6 left-6 z-30 hidden h-11 w-11 items-center justify-center rounded-full bg-teal text-white shadow-lg transition hover:bg-teal-deep" aria-label="بازگشت به بالا">
    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
</button>

<script src="<?php echo theme_path(); ?>js/functions.js"></script>
<?php if (analytics()): ?><?php echo analytics(); ?><?php endif; ?>
</body>
</html>
