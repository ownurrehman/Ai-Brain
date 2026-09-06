/**
 * Catalog category tabs — switch hero banners and product panels without a page load.
 * Updates the address bar to each tab's real permalink (no hash fragments).
 */
(function () {
  const root = document.querySelector("[data-catalog-panels]");
  if (!root) {
    return;
  }

  const nav = root.previousElementSibling;
  if (!nav || !nav.classList.contains("c-tabs")) {
    return;
  }

  const heroRoot = document.querySelector("[data-catalog-heroes]");
  const heroes = heroRoot
    ? Array.from(heroRoot.querySelectorAll("[data-catalog-hero]"))
    : [];
  const heroMap = new Map(
    heroes.map((hero) => [hero.getAttribute("data-catalog-hero"), hero])
  );

  const tabs = Array.from(nav.querySelectorAll("[data-catalog-tab]"));
  const panels = Array.from(root.querySelectorAll("[data-catalog-panel]"));
  if (!tabs.length || !panels.length) {
    return;
  }

  const panelMap = new Map(
    panels.map((panel) => [panel.getAttribute("data-catalog-panel"), panel])
  );

  function normalizePath(pathname) {
    const path = (pathname || "/").replace(/\/+$/, "");
    return path === "" ? "/" : path;
  }

  function tabForPanelKey(panelKey) {
    return tabs.find((tab) => tab.getAttribute("data-catalog-tab") === panelKey);
  }

  function tabForPath(pathname) {
    const target = normalizePath(pathname);
    return tabs.find((tab) => {
      try {
        return (
          normalizePath(new URL(tab.href, window.location.origin).pathname) ===
          target
        );
      } catch {
        return false;
      }
    });
  }

  function activateHero(panelKey) {
    if (!heroMap.size) {
      return;
    }

    heroes.forEach((hero) => {
      const on = hero.getAttribute("data-catalog-hero") === panelKey;
      hero.classList.toggle("is-on", on);
      hero.hidden = !on;
    });
  }

  function activate(panelKey, updateHistory) {
    const panel = panelMap.get(panelKey);
    const tab = tabForPanelKey(panelKey);
    if (!panel || !tab) {
      return;
    }

    const scrollY = window.scrollY;

    tabs.forEach((item) => {
      const on = item === tab;
      item.classList.toggle("is-on", on);
      item.setAttribute("aria-selected", on ? "true" : "false");
    });

    panels.forEach((item) => {
      const on = item === panel;
      item.classList.toggle("is-on", on);
      item.hidden = !on;
    });

    activateHero(panelKey);

    if (updateHistory) {
      const nextUrl = tab.href;
      const state = { catalogTab: panelKey };
      const current = normalizePath(window.location.pathname);
      const next = normalizePath(
        new URL(nextUrl, window.location.origin).pathname
      );

      if (current !== next) {
        window.history.pushState(state, "", nextUrl);
      } else if (window.location.hash) {
        window.history.replaceState(state, "", nextUrl);
      }
    }

    window.scrollTo(0, scrollY);
  }

  tabs.forEach((tab) => {
    tab.addEventListener("click", (event) => {
      event.preventDefault();
      activate(tab.getAttribute("data-catalog-tab"), true);
    });
  });

  window.addEventListener("popstate", () => {
    const match = tabForPath(window.location.pathname);
    if (match) {
      activate(match.getAttribute("data-catalog-tab"), false);
    }
  });

  const legacyHash = window.location.hash.replace(/^#/, "");
  if (legacyHash && panelMap.has(legacyHash)) {
    const tab = tabForPanelKey(legacyHash);
    activate(legacyHash, false);
    if (tab) {
      window.history.replaceState(
        { catalogTab: legacyHash },
        "",
        tab.href
      );
    }
    return;
  }

  const fromPath = tabForPath(window.location.pathname);
  if (fromPath) {
    activate(fromPath.getAttribute("data-catalog-tab"), false);
    return;
  }

  const initial = tabs.find((tab) => tab.classList.contains("is-on"));
  if (initial) {
    activate(initial.getAttribute("data-catalog-tab"), false);
  }
})();
