(() => {
  const spin = document.querySelector("[data-spin]");
  const still = document.querySelector("[data-still]");
  const stillImg = document.querySelector("[data-still] img");
  const view = document.querySelector("[data-spin-view]");
  const thumbs = [...document.querySelectorAll("[data-thumb]")];
  const mask = document.querySelector("[data-spin-mask]");

  let frames = [];
  if (spin instanceof HTMLElement) {
    try {
      const parsed = JSON.parse(spin.getAttribute("data-spin-frames") || "[]");
      if (Array.isArray(parsed)) {
        frames = parsed.filter((url) => typeof url === "string" && url !== "");
      }
    } catch {
      frames = [];
    }
  }

  const showView = (mode, src) => {
    const useSpin = mode === "spin" && spin && frames.length > 0;
    spin?.classList.toggle("is-on", Boolean(useSpin));
    still?.classList.toggle("is-on", !useSpin);
    if (!useSpin && stillImg instanceof HTMLImageElement && src) {
      stillImg.src = src;
    }
  };

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      thumbs.forEach((item) => item.classList.toggle("is-on", item === thumb));
      showView(thumb.getAttribute("data-view") || "still", thumb.getAttribute("data-src") || "");
    });
  });

  if (mask instanceof HTMLElement && view instanceof HTMLImageElement && frames.length > 1) {
    let index = 0;
    let originX = 0;
    let dragging = false;

    const paint = (next) => {
      const count = frames.length;
      index = ((next % count) + count) % count;
      view.src = frames[index];
      const ahead = frames[(index + 1) % count];
      const behind = frames[(index - 1 + count) % count];
      [ahead, behind].forEach((url) => {
        const preload = new Image();
        preload.src = url;
      });
    };

    const move = (pageX) => {
      const delta = pageX - originX;
      if (delta >= 18) {
        originX = pageX;
        paint(index + 1);
      } else if (delta <= -18) {
        originX = pageX;
        paint(index - 1);
      }
    };

    mask.addEventListener("mousedown", (event) => {
      dragging = true;
      originX = event.pageX;
      event.preventDefault();
    });
    window.addEventListener("mousemove", (event) => {
      if (dragging) {
        move(event.pageX);
      }
    });
    window.addEventListener("mouseup", () => {
      dragging = false;
    });

    let startX = 0;
    let startY = 0;
    mask.addEventListener(
      "touchstart",
      (event) => {
        const touch = event.targetTouches[0];
        if (!touch) {
          return;
        }
        dragging = true;
        originX = touch.pageX;
        startX = touch.pageX;
        startY = touch.pageY;
      },
      { passive: true }
    );
    mask.addEventListener(
      "touchmove",
      (event) => {
        const touch = event.targetTouches[0];
        if (!touch || !dragging) {
          return;
        }
        if (Math.abs(touch.pageX - startX) > Math.abs(touch.pageY - startY)) {
          event.preventDefault();
        }
        move(touch.pageX);
      },
      { passive: false }
    );
    mask.addEventListener("touchend", () => {
      dragging = false;
    });
  }

  const high = document.querySelector("[data-sticky-features]");
  if (high instanceof HTMLElement) {
    const panels = [...high.querySelectorAll("[data-feature-panel]")];
    const dots = [...high.querySelectorAll("[data-feature-dot]")];
    const count = panels.length;
    if (count) {
      const unit = () => window.innerHeight * 0.7;

      const applyHeight = () => {
        high.style.height = `${count * 70}vh`;
      };

      const sync = () => {
        const start = high.getBoundingClientRect().top + window.scrollY;
        let i = 0;
        while (i < count - 1 && window.scrollY >= start + unit() * (i + 1) - 1) {
          i += 1;
        }
        panels.forEach((panel, n) => panel.classList.toggle("is-on", n === i));
        dots.forEach((dot, n) => dot.classList.toggle("is-on", n === i));
      };

      dots.forEach((dot, i) => {
        dot.addEventListener("click", () => {
          window.scrollTo({ top: high.offsetTop + i * unit() + 2, behavior: "smooth" });
        });
      });

      applyHeight();
      sync();
      window.addEventListener("scroll", sync, { passive: true });
      window.addEventListener("resize", () => {
        applyHeight();
        sync();
      });
    }
  }
})();
