(function () {
	function toggle(className) {
		document.body.classList.toggle(className);
	}

	var menuOpen = document.querySelector('.menu-open');
	var menuClose = document.querySelector('.menu-close');
	var menuBackdrop = document.querySelector('[data-menu-backdrop]');

	if (menuOpen) {
		menuOpen.addEventListener('click', function () {
			toggle('is-menu-open');
		});
	}
	if (menuClose) {
		menuClose.addEventListener('click', function () {
			document.body.classList.remove('is-menu-open');
		});
	}
	if (menuBackdrop) {
		menuBackdrop.addEventListener('click', function () {
			document.body.classList.remove('is-menu-open');
		});
	}

	var searchOpen = document.querySelector('.search-open');
	var searchClose = document.querySelector('.search-close');
	if (searchOpen) {
		searchOpen.addEventListener('click', function () {
			toggle('is-search-open');
			var input = document.querySelector('.search-form input[name="search"]');
			if (input && document.body.classList.contains('is-search-open')) {
				setTimeout(function () { input.focus(); }, 50);
			}
		});
	}
	if (searchClose) {
		searchClose.addEventListener('click', function () {
			document.body.classList.remove('is-search-open');
		});
	}

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			document.body.classList.remove('is-menu-open', 'is-search-open');
		}
	});

	var scrollUp = document.getElementById('scroll-up');
	if (scrollUp) {
		window.addEventListener('scroll', function () {
			scrollUp.style.display = window.scrollY > 280 ? 'flex' : 'none';
		}, { passive: true });

		scrollUp.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}
})();
