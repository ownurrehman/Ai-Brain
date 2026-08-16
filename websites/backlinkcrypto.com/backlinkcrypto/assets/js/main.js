(() => {
  const toggle = document.querySelector("[data-bc-menu]");
  const panel = document.getElementById("bc-mobile-nav");
  if (toggle && panel) {
    toggle.addEventListener("click", () => {
      const open = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", open ? "false" : "true");
      if (open) {
        panel.setAttribute("hidden", "");
      } else {
        panel.removeAttribute("hidden");
      }
    });

    panel.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        toggle.setAttribute("aria-expanded", "false");
        panel.setAttribute("hidden", "");
      });
    });
  }

  const header = document.querySelector("[data-bc-header]");
  if (header) {
    const onScroll = () => {
      header.classList.toggle("is-scrolled", window.scrollY > 8);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  // Soft reveal for sections
  const reveal = document.querySelectorAll("[data-bc-reveal]");
  if (reveal.length && "IntersectionObserver" in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-in");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0, rootMargin: "0px 0px -10% 0px" }
    );
    reveal.forEach((el) => {
      // Already on screen (hash jump / short viewport) — show immediately
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        el.classList.add("is-in");
      } else {
        io.observe(el);
      }
    });
  } else {
    reveal.forEach((el) => el.classList.add("is-in"));
  }

  // Rows stay visible (no opacity gate) — keep class for optional polish
  document.querySelectorAll(".bc-row--anim").forEach((row) => row.classList.add("is-in"));

  // Product page quantity steppers
  document.querySelectorAll("[data-bc-qty]").forEach((wrap) => {
    const input = wrap.querySelector("input.qty");
    if (!input) return;
    const min = Number(input.min || 1);
    const maxRaw = input.max;
    const max = maxRaw === "" || maxRaw == null ? null : Number(maxRaw);

    const clamp = (n) => {
      let v = Number.isFinite(n) ? n : min;
      if (v < min) v = min;
      if (max != null && Number.isFinite(max) && v > max) v = max;
      return v;
    };

    wrap.querySelector("[data-bc-qty-minus]")?.addEventListener("click", () => {
      input.value = String(clamp(Number(input.value || min) - 1));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });
    wrap.querySelector("[data-bc-qty-plus]")?.addEventListener("click", () => {
      input.value = String(clamp(Number(input.value || min) + 1));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });
  });

  // Medium-like reading progress on single blog posts
  const progressRoot = document.querySelector("[data-bc-read-progress]");
  const progressBar = document.querySelector("[data-bc-read-bar]");
  const articleBody = document.querySelector("[data-bc-article-body]");
  if (progressRoot && progressBar && articleBody) {
    const update = () => {
      const rect = articleBody.getBoundingClientRect();
      const total = articleBody.scrollHeight - window.innerHeight;
      const scrolled = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
      const pct = total <= 0 ? 100 : Math.min(100, Math.max(0, (scrolled / total) * 100));
      progressBar.style.width = `${pct}%`;
      progressRoot.classList.toggle("is-complete", pct >= 99.5);
    };
    update();
    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
  }

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Count-up stats
  const counters = document.querySelectorAll("[data-bc-count-up]");
  if (counters.length && "IntersectionObserver" in window) {
    const runCount = (el) => {
      const to = Number(el.getAttribute("data-bc-to") || 0);
      const suffix = el.getAttribute("data-bc-suffix") || "";
      if (reduceMotion || !Number.isFinite(to) || to <= 0) {
        el.textContent = `${to}${suffix}`;
        return;
      }
      const duration = 900;
      const start = performance.now();
      const tick = (now) => {
        const t = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - t, 3);
        el.textContent = `${Math.round(to * eased)}${suffix}`;
        if (t < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };
    const cio = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          runCount(entry.target);
          cio.unobserve(entry.target);
        });
      },
      { threshold: 0.4 }
    );
    counters.forEach((el) => cio.observe(el));
  }

  // How-it-works step highlight on scroll
  const steps = document.querySelectorAll("[data-bc-step]");
  if (steps.length && "IntersectionObserver" in window) {
    const sio = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-active");
          }
        });
      },
      { threshold: 0.45 }
    );
    steps.forEach((el) => sio.observe(el));
  } else {
    steps.forEach((el) => el.classList.add("is-active"));
  }

  // Delivery timeline stagger
  const deliveryItems = document.querySelectorAll(".bc-delivery__timeline > li");
  if (deliveryItems.length && "IntersectionObserver" in window) {
    const dio = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-in");
          dio.unobserve(entry.target);
        });
      },
      { threshold: 0.3 }
    );
    deliveryItems.forEach((el, i) => {
      el.style.setProperty("--bc-step-delay", `${i * 80}ms`);
      dio.observe(el);
    });
  } else {
    deliveryItems.forEach((el) => el.classList.add("is-in"));
  }
})();
