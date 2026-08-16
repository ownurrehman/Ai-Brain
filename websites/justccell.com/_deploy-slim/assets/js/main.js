(() => {
  const header = document.querySelector("[data-header]");
  const toggle = document.querySelector("[data-nav-toggle]");
  if (header && toggle) {
    toggle.addEventListener("click", () => {
      const open = header.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  const langSwitch = document.querySelector("[data-lang-switch]");
  const langToggle = document.querySelector("[data-lang-toggle]");
  const langPanel = document.querySelector("[data-lang-panel]");
  if (langSwitch && langToggle && langPanel) {
    langToggle.addEventListener("click", () => {
      const open = langSwitch.classList.toggle("is-open");
      langToggle.setAttribute("aria-expanded", open ? "true" : "false");
      langPanel.hidden = !open;
    });
    document.addEventListener("click", (event) => {
      if (!(event.target instanceof Node) || !langSwitch.contains(event.target)) {
        langSwitch.classList.remove("is-open");
        langToggle.setAttribute("aria-expanded", "false");
        langPanel.hidden = true;
      }
    });
  }

  document.querySelectorAll("[data-mega-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.closest("[data-mega]")?.classList.toggle("is-open");
    });
  });

  const slides = [...document.querySelectorAll(".h-banner__slide")];
  const dotsWrap = document.querySelector("[data-banner-dots]");
  if (slides.length && dotsWrap) {
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
  }

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
      scroller.scrollBy({ left: dir * (scroller.clientWidth * 0.7), behavior: "smooth" });
    };
    prev?.addEventListener("click", () => jump(-1));
    next?.addEventListener("click", () => jump(1));
  });
})();
