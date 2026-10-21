<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language(); ?>" class="scroll-smooth" id="html-id">
<head>
    <?php echo head_contents(); ?>
    <?php echo $metatags; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#18181B',
                        canvas: '#F4F4F5',
                        paper: '#FFFFFF',
                        line: '#E4E4E7',
                        mute: '#71717A',
                        sky: {
                            DEFAULT: '#0284C8',
                            soft: '#0EA5E9',
                            deep: '#0369A1',
                            faint: '#E0F2FE'
                        }
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'system-ui', 'sans-serif']
                    },
                    maxWidth: {
                        prose: '42rem',
                        site: '68rem'
                    },
                    screens: {
                        xs: '420px'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?php echo theme_path(); ?>css/style.css">
</head>
<body class="bg-canvas font-outfit text-ink antialiased">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>

<div class="blogen-bg" aria-hidden="true"></div>

<div class="relative flex min-h-screen flex-col">
    <header class="sticky top-0 z-40 border-b border-line/80 bg-paper/85 backdrop-blur-md supports-[backdrop-filter]:bg-paper/70">
        <div class="mx-auto flex max-w-site items-center justify-between gap-3 px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="<?php echo site_url(); ?>" class="group flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky text-sm font-bold text-white shadow-sm transition group-hover:bg-sky-deep sm:h-10 sm:w-10 sm:text-base">B</span>
                <span class="min-w-0">
                    <span class="block truncate text-base font-bold tracking-tight text-ink sm:text-lg"><?php echo blog_title(); ?></span>
                    <?php if (blog_tagline()): ?>
                    <span class="hidden truncate text-xs text-mute sm:block"><?php echo blog_tagline(); ?></span>
                    <?php endif; ?>
                </span>
            </a>

            <div class="flex items-center gap-0.5 sm:gap-2">
                <nav class="nav-top hidden items-center md:flex" aria-label="Main navigation">
                    <?php echo menu('nav-menu'); ?>
                </nav>

                <button type="button" class="search-open inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute transition hover:bg-canvas hover:text-ink" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </button>

                <button type="button" class="menu-open inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute transition hover:bg-canvas hover:text-ink md:hidden" aria-label="Open menu" id="menu-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="menu-mobile" role="dialog" aria-modal="true" tabindex="-1">
        <div class="fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm" data-menu-backdrop></div>
        <div class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xs flex-col bg-paper shadow-2xl">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <span class="font-bold text-ink"><?php echo blog_title(); ?></span>
                <button type="button" class="menu-close inline-flex h-10 w-10 items-center justify-center rounded-xl text-mute hover:bg-canvas" aria-label="Close menu">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <nav class="nav-mobile flex-1 overflow-y-auto overscroll-contain px-5 py-6" aria-label="Mobile navigation">
                <?php echo menu('nav-menu-mobile'); ?>
            </nav>
        </div>
    </div>

    <main class="mx-auto w-full max-w-site flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
        <?php echo content(); ?>
    </main>

    <footer class="mt-auto border-t border-line bg-paper">
        <div class="mx-auto flex max-w-site flex-col items-center gap-4 px-4 py-10 text-center sm:px-6 lg:px-8">
            <div class="social-links flex flex-wrap items-center justify-center gap-2.5">
                <?php echo social(); ?>
            </div>
            <div class="text-sm text-mute">
                <?php echo copyright(); ?>
            </div>
            <p class="text-xs text-mute/70">Blogen theme</p>
        </div>
    </footer>
</div>

<div class="search-form fixed inset-0 z-50 hidden items-start justify-center bg-ink/40 p-4 pt-[16vh] backdrop-blur-sm sm:pt-[18vh]">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-line bg-paper shadow-2xl">
        <div class="flex items-center gap-3 px-4 py-3">
            <svg class="h-5 w-5 shrink-0 text-mute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <form role="search" class="w-full">
                <input name="search" type="search" class="h-11 w-full bg-transparent text-base text-ink placeholder:text-mute/60 focus:outline-none sm:h-10 sm:text-sm" placeholder="<?php echo i18n('type_to_search'); ?>" autocomplete="off">
            </form>
            <button type="button" class="search-close shrink-0 rounded-lg px-2.5 py-1.5 text-sm text-mute hover:bg-canvas hover:text-ink" aria-label="Close search">Close</button>
        </div>
    </div>
</div>

<button type="button" id="scroll-up" class="fixed bottom-5 right-5 z-30 hidden h-11 w-11 items-center justify-center rounded-full bg-sky text-white shadow-lg transition hover:bg-sky-deep sm:bottom-6 sm:right-6" aria-label="Back to top">
    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
</button>

<script src="<?php echo theme_path(); ?>js/functions.js"></script>
<?php if (analytics()): ?><?php echo analytics(); ?><?php endif; ?>
</body>
</html>
