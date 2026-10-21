/**
 * Modern Bilingual — theme interactions
 * Dark mode, search (⌘K), mobile drawer, TOC, reading progress, referrer links
 */
(function () {
  'use strict';

  var root = document.documentElement;
  var body = document.body;

  /* ---------- Dark / Light mode ---------- */
  function getStoredTheme() {
    try {
      return localStorage.getItem('mb-theme');
    } catch (e) {
      return null;
    }
  }

  function setStoredTheme(value) {
    try {
      localStorage.setItem('mb-theme', value);
      localStorage.setItem('mb-theme-default', root.getAttribute('data-default-theme') || 'system');
    } catch (e) { /* ignore */ }
  }

  function resolveTheme(mode) {
    if (mode === 'dark' || mode === 'light') return mode;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(mode) {
    var resolved = resolveTheme(mode);
    root.classList.toggle('dark', resolved === 'dark');
  }

  function activeMode() {
    return getStoredTheme() || root.getAttribute('data-default-theme') || 'system';
  }

  function cycleTheme() {
    var resolved = resolveTheme(activeMode());
    var next = resolved === 'dark' ? 'light' : 'dark';
    setStoredTheme(next);
    applyTheme(next);
    if (window.lucide) lucide.createIcons();
  }

  var themeToggle = document.getElementById('theme-toggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', cycleTheme);
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
    var stored = getStoredTheme();
    if (!stored || stored === 'system') {
      applyTheme(activeMode());
    }
  });

  /* ---------- Mobile drawer ---------- */
  function openMenu() {
    body.classList.add('is-menu-open');
  }

  function closeMenu() {
    body.classList.remove('is-menu-open');
  }

  var menuOpen = document.querySelector('.menu-open');
  var menuCloseEls = document.querySelectorAll('.menu-close, [data-drawer-close]');
  if (menuOpen) menuOpen.addEventListener('click', openMenu);
  menuCloseEls.forEach(function (el) {
    el.addEventListener('click', closeMenu);
  });

  /* ---------- Search modal ---------- */
  function openSearch() {
    body.classList.add('is-search-open');
    var input = document.querySelector('[data-search-input]');
    if (input) {
      setTimeout(function () { input.focus(); }, 40);
    }
  }

  function closeSearch() {
    body.classList.remove('is-search-open');
  }

  var searchOpen = document.querySelector('.search-open');
  var searchClose = document.querySelector('.search-close');
  if (searchOpen) searchOpen.addEventListener('click', openSearch);
  if (searchClose) searchClose.addEventListener('click', closeSearch);

  var searchBackdrop = document.querySelector('.search-modal');
  if (searchBackdrop) {
    searchBackdrop.addEventListener('click', function (e) {
      if (e.target === searchBackdrop) closeSearch();
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeMenu();
      closeSearch();
    }
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      if (body.classList.contains('is-search-open')) {
        closeSearch();
      } else {
        openSearch();
      }
    }
  });

  /* ---------- Scroll to top ---------- */
  var scrollTop = document.getElementById('scroll-top');
  if (scrollTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 320) {
        scrollTop.style.display = 'flex';
      } else {
        scrollTop.style.display = 'none';
      }
    }, { passive: true });

    scrollTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Reading progress ---------- */
  var progress = document.getElementById('reading-progress');
  var article = document.querySelector('[data-article-body]');
  if (progress && article) {
    window.addEventListener('scroll', function () {
      var rect = article.getBoundingClientRect();
      var total = article.offsetHeight - window.innerHeight;
      var scrolled = -rect.top;
      var ratio = total > 0 ? Math.min(1, Math.max(0, scrolled / total)) : 0;
      progress.style.transform = 'scaleX(' + ratio + ')';
    }, { passive: true });
  }

  /* ---------- Table of Contents ---------- */
  function slugify(text) {
    return text
      .toString()
      .trim()
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w\u0600-\u06FF\-]+/g, '')
      .replace(/\-\-+/g, '-')
      .replace(/^-+|-+$/g, '') || 'section';
  }

  var tocNav = document.getElementById('mb-toc');
  var articleBody = document.querySelector('[data-article-body]');
  if (tocNav && articleBody) {
    var headings = articleBody.querySelectorAll('h2, h3');
    if (headings && headings.length > 0) {
      var list = document.createElement('nav');
      list.className = 'mb-toc-list space-y-0.5';
      var used = {};

      headings.forEach(function (h) {
        if (!h.id) {
          var base = slugify(h.textContent);
          var id = base;
          var n = 1;
          while (used[id] || document.getElementById(id)) {
            id = base + '-' + n++;
          }
          used[id] = true;
          h.id = id;
        }
        var a = document.createElement('a');
        a.href = '#' + h.id;
        a.textContent = h.textContent;
        a.className = h.tagName === 'H3' ? 'toc-h3' : 'toc-h2';
        list.appendChild(a);
      });

      tocNav.appendChild(list);
      tocNav.classList.remove('hidden');
      var fallback = document.getElementById('toc-fallback');
      if (fallback) fallback.classList.add('hidden');

      var tocLinks = list.querySelectorAll('a');
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            tocLinks.forEach(function (l) { l.classList.remove('is-active'); });
            var active = list.querySelector('a[href="#' + entry.target.id + '"]');
            if (active) active.classList.add('is-active');
          }
        });
      }, { rootMargin: '-20% 0px -70% 0px' });

      headings.forEach(function (h) { observer.observe(h); });
    }
  }

  /* ---------- Copy link ---------- */
  var copyBtn = document.querySelector('[data-copy-link]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var url = copyBtn.getAttribute('data-copy-link') || window.location.href;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
          flashCopy(copyBtn);
        }).catch(function () {
          fallbackCopy(url, copyBtn);
        });
      } else {
        fallbackCopy(url, copyBtn);
      }
    });
  }

  function flashCopy(btn) {
    var original = btn.getAttribute('data-label') || btn.innerHTML;
    btn.setAttribute('data-label', original);
    btn.innerHTML = '<i data-lucide="check" class="h-4 w-4"></i> <span>Copied</span>';
    if (window.lucide) lucide.createIcons();
    setTimeout(function () {
      btn.innerHTML = original;
      if (window.lucide) lucide.createIcons();
    }, 1600);
  }

  function fallbackCopy(text, btn) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); flashCopy(btn); } catch (e) { /* ignore */ }
    document.body.removeChild(ta);
  }

  /* ---------- External link referrer injection ---------- */
  var referrerParam = body.getAttribute('data-referrer-param') || '';
  if (referrerParam) {
    var host = window.location.hostname;
    var contentRoots = document.querySelectorAll('[data-article-body], .newsletter-promo, [data-promo-links]');
    contentRoots.forEach(function (rootEl) {
      rootEl.querySelectorAll('a[href]').forEach(function (a) {
        try {
          var href = a.getAttribute('href');
          if (!href || href.charAt(0) === '#' || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
            return;
          }
          var url = new URL(href, window.location.origin);
          if (url.hostname === host) return;
          // Already has param?
          var pairs = referrerParam.split('&');
          var skip = false;
          pairs.forEach(function (pair) {
            var key = pair.split('=')[0];
            if (key && url.searchParams.has(key)) skip = true;
          });
          if (skip) return;
          var sep = url.search ? '&' : '?';
          a.setAttribute('href', url.toString().split('#')[0] + sep + referrerParam + (url.hash || ''));
          if (!a.getAttribute('rel')) {
            a.setAttribute('rel', 'noopener noreferrer');
          }
        } catch (err) { /* invalid URL */ }
      });
    });
  }

  /* ---------- Re-init Lucide after DOM tweaks ---------- */
  if (window.lucide) {
    lucide.createIcons();
  }
})();
