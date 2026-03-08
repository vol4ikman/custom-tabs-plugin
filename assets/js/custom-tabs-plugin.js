(function () {
	"use strict";

	function activateTab(container, nextTab, setFocus) {
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

		if (!tabs.length) {
			return;
		}

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

	document.addEventListener("DOMContentLoaded", function () {
		var tabContainers = document.querySelectorAll("[data-ctp-tabs]");

		tabContainers.forEach(function (container) {
			initTabs(container);
		});
	});
})();
