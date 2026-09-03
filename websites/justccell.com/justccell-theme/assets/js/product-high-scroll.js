/**
 * Product highlight section — sticky scroll-scrub (ccell.com .high pattern).
 * One viewport segment per panel; scroll position drives the active slide.
 * Rank Ray — https://rankray.com
 */
(() => {
  const root = document.querySelector("[data-sticky-features]");
  if (!(root instanceof HTMLElement)) {
    return;
  }

  const panels = [...root.querySelectorAll("[data-feature-panel]")];
  const dots = [...root.querySelectorAll("[data-feature-dot]")];
  const count = panels.length;
  if (!count) {
    return;
  }

  /** Scroll distance between panel changes (ccell ~110vh per step). */
  const SLIDE_VH = 110;
  /** Sticky viewport + (n−1) transition steps — no extra runway after the last panel. */
  const containerHeightVh = () => 100 + (count - 1) * SLIDE_VH;
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  let activeIndex = 0;
  let rafId = 0;
  let programmaticUntil = 0;

  const adminBarPx = () => {
    const raw = getComputedStyle(document.documentElement).getPropertyValue("--jc-admin-bar").trim();
    const n = parseFloat(raw);
    return Number.isFinite(n) ? n : 0;
  };

  /** Scroll distance that advances one panel (matches container segment height). */
  const segmentPx = () => window.innerHeight * (SLIDE_VH / 100);

  const sectionTop = () => root.getBoundingClientRect().top + window.scrollY;

  const maxOffsetPx = () => (count - 1) * segmentPx();

  const applyHeight = () => {
    root.style.height = `${containerHeightVh()}vh`;
  };

  const isInSection = () => {
    const rect = root.getBoundingClientRect();
    const topInset = adminBarPx();
    return rect.bottom > topInset + 8 && rect.top < window.innerHeight - 8;
  };

  const indexFromScroll = () => {
    let offset = window.scrollY - sectionTop();
    if (offset <= 0) {
      return 0;
    }
    const max = maxOffsetPx();
    if (offset >= max) {
      return count - 1;
    }
    const step = segmentPx();
    const idx = Math.floor(offset / step + 1e-5);
    return Math.max(0, Math.min(count - 1, idx));
  };

  const paint = (index) => {
    const next = Math.max(0, Math.min(count - 1, index));
    if (next === activeIndex) {
      return next;
    }
    activeIndex = next;
    panels.forEach((panel, i) => panel.classList.toggle("is-on", i === activeIndex));
    dots.forEach((dot, i) => dot.classList.toggle("is-on", i === activeIndex));
    return activeIndex;
  };

  const syncFromScroll = () => {
    if (Date.now() < programmaticUntil) {
      return;
    }
    paint(indexFromScroll());
  };

  const scheduleSync = () => {
    if (rafId) {
      return;
    }
    rafId = window.requestAnimationFrame(() => {
      rafId = 0;
      syncFromScroll();
    });
  };

  const scrollToIndex = (index, smooth = !reduceMotion) => {
    const target = Math.max(0, Math.min(count - 1, index));
    paint(target);
    const top = sectionTop() + target * segmentPx();
    programmaticUntil = Date.now() + (smooth ? 720 : 48);
    window.scrollTo({ top, behavior: smooth ? "smooth" : "auto" });
    window.setTimeout(() => {
      programmaticUntil = 0;
      paint(target);
    }, smooth ? 740 : 50);
  };

  dots.forEach((dot, i) => {
    dot.addEventListener("click", () => scrollToIndex(i));
  });

  applyHeight();
  paint(0);
  syncFromScroll();

  window.addEventListener("scroll", scheduleSync, { passive: true });
  window.addEventListener("resize", () => {
    applyHeight();
    scrollToIndex(activeIndex, false);
  });

  if ("onscrollend" in window) {
    window.addEventListener(
      "scrollend",
      () => {
        programmaticUntil = 0;
        paint(indexFromScroll());
      },
      { passive: true }
    );
  }

  // Keep index accurate when returning to the tab after inertia scroll.
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "visible" && isInSection()) {
      scheduleSync();
    }
  });
})();
