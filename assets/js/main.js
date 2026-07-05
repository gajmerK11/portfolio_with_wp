/**
 * Portfolio theme — scroll interactions.
 *
 * 1. Fixed sidebar (CSS handles position; JS only manages state).
 * 2. Active nav item follows the section currently in view.
 * 3. Divider line grows along the sidebar/content border on scroll.
 *
 * Depends on: gsap, ScrollTrigger.
 */
(function () {
	'use strict';

	function init() {
		if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
			return;
		}
		gsap.registerPlugin(ScrollTrigger);

		setupDividerLine();
		setupActiveNav();
	}

	/**
	 * Grow the vertical accent line as the page scrolls down,
	 * shrink it back toward the top. Scrubbed to scroll progress.
	 */
	function setupDividerLine() {
		var line = document.getElementById('divider-line');
		if (!line) {
			return;
		}

		gsap.fromTo(
			line,
			{ scaleY: 0 },
			{
				scaleY: 1,
				ease: 'none',
				scrollTrigger: {
					trigger: document.body,
					start: 'top top',
					end: 'bottom bottom',
					scrub: true,
				},
			}
		);
	}

	/**
	 * Toggle the active nav item based on which section holds the
	 * viewport center. Works for any number of [data-section] blocks.
	 */
	function setupActiveNav() {
		var sections = document.querySelectorAll('[data-section]');
		var navItems = document.querySelectorAll('.nav-item[data-target]');
		if (!sections.length || !navItems.length) {
			return;
		}

		function setActive(target) {
			navItems.forEach(function (item) {
				item.classList.toggle('is-active', item.dataset.target === target);
			});
		}

		sections.forEach(function (section) {
			ScrollTrigger.create({
				trigger: section,
				start: 'top center',
				end: 'bottom center',
				onToggle: function (self) {
					if (self.isActive) {
						setActive(section.dataset.section);
					}
				},
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
