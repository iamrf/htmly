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
      var list = document.createElement('ul');
      list.className = 'mb-toc-list';
      list.setAttribute('role', 'list');
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
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = '#' + h.id;
        a.textContent = h.textContent;
        a.className = h.tagName === 'H3' ? 'toc-h3' : 'toc-h2';
        li.appendChild(a);
        list.appendChild(li);
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

  /* ---------- Local comments ---------- */
  (function initComments() {
    var rootEl = document.getElementById('comments');
    if (!rootEl || !rootEl.classList.contains('mb-comments')) return;

    var messages = {
      comment_submission_success: rootEl.getAttribute('data-msg-success') || 'Your comment has been posted successfully!',
      comment_submission_moderation: rootEl.getAttribute('data-msg-moderation') || 'Your comment has been submitted and is awaiting moderation.',
      comment_submission_error: rootEl.getAttribute('data-msg-error') || 'There was an error submitting your comment. Please try again.',
      comment_submission_error_shortname: rootEl.getAttribute('data-msg-shortname') || 'Name is required and must be at least 2 characters.',
      comment_submission_error_email: rootEl.getAttribute('data-msg-email') || 'Valid email is required.',
      comment_submission_error_short: rootEl.getAttribute('data-msg-short') || 'Comment is required and must be at least 3 characters.',
      comment_submission_error_spam: rootEl.getAttribute('data-msg-spam') || 'SPAM detected!'
    };

    function stampAntispam(scope) {
      var ts = Math.floor(Date.now() / 1000);
      (scope || document).querySelectorAll('[data-mb-antispam], [name="company"]').forEach(function (field) {
        if (field.closest && field.closest('.mb-comments, #comments')) {
          field.value = String(ts);
        }
      });
    }

    function refreshIcons() {
      if (window.lucide) lucide.createIcons();
    }

    function closeAllReplies() {
      rootEl.querySelectorAll('.mb-comment__reply-slot').forEach(function (slot) {
        slot.hidden = true;
        slot.innerHTML = '';
      });
      rootEl.querySelectorAll('[data-mb-reply]').forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'false');
      });
    }

    function showReply(commentId, trigger) {
      var slot = document.getElementById('mb-reply-slot-' + commentId);
      var template = document.getElementById('mb-reply-form-template');
      if (!slot || !template) return;

      var alreadyOpen = trigger.getAttribute('aria-expanded') === 'true';
      closeAllReplies();
      if (alreadyOpen) return;

      var urlpath = rootEl.getAttribute('data-comments-url') || '';
      var frag = template.content.cloneNode(true);
      var form = frag.querySelector('form');
      if (!form) return;

      var uid = 'mb-reply-' + commentId.replace(/[^a-zA-Z0-9_\-.]/g, '');
      form.id = uid;

      var urlInput = form.querySelector('[name="url"]');
      var parentInput = form.querySelector('[name="parent_id"]');
      if (urlInput) urlInput.value = urlpath;
      if (parentInput) parentInput.value = commentId;

      form.querySelectorAll('[id]').forEach(function (el) {
        var oldId = el.id;
        var nextId = oldId.replace(/mb-reply-form-__PARENT__/g, uid).replace(/__PARENT__/g, commentId);
        el.id = nextId;
        form.querySelectorAll('label[for="' + oldId + '"]').forEach(function (lab) {
          lab.setAttribute('for', nextId);
        });
      });

      var titleText = slot.getAttribute('data-reply-title') || '';
      if (titleText) {
        var title = document.createElement('p');
        title.className = 'mb-comment__reply-slot-title';
        title.textContent = titleText;
        slot.appendChild(title);
      }
      slot.appendChild(frag);
      slot.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');

      stampAntispam(slot);
      refreshIcons();

      var focusEl = slot.querySelector('textarea');
      if (focusEl) {
        try { focusEl.focus(); } catch (e) { /* ignore */ }
      }
    }

    rootEl.addEventListener('click', function (e) {
      var replyBtn = e.target.closest('[data-mb-reply]');
      if (replyBtn && rootEl.contains(replyBtn)) {
        e.preventDefault();
        showReply(replyBtn.getAttribute('data-mb-reply'), replyBtn);
        return;
      }
      var cancelBtn = e.target.closest('[data-mb-cancel-reply]');
      if (cancelBtn && rootEl.contains(cancelBtn)) {
        e.preventDefault();
        closeAllReplies();
      }
    });

    function showStatusFromHash() {
      var hash = window.location.hash || '';
      if (hash.indexOf('#comment-status') !== 0) return;
      var parts = hash.split('+');
      if (parts.length < 2) return;
      var key = decodeURIComponent(parts[1]);
      var alertEl = document.getElementById('mb-comment-alert');
      if (!alertEl || !messages[key]) return;

      alertEl.hidden = false;
      alertEl.textContent = messages[key];
      alertEl.className = 'mb-comments__alert';
      if (key.indexOf('error') !== -1) {
        alertEl.classList.add('mb-comments__alert--error');
      } else if (key.indexOf('success') !== -1) {
        alertEl.classList.add('mb-comments__alert--success');
      } else if (key.indexOf('moderation') !== -1) {
        alertEl.classList.add('mb-comments__alert--warning');
      }

      try {
        rootEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } catch (err) {
        rootEl.scrollIntoView(true);
      }
    }

    stampAntispam(rootEl);
    showStatusFromHash();
    window.addEventListener('hashchange', showStatusFromHash);
  })();

  /* ---------- Re-init Lucide after DOM tweaks ---------- */
  if (window.lucide) {
    lucide.createIcons();
  }
})();
