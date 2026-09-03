/**
 * Slide-out cart drawer — AJAX add-to-cart, no page reload.
 * Rank Ray — https://rankray.com
 */
(() => {
  const cfg = window.JustccellCart;
  if (!cfg || !cfg.ajaxUrl) {
    return;
  }

  const drawer = document.querySelector("[data-cart-drawer]");
  const panel = drawer?.querySelector("[data-cart-panel]");
  const backdrop = drawer?.querySelector("[data-cart-backdrop]");
  const itemsEl = drawer?.querySelector("[data-cart-items]");
  const subtotalEl = drawer?.querySelector("[data-cart-subtotal]");
  const countEls = document.querySelectorAll("[data-cart-count]");
  const viewCartLink = drawer?.querySelector("[data-cart-view]");
  const toastEl = drawer?.querySelector("[data-cart-toast]");

  const i18n = cfg.i18n || {};

  const setCount = (count) => {
    const n = Math.max(0, Number(count) || 0);
    countEls.forEach((el) => {
      if (el instanceof HTMLElement) {
        el.textContent = String(n);
        el.hidden = n < 1;
      }
    });
  };

  const escapeHtml = (str) =>
    String(str || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");

  const decodeMoney = (str) => {
    const node = document.createElement("textarea");
    node.innerHTML = String(str || "");
    return node.value;
  };

  const renderItems = (items) => {
    if (!(itemsEl instanceof HTMLElement)) {
      return;
    }
    if (!Array.isArray(items) || items.length === 0) {
      itemsEl.innerHTML = `<p class="jc-cart__empty">${escapeHtml(i18n.empty || "Your cart is empty.")}</p>`;
      return;
    }

    itemsEl.innerHTML = items
      .map((item) => {
        const meta = Array.isArray(item.meta) ? item.meta.map((line) => `<li>${escapeHtml(line)}</li>`).join("") : "";
        const variation = item.variation ? `<p class="jc-cart-item__var">${escapeHtml(item.variation)}</p>` : "";
        return `<article class="jc-cart-item">
          <div class="jc-cart-item__thumb-wrap">${item.thumb || ""}</div>
          <div class="jc-cart-item__body">
            <h3 class="jc-cart-item__name">${escapeHtml(item.name)}</h3>
            ${variation}
            ${meta ? `<ul class="jc-cart-item__meta">${meta}</ul>` : ""}
            <p class="jc-cart-item__qty">${escapeHtml(String(item.qty || 1))} × ${escapeHtml(decodeMoney(item.price || ""))}</p>
          </div>
        </article>`;
      })
      .join("");
  };

  const applyPayload = (data) => {
    if (!data || typeof data !== "object") {
      return;
    }
    setCount(data.count);
    if (subtotalEl instanceof HTMLElement) {
      subtotalEl.innerHTML = data.subtotal_html || "";
    }
    if (viewCartLink instanceof HTMLAnchorElement && data.cart_url) {
      viewCartLink.href = data.cart_url;
    }
    renderItems(data.items);
  };

  const setOpen = (open) => {
    if (!(drawer instanceof HTMLElement)) {
      return;
    }
    drawer.classList.toggle("is-open", open);
    drawer.classList.remove("is-minimized");
    drawer.setAttribute("aria-hidden", open ? "false" : "true");
    document.documentElement.classList.toggle("jc-cart-open", open);
    if (open && panel instanceof HTMLElement) {
      panel.focus();
    }
  };

  const setMinimized = (min) => {
    if (!(drawer instanceof HTMLElement)) {
      return;
    }
    drawer.classList.toggle("is-minimized", min);
    if (min) {
      drawer.classList.remove("is-open");
      drawer.setAttribute("aria-hidden", "true");
      document.documentElement.classList.remove("jc-cart-open");
    }
  };

  const showToast = (message, ok = true) => {
    if (!(toastEl instanceof HTMLElement)) {
      return;
    }
    toastEl.hidden = false;
    toastEl.textContent = message;
    toastEl.classList.toggle("is-error", !ok);
    window.clearTimeout(showToast._t);
    showToast._t = window.setTimeout(() => {
      toastEl.hidden = true;
    }, 4200);
  };

  const refreshDrawer = async () => {
    const body = new URLSearchParams();
    body.set("action", "justccell_cart_drawer");
    body.set("nonce", cfg.nonce || "");
    const res = await fetch(cfg.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body: body.toString(),
    });
    const json = await res.json();
    if (json?.success && json.data?.data) {
      applyPayload(json.data.data);
    }
  };

  const addToCart = async (formData, trigger) => {
    if (!(formData instanceof FormData)) {
      return { success: false, message: i18n.error || "Could not add to cart." };
    }

    formData.set("action", "justccell_add_to_cart");
    formData.set("justccell_cart_ajax", "1");
    if (!formData.has("justccell_cart_nonce")) {
      formData.set("justccell_cart_nonce", cfg.nonce || "");
    }

    if (trigger instanceof HTMLButtonElement) {
      trigger.disabled = true;
      trigger.dataset.busy = "1";
      const prev = trigger.textContent;
      trigger.textContent = i18n.adding || "Adding…";
      try {
        const res = await fetch(cfg.ajaxUrl, {
          method: "POST",
          credentials: "same-origin",
          body: formData,
        });
        const json = await res.json();
        if (json?.success) {
          const payload = json.data?.data || json.data;
          applyPayload(payload);
          const msg = json.data?.message || i18n.added || "Added to your cart.";
          showToast(msg, true);
          setOpen(true);
          window.dispatchEvent(new CustomEvent("justccell:cart-updated", { detail: payload }));
          return { success: true, message: msg };
        }
        const err = json?.data?.message || i18n.error || "Could not add to cart.";
        showToast(err, false);
        return { success: false, message: err };
      } catch {
        const err = i18n.error || "Could not add to cart.";
        showToast(err, false);
        return { success: false, message: err };
      } finally {
        trigger.disabled = false;
        delete trigger.dataset.busy;
        trigger.textContent = prev;
      }
    }

    const res = await fetch(cfg.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    });
    const json = await res.json();
    if (json?.success) {
      const payload = json.data?.data || json.data;
      applyPayload(payload);
      setOpen(true);
      return { success: true, message: json.data?.message || "" };
    }
    return { success: false, message: json?.data?.message || i18n.error || "" };
  };

  window.JustccellCartApi = {
    addToCart,
    open: () => setOpen(true),
    close: () => setOpen(false),
    minimize: () => setMinimized(true),
    refresh: refreshDrawer,
    applyPayload,
  };

  document.addEventListener("click", (event) => {
    const target = event.target instanceof Element ? event.target : null;
    if (!target) {
      return;
    }

    if (target.closest("[data-cart-open]")) {
      event.preventDefault();
      refreshDrawer().then(() => setOpen(true));
      return;
    }

    if (target.closest("[data-cart-close]")) {
      event.preventDefault();
      setOpen(false);
      return;
    }

    if (target.closest("[data-cart-minimize]")) {
      event.preventDefault();
      setMinimized(true);
    }

    if (target === backdrop) {
      setOpen(false);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && drawer?.classList.contains("is-open")) {
      setOpen(false);
    }
  });
})();
