(function () {
	"use strict";

	var MOBILE_BREAKPOINT = 1279;

	function isMobileViewport() {
		return window.matchMedia("(max-width: " + MOBILE_BREAKPOINT + "px)").matches;
	}

	function activateTab(container, nextTab, setFocus) {
		if ("mobile" === container.getAttribute("data-ctp-mode")) {
			return;
		}

		var tabs = container.querySelectorAll('[role="tab"]');
		var panels = container.querySelectorAll('[role="tabpanel"]');

		tabs.forEach(function (tab) {
			var isActive = tab === nextTab;
			tab.classList.toggle("is-active", isActive);
			tab.setAttribute("aria-selected", isActive ? "true" : "false");
			tab.setAttribute("tabindex", isActive ? "0" : "-1");
		});

		panels.forEach(function (panel) {
			var shouldShow = panel.id === nextTab.getAttribute("aria-controls");
			panel.classList.toggle("is-active", shouldShow);
			panel.hidden = !shouldShow;
		});

		if (setFocus) {
			nextTab.focus();
		}
	}

	function initTabs(container) {
		var tabs = container.querySelectorAll('[role="tab"]');

		if (!tabs.length || "true" === container.getAttribute("data-ctp-desktop-bound")) {
			return;
		}

		container.setAttribute("data-ctp-desktop-bound", "true");

		tabs.forEach(function (tab, index) {
			tab.addEventListener("click", function () {
				activateTab(container, tab, false);
			});

			tab.addEventListener("keydown", function (event) {
				var previous;
				var next;

				if (event.key === "ArrowRight" || event.key === "ArrowDown") {
					event.preventDefault();
					next = tabs[(index + 1) % tabs.length];
					activateTab(container, next, true);
				}

				if (event.key === "ArrowLeft" || event.key === "ArrowUp") {
					event.preventDefault();
					previous = tabs[(index - 1 + tabs.length) % tabs.length];
					activateTab(container, previous, true);
				}

				if (event.key === "Home") {
					event.preventDefault();
					activateTab(container, tabs[0], true);
				}

				if (event.key === "End") {
					event.preventDefault();
					activateTab(container, tabs[tabs.length - 1], true);
				}
			});
		});
	}

	function syncDesktopStateFromIndex(container, index) {
		var tabs = container.querySelectorAll('[role="tab"]');
		var panels = container.querySelectorAll('[role="tabpanel"]');

		tabs.forEach(function (tab, tabIndex) {
			var isActive = tabIndex === index;
			tab.classList.toggle("is-active", isActive);
			tab.setAttribute("aria-selected", isActive ? "true" : "false");
			tab.setAttribute("tabindex", isActive ? "0" : "-1");
		});

		panels.forEach(function (panel, panelIndex) {
			var shouldShow = panelIndex === index;
			panel.classList.toggle("is-active", shouldShow);
			panel.hidden = !shouldShow;
		});
	}

	function destroySlickIfNeeded($el) {
		if ($el && $el.length && $el.hasClass("slick-initialized")) {
			$el.slick("unslick");
		}
	}

	function initMobileSlick(container) {
		if ("undefined" === typeof window.jQuery || "undefined" === typeof window.jQuery.fn || "undefined" === typeof window.jQuery.fn.slick) {
			return;
		}

		var $ = window.jQuery;
		var $container = $(container);
		var $tablist = $container.find(".ctp__tablist");
		var $panels = $container.find(".ctp__panels");

		if (!$tablist.length || !$panels.length) {
			return;
		}

		if ($tablist.find(".ctp__tab").length < 2) {
			container.setAttribute("data-ctp-mode", "desktop");
			return;
		}

		destroySlickIfNeeded($tablist);
		destroySlickIfNeeded($panels);

		container.setAttribute("data-ctp-mode", "mobile");

		$panels.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: false,
			adaptiveHeight: true,
			swipe: true,
			draggable: true,
			asNavFor: $tablist,
			infinite: false,
		});

		$tablist.slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: false,
			focusOnSelect: true,
			swipeToSlide: true,
			variableWidth: true,
			asNavFor: $panels,
			infinite: false,
		});

		$panels.on("init reInit afterChange", function (event, slick, currentSlide) {
			var index = "number" === typeof currentSlide ? currentSlide : 0;
			syncDesktopStateFromIndex(container, index);
		});

		$tablist.on("afterChange", function (event, slick, currentSlide) {
			syncDesktopStateFromIndex(container, currentSlide || 0);
		});

		syncDesktopStateFromIndex(container, 0);
	}

	function initDesktopMode(container) {
		if ("undefined" !== typeof window.jQuery) {
			var $ = window.jQuery;
			destroySlickIfNeeded($(container).find(".ctp__tablist"));
			destroySlickIfNeeded($(container).find(".ctp__panels"));
		}

		container.setAttribute("data-ctp-mode", "desktop");
		initTabs(container);
		syncDesktopStateFromIndex(container, 0);
	}

	function applyMode(container) {
		var currentMode = container.getAttribute("data-ctp-mode");

		if (isMobileViewport()) {
			if ("mobile" === currentMode) {
				return;
			}

			initMobileSlick(container);
			return;
		}

		if ("desktop" === currentMode) {
			return;
		}

		initDesktopMode(container);
	}

	document.addEventListener("DOMContentLoaded", function () {
		var tabContainers = document.querySelectorAll("[data-ctp-tabs]");

		tabContainers.forEach(function (container) {
			applyMode(container);

			window.addEventListener("resize", function () {
				applyMode(container);
			});
		});
	});
})();
