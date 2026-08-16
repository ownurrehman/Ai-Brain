(() => {
  const cfg = window.bcCart || {};
  const ajaxUrl = cfg.ajaxUrl || "/wp-admin/admin-ajax.php";
  const i18n = cfg.i18n || {};

  const drawer = document.getElementById("bc-drawer");
  const backdrop = document.getElementById("bc-drawer-backdrop");
  const toast = document.getElementById("bc-toast");
  const itemsEl = document.querySelector("[data-bc-cart-items]");
  const subtotalEl = document.querySelector("[data-bc-cart-subtotal]");
  const countEls = () => document.querySelectorAll("[data-bc-cart-count]");

  const post = async (action, data = {}) => {
    const body = new URLSearchParams({ action, ...data });
    const res = await fetch(ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      credentials: "same-origin",
      body,
    });
    return res.json();
  };

  const showToast = (message, actionLabel, actionHref) => {
    if (!toast) return;
    toast.innerHTML = "";
    const text = document.createElement("span");
    text.textContent = message;
    toast.appendChild(text);
    if (actionLabel && actionHref) {
      const a = document.createElement("button");
      a.type = "button";
      a.className = "bc-toast__action";
      a.textContent = actionLabel;
      a.addEventListener("click", () => openDrawer());
      toast.appendChild(a);
    }
    toast.hidden = false;
    toast.classList.add("is-visible");
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => {
      toast.classList.remove("is-visible");
      toast.hidden = true;
    }, 3200);
  };

  const updateCount = (count) => {
    countEls().forEach((el) => {
      el.textContent = String(count);
      el.classList.toggle("is-empty", !count);
      el.classList.add("is-pop");
      setTimeout(() => el.classList.remove("is-pop"), 350);
    });
  };

  const renderDrawer = (payload) => {
    if (!itemsEl) return;
    const items = payload.items || [];
    if (!items.length) {
      itemsEl.innerHTML = `<p class="bc-drawer__empty">${i18n.empty || "Your cart is empty"}</p>`;
    } else {
      itemsEl.innerHTML = items
        .map(
          (item) => `
        <div class="bc-drawer__item" data-key="${item.key}">
          <div class="bc-drawer__item-main">
            <strong>${item.name}</strong>
            <div class="bc-drawer__item-price">${item.subtotal}</div>
          </div>
          <div class="bc-drawer__item-actions">
            <div class="bc-qty">
              <button type="button" data-bc-qty="-1" aria-label="Decrease">−</button>
              <span>${item.qty}</span>
              <button type="button" data-bc-qty="1" aria-label="Increase">+</button>
            </div>
            <button type="button" class="bc-drawer__remove" data-bc-remove>${"Remove"}</button>
          </div>
        </div>`
        )
        .join("");
    }
    if (subtotalEl) subtotalEl.innerHTML = payload.subtotal || "—";
    updateCount(payload.count || 0);
  };

  const refreshDrawer = async () => {
    try {
      const json = await post("bc_cart_drawer");
      if (json && json.success) renderDrawer(json.data);
    } catch (_) {
      /* ignore */
    }
  };

  const openDrawer = async () => {
    await refreshDrawer();
    if (drawer) {
      drawer.classList.add("is-open");
      drawer.setAttribute("aria-hidden", "false");
    }
    if (backdrop) {
      backdrop.hidden = false;
      backdrop.classList.add("is-open");
    }
    document.body.classList.add("bc-drawer-open");
  };

  const closeDrawer = () => {
    if (drawer) {
      drawer.classList.remove("is-open");
      drawer.setAttribute("aria-hidden", "true");
    }
    if (backdrop) {
      backdrop.classList.remove("is-open");
      backdrop.hidden = true;
    }
    document.body.classList.remove("bc-drawer-open");
  };

  const addToCart = async (btn) => {
    const productId = btn.getAttribute("data-product_id");
    if (!productId || btn.disabled) return;

    const original = btn.textContent;
    btn.disabled = true;
    btn.classList.add("is-loading");
    btn.textContent = i18n.adding || "Adding…";

    try {
      const json = await post("bc_add_to_cart", {
        product_id: productId,
        quantity: "1",
      });
      if (!json || !json.success) {
        throw new Error((json && json.data && json.data.message) || "error");
      }
      renderDrawer(json.data);
      btn.classList.remove("is-loading");
      btn.classList.add("is-added");
      btn.textContent = "✓";
      showToast(
        `${i18n.added || "Added to cart"}${json.data.added ? ": " + json.data.added : ""}`,
        i18n.viewCart || "View cart"
      );
      openDrawer();
      setTimeout(() => {
        btn.classList.remove("is-added");
        btn.textContent = i18n.add || original || "ADD";
        btn.disabled = false;
      }, 1200);
    } catch (_) {
      btn.classList.remove("is-loading");
      btn.disabled = false;
      btn.textContent = i18n.add || original || "ADD";
      showToast(i18n.error || "Could not update cart");
    }
  };

  const addSelectedToCart = async (btn) => {
    const ids = [
      ...new Set(
        Array.from(document.querySelectorAll(".bc-row-check:checked")).map((c) => c.value).filter(Boolean)
      ),
    ];
    if (!ids.length) {
      showToast(i18n.noneSelected || "Select at least one site");
      return;
    }
    const original = btn.textContent;
    btn.disabled = true;
    btn.classList.add("is-loading");
    btn.textContent = i18n.adding || "Adding…";
    try {
      const json = await post("bc_add_to_cart_bulk", {
        product_ids: ids.join(","),
      });
      if (!json || !json.success) {
        throw new Error((json && json.data && json.data.message) || "error");
      }
      renderDrawer(json.data);
      document.querySelectorAll(".bc-row-check").forEach((c) => {
        c.checked = false;
      });
      const selectAll = document.getElementById("bc-select-all");
      if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
      }
      if (typeof window.__bcSyncBulkBar === "function") window.__bcSyncBulkBar();
      showToast(
        `${i18n.added || "Added to cart"}${json.data.added ? ": " + json.data.added : ""}`,
        i18n.viewCart || "View cart"
      );
      openDrawer();
    } catch (_) {
      showToast(i18n.error || "Could not update cart");
    } finally {
      btn.classList.remove("is-loading");
      btn.disabled = false;
      btn.textContent = original || "Add selected";
    }
  };

  document.addEventListener("click", async (e) => {
    const bulkBtn = e.target.closest("[data-bc-add-selected]");
    if (bulkBtn) {
      e.preventDefault();
      addSelectedToCart(bulkBtn);
      return;
    }

    const addBtn = e.target.closest(".bc-add[data-product_id]");
    if (addBtn) {
      e.preventDefault();
      addToCart(addBtn);
      return;
    }

    if (e.target.closest("[data-bc-cart-open]")) {
      e.preventDefault();
      openDrawer();
      return;
    }

    if (e.target.closest("[data-bc-cart-close]") || e.target === backdrop) {
      closeDrawer();
      return;
    }

    const qtyBtn = e.target.closest("[data-bc-qty]");
    if (qtyBtn) {
      const item = qtyBtn.closest(".bc-drawer__item");
      if (!item) return;
      const key = item.getAttribute("data-key");
      const delta = Number(qtyBtn.getAttribute("data-bc-qty") || 0);
      const current = Number(item.querySelector(".bc-qty span")?.textContent || 1);
      const next = current + delta;
      try {
        const json = await post("bc_update_cart_qty", {
          key,
          quantity: String(Math.max(0, next)),
        });
        if (json && json.success) renderDrawer(json.data);
      } catch (_) {
        showToast(i18n.error || "Could not update cart");
      }
      return;
    }

    const removeBtn = e.target.closest("[data-bc-remove]");
    if (removeBtn) {
      const item = removeBtn.closest(".bc-drawer__item");
      if (!item) return;
      try {
        const json = await post("bc_remove_cart_item", {
          key: item.getAttribute("data-key"),
        });
        if (json && json.success) renderDrawer(json.data);
      } catch (_) {
        showToast(i18n.error || "Could not update cart");
      }
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeDrawer();
  });
})();
