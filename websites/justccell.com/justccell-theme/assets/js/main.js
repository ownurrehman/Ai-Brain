/**
 * Justccell chrome interactions.
 * Rank Ray — https://rankray.com
 */
(() => {
  const header = document.querySelector("[data-header]");
  const toggle = document.querySelector("[data-nav-toggle]");
  const DRAWER_MAX = 1360;
  let setOpen = () => {};

  if (header && toggle) {
    setOpen = (open) => {
      document.body.classList.toggle("c-open", open);
      header.classList.toggle("is-open", open);
      if (open) {
        header.classList.remove("header-hidden");
      }
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    };
    toggle.addEventListener("click", () => {
      setOpen(!document.body.classList.contains("c-open"));
    });
    toggle.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        setOpen(!document.body.classList.contains("c-open"));
      }
    });

    // Leaving mobile width with the drawer open would strand body.c-open.
    window.addEventListener("resize", () => {
      if (window.innerWidth > DRAWER_MAX && document.body.classList.contains("c-open")) {
        setOpen(false);
      }
    });
  }

  if (header) {
    let lastScrollTop = window.scrollY;
    const showNav = () => header.classList.remove("header-hidden");
    const hideNav = () => {
      if (document.body.classList.contains("c-open")) {
        return;
      }
      header.classList.add("header-hidden");
      header.classList.remove("has-mega-open");
    };
    const onScroll = () => {
      const scrollTop = window.scrollY;
      header.classList.toggle("is-scrolled", scrollTop > 10);
      if (scrollTop <= 8) {
        showNav();
      } else if (scrollTop > lastScrollTop + 2) {
        hideNav();
      } else if (scrollTop < lastScrollTop - 2) {
        showNav();
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener(
      "wheel",
      (event) => {
        if (event.deltaY > 0 && window.scrollY > 0) {
          hideNav();
        } else if (event.deltaY < 0) {
          showNav();
        }
      },
      { passive: true }
    );

    // Mega panels are white, so the bar cannot stay transparent while one is open.
    header.querySelectorAll("[data-mega]").forEach((item) => {
      const sync = (open) => header.classList.toggle("has-mega-open", open);
      item.addEventListener("mouseenter", () => sync(true));
      item.addEventListener("mouseleave", () => sync(false));
      item.addEventListener("focusin", () => sync(true));
    });
  }

  document.querySelectorAll("[data-acc-toggle]").forEach((btn) => {
    btn.addEventListener("click", (event) => {
      event.preventDefault();
      btn.closest("li")?.classList.toggle("is-open");
    });
  });

  const megaRoots = document.querySelectorAll("[data-mega] .pro_nav_tab2");
  megaRoots.forEach((megaRoot) => {
    const mega = megaRoot.closest("[data-mega]");
    megaRoot.querySelectorAll("[data-mega-tab]").forEach((tab) => {
      const activate = () => {
        const key = tab.getAttribute("data-mega-tab");
        megaRoot.querySelectorAll("[data-mega-tab]").forEach((el) => {
          el.classList.toggle("on", el === tab);
        });
        (mega || document).querySelectorAll("[data-mega-panel]").forEach((panel) => {
          panel.classList.toggle("on", panel.getAttribute("data-mega-panel") === key);
        });
      };
      tab.addEventListener("mouseenter", activate);
      tab.addEventListener("focus", activate);
      tab.addEventListener("click", (event) => {
        if (tab.classList.contains("on")) {
          return;
        }
        event.preventDefault();
        activate();
      });
    });
  });

  document.querySelectorAll("[data-mega-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const item = btn.closest("[data-mega]");
      document.querySelectorAll("[data-mega]").forEach((other) => {
        if (other !== item) {
          other.classList.remove("is-open");
        }
      });
      item?.classList.toggle("is-open");
    });
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      document.querySelectorAll("[data-mega].is-open").forEach((el) => el.classList.remove("is-open"));
      header?.classList.remove("has-mega-open");
      setOpen(false);
    }
  });

  document.querySelectorAll("[data-banners], [data-hero]").forEach((root) => {
    const slides = [...root.querySelectorAll(".h-banner__slide, .c-hero__slide")];
    const dotsWrap = root.querySelector("[data-banner-dots]");
    if (!slides.length || !dotsWrap) {
      return;
    }
    let index = 0;
    slides.forEach((_, i) => {
      const dot = document.createElement("button");
      dot.type = "button";
      dot.setAttribute("aria-label", `Slide ${i + 1}`);
      if (i === 0) {
        dot.classList.add("is-on");
      }
      dot.addEventListener("click", () => go(i));
      dotsWrap.append(dot);
    });
    const dots = [...dotsWrap.querySelectorAll("button")];
    const go = (next) => {
      slides[index].classList.remove("is-on");
      dots[index].classList.remove("is-on");
      index = (next + slides.length) % slides.length;
      slides[index].classList.add("is-on");
      dots[index].classList.add("is-on");
    };
    window.setInterval(() => go(index + 1), 5000);
  });

  const buttons = [...document.querySelectorAll("[data-tab]")];
  const rails = [...document.querySelectorAll("[data-rail]")];
  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const key = btn.getAttribute("data-tab");
      buttons.forEach((b) => {
        const on = b === btn;
        b.classList.toggle("is-on", on);
        b.setAttribute("aria-selected", on ? "true" : "false");
      });
      rails.forEach((rail) => {
        rail.classList.toggle("is-on", rail.getAttribute("data-rail") === key);
      });
    });
  });

  document.querySelectorAll("[data-j3-tabs]").forEach((root) => {
    const tabs = [...root.querySelectorAll("[data-j3-tab]")];
    const panels = [...root.querySelectorAll("[data-j3-panel]")];
    const activate = (key) => {
      tabs.forEach((tab) => {
        const on = tab.getAttribute("data-j3-tab") === key;
        tab.classList.toggle("is-on", on);
        tab.setAttribute("aria-selected", on ? "true" : "false");
      });
      panels.forEach((panel) => {
        panel.classList.toggle("is-on", panel.getAttribute("data-j3-panel") === key);
      });
    };
    tabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        const key = tab.getAttribute("data-j3-tab") || "";
        activate(key);
        if (key !== "" && window.history && window.history.replaceState) {
          window.history.replaceState(null, "", `#${key}`);
        }
      });
    });
    const hash = (window.location.hash || "").replace(/^#/, "");
    if (hash !== "" && root.querySelector(`[data-j3-tab="${hash}"]`)) {
      activate(hash);
    }
  });

  const accountRadios = [...document.querySelectorAll('input[name="account_type"]')];
  const vatInput = document.querySelector("[data-b2b-vat]");
  const companyInput = document.querySelector("[data-b2b-company]");
  const syncAccount = () => {
    const type = accountRadios.find((el) => el.checked)?.value;
    const isB2b = type === "b2b";
    if (vatInput) {
      vatInput.required = isB2b;
    }
    if (companyInput) {
      companyInput.required = isB2b;
    }
  };
  accountRadios.forEach((el) => el.addEventListener("change", syncAccount));
  syncAccount();

  document.querySelectorAll("[data-rail]").forEach((rail) => {
    const scroller = rail.querySelector("[data-rail-scroller]");
    const prev = rail.querySelector("[data-rail-prev]");
    const next = rail.querySelector("[data-rail-next]");
    if (!scroller) {
      return;
    }
    const jump = (dir) => {
      const card = scroller.querySelector(".h-card, .p-explore__card, .s-404__sku");
      const styles = window.getComputedStyle(scroller);
      const gap = Number.parseFloat(styles.columnGap || styles.gap || "38") || 38;
      const step = card ? card.getBoundingClientRect().width + gap : scroller.clientWidth * 0.7;
      scroller.scrollBy({ left: dir * step, behavior: "smooth" });
    };
    prev?.addEventListener("click", () => jump(-1));
    next?.addEventListener("click", () => jump(1));
  });

  document.querySelectorAll("[data-culture]").forEach((box) => {
    const cards = [...box.querySelectorAll("[data-culture-card]")];
    cards.forEach((card) => {
      card.addEventListener("click", () => {
        cards.forEach((c) => {
          const on = c === card;
          c.classList.toggle("is-on", on);
          c.setAttribute("aria-pressed", on ? "true" : "false");
        });
      });
    });
  });

  document.querySelectorAll("[data-history]").forEach((root) => {
    const slides = [...root.querySelectorAll("[data-history-slide]")];
    const years = [...root.querySelectorAll("[data-history-year]")];
    if (!slides.length || !years.length) {
      return;
    }
    let index = 0;
    const go = (next) => {
      slides[index].classList.remove("is-on");
      years[index].classList.remove("is-on");
      years[index].setAttribute("aria-selected", "false");
      index = (next + slides.length) % slides.length;
      slides[index].classList.add("is-on");
      years[index].classList.add("is-on");
      years[index].setAttribute("aria-selected", "true");
    };
    years.forEach((btn, i) => {
      btn.addEventListener("click", () => go(i));
    });
    root.querySelector("[data-history-prev]")?.addEventListener("click", () => go(index - 1));
    root.querySelector("[data-history-next]")?.addEventListener("click", () => go(index + 1));
  });

  const revealSkip = ".h-banner, .c-hero, .p-banner, .a-hero, .why-hero, .j3-hero, .jc-contact__hero, .why-tab, .d-tab, .d-clone, .h-tabs, .c-tabs, .h-rail, .p-high, .show_nav, footer, header, .site-header";
  const revealUp = [
    ".js-reveal",
    ".a-subh",
    ".a-culture__box",
    ".a-history__stage",
    ".a-listen__card",
    ".h-custom .h-title",
    ".h-custom__intro",
    ".h-trusted",
    ".c-group__head",
    ".p-evomax",
    ".p-details__wide",
    ".p-details__cell",
    ".p-explore__head",
    ".jc-contact__panel",
    ".jc-contact__faq",
    ".why-stats__item",
    ".why-compare",
    ".s-compare",
    ".j3-band",
    ".j3-products",
  ].join(",");
  const revealLeft = [
    ".a-company__img",
    ".h-fill__txt",
    ".h-custom__premium-img",
    ".p-dart__copy",
    ".p-laser__copy",
    ".why-intro__copy",
    ".why-split__media",
    ".why-row__media",
    ".j3-split:not(.j3-split--reverse) .j3-split__media",
    ".j3-split--reverse .j3-split__txt",
  ].join(",");
  const revealRight = [
    ".a-company__txt",
    ".h-fill__img",
    ".h-custom__premium-txt",
    ".p-laser__media",
    ".why-intro__media",
    ".why-split__copy",
    ".why-row__copy",
    ".j3-split:not(.j3-split--reverse) .j3-split__txt",
    ".j3-split--reverse .j3-split__media",
  ].join(",");
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const collect = (sel) => [...document.querySelectorAll(sel)].filter((el) => {
    if (el.matches(revealSkip) || el.closest(revealSkip)) {
      return false;
    }
    return true;
  });
  const leftSet = new Set(collect(revealLeft));
  const rightSet = new Set(collect(revealRight));
  let revealNodes = [...new Set([...collect(revealUp), ...leftSet, ...rightSet])];
  const nodeSet = new Set(revealNodes);
  revealNodes = revealNodes.filter((el) => {
    let parent = el.parentElement;
    while (parent) {
      if (nodeSet.has(parent)) {
        return false;
      }
      parent = parent.parentElement;
    }
    return true;
  });
  const staggerParents = new Map();
  revealNodes.forEach((el) => {
    const parent = el.parentElement;
    if (!parent) {
      return;
    }
    if (!staggerParents.has(parent)) {
      staggerParents.set(parent, []);
    }
    staggerParents.get(parent).push(el);
  });
  staggerParents.forEach((els) => {
    if (els.length < 2) {
      return;
    }
    els.forEach((el, i) => {
      el.style.setProperty("--reveal-delay", `${(Math.min(i, 7) * 0.12).toFixed(2)}s`);
    });
  });
  if (reduceMotion) {
    revealNodes.forEach((el) => el.classList.add("is-in"));
  } else if (revealNodes.length) {
    document.documentElement.classList.add("has-reveal");
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }
          entry.target.classList.add("is-in");
          io.unobserve(entry.target);
        });
      },
      { threshold: 0, rootMargin: "0px 0px -160px 0px" }
    );
    revealNodes.forEach((el) => {
      el.classList.add("js-reveal");
      if (leftSet.has(el)) {
        el.classList.add("js-reveal--left");
      } else if (rightSet.has(el)) {
        el.classList.add("js-reveal--right");
      }
      io.observe(el);
    });
  }

  const whyOn = document.querySelector(".why-tab a.on, .d-tab a.on");
  if (whyOn instanceof HTMLElement && whyOn.parentElement) {
    const bar = whyOn.parentElement;
    bar.scrollLeft = Math.max(0, whyOn.offsetLeft - 24);
  }
})();
