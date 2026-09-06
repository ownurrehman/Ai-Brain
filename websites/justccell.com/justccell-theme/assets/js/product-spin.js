/**
 * Product 360° drag-to-spin — ccell.com dart_r360 / rotate360 parity.
 * All frames ship in HTML with src; active frame toggles .is-on (opacity stack).
 * Rank Ray — https://rankray.com
 */
(() => {
  const STEP_PX = 20;

  const bindSpin = (root) => {
    const mask = root.querySelector("[data-spin-mask]");
    const handle = mask instanceof HTMLElement ? mask : root;
    const frames = [...root.querySelectorAll(".p-spin__frames img, img.p-spin__view")].filter(
      (node) => node instanceof HTMLImageElement
    );
    if (frames.length < 2) {
      return;
    }

    let index = Math.max(0, frames.findIndex((img) => img.classList.contains("is-on")));
    let originX = 0;
    let dragging = false;
    let touchStartX = 0;
    let touchStartY = 0;

    const showFrame = (next) => {
      index = ((next % frames.length) + frames.length) % frames.length;
      frames.forEach((img, i) => {
        img.classList.toggle("is-on", i === index);
      });
    };

    const step = (pageX) => {
      if (pageX - originX >= STEP_PX) {
        originX = pageX;
        showFrame(index + 1);
      } else if (pageX - originX <= -STEP_PX) {
        originX = pageX;
        showFrame(index - 1);
      }
    };

    if (!frames[index]?.classList.contains("is-on")) {
      showFrame(index);
    }

    handle.addEventListener("mousedown", (event) => {
      if (event.button !== 0) {
        return;
      }
      event.preventDefault();
      dragging = true;
      originX = event.pageX;
      root.classList.add("is-dragging");

      const onMove = (moveEvent) => {
        if (!dragging) {
          return;
        }
        step(moveEvent.pageX);
      };
      const onUp = () => {
        dragging = false;
        root.classList.remove("is-dragging");
        window.removeEventListener("mousemove", onMove);
        window.removeEventListener("mouseup", onUp);
      };
      window.addEventListener("mousemove", onMove);
      window.addEventListener("mouseup", onUp);
    });

    handle.addEventListener(
      "touchstart",
      (event) => {
        const touch = event.targetTouches[0];
        if (!touch) {
          return;
        }
        dragging = true;
        originX = touch.pageX;
        touchStartX = touch.pageX;
        touchStartY = touch.pageY;
      },
      { passive: true }
    );

    handle.addEventListener(
      "touchmove",
      (event) => {
        const touch = event.targetTouches[0];
        if (!touch || !dragging) {
          return;
        }
        if (Math.abs(touch.pageX - touchStartX) > Math.abs(touch.pageY - touchStartY)) {
          event.preventDefault();
        }
        step(touch.pageX);
      },
      { passive: false }
    );

    const endTouch = () => {
      dragging = false;
      root.classList.remove("is-dragging");
    };
    handle.addEventListener("touchend", endTouch);
    handle.addEventListener("touchcancel", endTouch);
  };

  document.querySelectorAll("[data-spin]").forEach((root) => {
    if (root instanceof HTMLElement) {
      bindSpin(root);
    }
  });
})();
