/**
 * Product gallery, 360 spin, wholesale buy box.
 * Rank Ray — https://rankray.com
 */
(() => {
  const spin = document.querySelector("[data-spin]");
  const still = document.querySelector("[data-still]");
  const stillImg = document.querySelector("[data-still] img");
  const thumbs = [...document.querySelectorAll("[data-thumb]")];
  const mask = document.querySelector("[data-spin-mask]");
  const stage = document.querySelector("[data-product-stage]");
  const defaultImageId = Number(stage?.getAttribute("data-default-image-id") || 0);
  const defaultImageUrl = stage?.getAttribute("data-default-image-url") || "";
  const frameImgs = spin instanceof HTMLElement
    ? [...spin.querySelectorAll(".p-spin__frames img")].length
      ? [...spin.querySelectorAll(".p-spin__frames img")]
      : [...spin.querySelectorAll("img.p-spin__view")]
    : [];

  let variationImage = { id: defaultImageId, src: defaultImageUrl };

  const sameUrl = (a, b) => {
    if (!a || !b) {
      return false;
    }
    return String(a).split("?")[0] === String(b).split("?")[0];
  };

  const colourMatchesDefault = () => {
    if (defaultImageId > 0 && Number(variationImage.id) > 0) {
      return Number(variationImage.id) === defaultImageId;
    }
    if (defaultImageUrl && variationImage.src) {
      return sameUrl(variationImage.src, defaultImageUrl);
    }
    return true;
  };

  const paintStill = (src) => {
    if (!(stillImg instanceof HTMLImageElement) || !src) {
      return;
    }
    stillImg.removeAttribute("srcset");
    stillImg.removeAttribute("sizes");
    stillImg.src = src;
  };

  const showView = (mode, src) => {
    const allowSpin = mode === "spin" && spin && frameImgs.length > 0 && colourMatchesDefault();
    spin?.classList.toggle("is-on", Boolean(allowSpin));
    still?.classList.toggle("is-on", !allowSpin);
    if (!allowSpin) {
      paintStill(src || variationImage.src || defaultImageUrl);
    }
  };

  const applyVariationImage = (variation) => {
    const image = variation && typeof variation === "object" ? variation.image || {} : {};
    variationImage = {
      id: Number(variation?.image_id || image.id || 0),
      src: String(image.full_src || image.src || variationImage.src || defaultImageUrl),
    };
    const first = thumbs[0];
    const firstOn = !first || first.classList.contains("is-on");
    const mode = firstOn && first?.getAttribute("data-view") === "spin" ? "spin" : "still";
    const thumbSrc = firstOn ? "" : (first?.getAttribute("data-src") || "");
    showView(mode, colourMatchesDefault() ? thumbSrc || variationImage.src : variationImage.src);
  };

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      thumbs.forEach((item) => item.classList.toggle("is-on", item === thumb));
      const img = thumb.querySelector("img");
      const src =
        thumb.getAttribute("data-src") ||
        (img instanceof HTMLImageElement ? img.currentSrc || img.src : "") ||
        "";
      const mode = thumb.getAttribute("data-view") || "still";
      if (mode === "spin" && colourMatchesDefault()) {
        showView("spin", src);
        return;
      }
      showView("still", colourMatchesDefault() ? src : variationImage.src || src);
    });
  });

  if (window.jQuery) {
    const $ = window.jQuery;
    $(document.body).on("found_variation", ".variations_form", (_event, variation) => {
      applyVariationImage(variation || {});
    });
    $(document.body).on("reset_data reset_image hide_variation", ".variations_form", () => {
      variationImage = { id: defaultImageId, src: defaultImageUrl };
      const first = thumbs[0];
      const firstOn = !first || first.classList.contains("is-on");
      showView(firstOn && first?.getAttribute("data-view") === "spin" ? "spin" : "still", defaultImageUrl);
    });
    $(".variations_form").trigger("check_variations");
  }

  if (spin instanceof HTMLElement && frameImgs.length > 1) {
    const handle = mask instanceof HTMLElement ? mask : spin;
    const count = frameImgs.length;
    let index = Math.max(0, frameImgs.findIndex((img) => img.classList.contains("is-on")));
    let originX = 0;
    let dragging = false;
    let startX = 0;
    let startY = 0;

    const paint = (next) => {
      index = ((next % count) + count) % count;
      frameImgs.forEach((img, i) => {
        img.classList.toggle("is-on", i === index);
      });
    };

    const stepFromDelta = (pageX) => {
      const delta = pageX - originX;
      if (delta >= 20) {
        originX = pageX;
        paint(index + 1);
      } else if (delta <= -20) {
        originX = pageX;
        paint(index - 1);
      }
    };

    const begin = (pageX) => {
      dragging = true;
      originX = pageX;
      spin.classList.add("is-dragging");
    };

    const end = () => {
      dragging = false;
      spin.classList.remove("is-dragging");
    };

    handle.addEventListener("mousedown", (event) => {
      if (event.button !== 0) {
        return;
      }
      event.preventDefault();
      begin(event.pageX);
    });
    window.addEventListener("mousemove", (event) => {
      if (dragging) {
        stepFromDelta(event.pageX);
      }
    });
    window.addEventListener("mouseup", end);

    handle.addEventListener(
      "touchstart",
      (event) => {
        const touch = event.targetTouches[0];
        if (!touch) {
          return;
        }
        begin(touch.pageX);
        startX = touch.pageX;
        startY = touch.pageY;
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
        if (Math.abs(touch.pageX - startX) > Math.abs(touch.pageY - startY)) {
          event.preventDefault();
        }
        stepFromDelta(touch.pageX);
      },
      { passive: false }
    );
    handle.addEventListener("touchend", end);
    handle.addEventListener("touchcancel", end);
  }

  const buy = document.querySelector("[data-buy-box]");
  if (buy instanceof HTMLElement) {
    const qty = buy.querySelector("[data-buy-qty]");
    const tbody = buy.querySelector("[data-buy-tiers]");
    const submits = buy.querySelectorAll("[data-buy-submit]");
    const sticky = document.querySelector("[data-buy-sticky]");
    const stickyPrice = sticky?.querySelector("[data-buy-sticky-price]");
    const jsonEl = buy.querySelector("[data-buy-config]") || buy.querySelector("[data-buy-offers]");
    let config = { tiers: [], variation_tiers: {}, tier_overrides: {}, attributes: [] };
    try {
      const parsed = JSON.parse(jsonEl?.textContent || "{}");
      if (Array.isArray(parsed)) {
        config.tiers = Array.isArray(parsed[0]?.tiers) ? parsed[0].tiers : [];
      } else if (parsed && typeof parsed === "object") {
        config = {
          tiers: Array.isArray(parsed.tiers) ? parsed.tiers : [],
          variation_tiers:
            parsed.variation_tiers && typeof parsed.variation_tiers === "object" ? parsed.variation_tiers : {},
          tier_overrides:
            parsed.tier_overrides && typeof parsed.tier_overrides === "object" ? parsed.tier_overrides : {},
          attributes: Array.isArray(parsed.attributes) ? parsed.attributes : [],
        };
      }
    } catch {
      config = { tiers: [], variation_tiers: {}, tier_overrides: {}, attributes: [] };
    }

    const cartForm = buy.querySelector("form.cart, form.variations_form");
    const wooQty = cartForm instanceof HTMLFormElement ? cartForm.querySelector("input.qty") : null;

    const attrSelects = () =>
      Array.from(
        buy.querySelectorAll("select[data-buy-attr], form.variations_form select[name^='attribute_']")
      ).filter((el) => el instanceof HTMLSelectElement);

    const selectedAttrs = () => {
      const out = {};
      attrSelects().forEach((sel) => {
        const key =
          sel.getAttribute("data-buy-attr") ||
          (sel.name || "").replace(/^attribute_(?:pa_)?/, "");
        if (key && sel.value) {
          out[key] = sel.value;
        }
      });
      return out;
    };

    const currentVariationId = () => {
      const el = buy.querySelector("input.variation_id, input[name='variation_id']");
      return el instanceof HTMLInputElement ? el.value : "";
    };

    const syncWooQty = () => {
      if (qty instanceof HTMLInputElement && wooQty instanceof HTMLInputElement && qty !== wooQty) {
        wooQty.value = qty.value;
      }
    };

    const activeTiers = () => {
      const vid = currentVariationId();
      const mapped = vid && config.variation_tiers ? config.variation_tiers[vid] : null;
      if (Array.isArray(mapped) && mapped.length > 0) {
        return mapped;
      }
      return Array.isArray(config.tiers) ? config.tiers : [];
    };

    const formatMoney = (amount) => {
      const n = Number(amount);
      if (!Number.isFinite(n)) {
        return "";
      }
      const currency = buy.getAttribute("data-currency") || "GBP";
      try {
        const parts = new Intl.NumberFormat("en-GB", { style: "currency", currency }).formatToParts(n);
        return parts
          .map((part) => (part.type === "currency" ? part.value + "\u00A0" : part.value))
          .join("");
      } catch {
        return "£\u00A0" + n.toFixed(2);
      }
    };

    const laserQuote = () => {
      const api = window.JustccellLaserApi;
      if (!api || typeof api.quote !== "function") {
        return null;
      }
      const next = api.quote();
      if (!next || !next.active) {
        return null;
      }
      return next;
    };

    const variationUnitAmount = (variation) => {
      if (!variation || typeof variation !== "object") {
        return NaN;
      }
      const raw =
        variation.display_price ??
        variation.price ??
        variation.display_regular_price ??
        variation.regular_price;
      const n = Number(raw);
      return Number.isFinite(n) ? n : NaN;
    };

    let activeVariation = null;

    const syncStickyFooter = (variation = activeVariation) => {
      if (!(stickyPrice instanceof HTMLElement)) {
        return;
      }
      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      const tiers = activeTiers();
      let match = null;
      if (Array.isArray(tiers) && tiers.length > 0) {
        match =
          tiers.find((tier) => {
            const min = Number(tier.qty_min) || 0;
            const max = Number(tier.qty_max) || 0;
            return quantity >= min && (max === 0 || quantity <= max);
          }) || tiers[0];
      }
      let unitNum = match ? Number(match.unit) : NaN;
      if (!Number.isFinite(unitNum) || unitNum <= 0) {
        unitNum = variationUnitAmount(variation);
      }
      const laser = laserQuote();
      const laserTotal = laser ? Number(laser.total) || 0 : 0;
      const canTotal = Number.isFinite(unitNum) && unitNum > 0;
      const hardwareTotal = canTotal ? unitNum * quantity : 0;
      const grand = hardwareTotal + laserTotal;

      if (!canTotal && !laser) {
        stickyPrice.textContent = buy.dataset.emptyTiers
          ? String(buy.dataset.emptyTiers)
          : "Quote on request";
        return;
      }

      if (canTotal && (laser || quantity > 1)) {
        stickyPrice.textContent = `${formatMoney(unitNum)} · ${formatMoney(grand)}`;
        return;
      }
      if (canTotal) {
        stickyPrice.textContent = formatMoney(laser ? grand : unitNum);
        return;
      }
      stickyPrice.textContent = formatMoney(laserTotal);
    };

    const paintQuote = (tiers, quantity, shown) => {
      const unitEl = buy.querySelector("[data-buy-unit]");
      const bandEl = buy.querySelector("[data-buy-band]");
      const totalEl = buy.querySelector("[data-buy-total]");
      const totalRow = buy.querySelector("[data-buy-total-row]");
      const labelEl = buy.querySelector("[data-buy-unit-label]");
      const quote = buy.querySelector("[data-buy-quote]");
      const bandSep = buy.querySelector("[data-buy-band-sep]");
      const hardwareRow = buy.querySelector("[data-buy-hardware-row]");
      const hardwareEl = buy.querySelector("[data-buy-hardware]");
      const laserRow = buy.querySelector("[data-buy-laser-row]");
      const laserEl = buy.querySelector("[data-buy-laser]");
      const empty = !Array.isArray(tiers) || tiers.length === 0;
      let match = null;
      if (!empty) {
        match = tiers.find((tier) => {
          const min = Number(tier.qty_min) || 0;
          const max = Number(tier.qty_max) || 0;
          return quantity >= min && (max === 0 || quantity <= max);
        }) || tiers[0];
      }
      const unitLabel = shown || (match ? String(match.price || "") : "") || "Quote on request";
      if (unitEl instanceof HTMLElement) {
        unitEl.textContent = unitLabel;
      }
      if (bandEl instanceof HTMLElement) {
        bandEl.textContent = match ? String(match.range || "") : "";
      }
      if (bandSep instanceof HTMLElement) {
        bandSep.hidden = !match || !String(match.range || "");
      }
      if (labelEl instanceof HTMLElement) {
        labelEl.hidden = empty;
      }
      const unitNum = match ? Number(match.unit) : NaN;
      const canTotal = Number.isFinite(unitNum) && unitNum > 0;
      const hardwareTotal = canTotal ? unitNum * quantity : 0;
      const laser = laserQuote();
      const laserTotal = laser ? Number(laser.total) || 0 : 0;
      const grand = hardwareTotal + laserTotal;
      if (hardwareEl instanceof HTMLElement) {
        hardwareEl.textContent = canTotal ? formatMoney(hardwareTotal) : "";
      }
      if (hardwareRow instanceof HTMLElement) {
        hardwareRow.hidden = !canTotal || !laser;
      }
      if (laserEl instanceof HTMLElement) {
        laserEl.textContent = laser ? formatMoney(laserTotal) : "";
      }
      if (laserRow instanceof HTMLElement) {
        laserRow.hidden = !laser;
      }
      if (totalEl instanceof HTMLElement) {
        totalEl.textContent = canTotal || laser ? formatMoney(grand) : "";
      }
      if (totalRow instanceof HTMLElement) {
        totalRow.hidden = !(canTotal || laser);
      }
      if (quote instanceof HTMLElement) {
        quote.classList.toggle("is-quote", empty && !laser);
        quote.classList.toggle("has-laser", Boolean(laser));
      }
      syncStickyFooter();
    };

    const paintTiers = (tiers, quantity) => {
      if (!(tbody instanceof HTMLElement)) {
        paintQuote(tiers, quantity, "");
        return;
      }
      tbody.replaceChildren();
      let shown = "";
      if (!Array.isArray(tiers) || tiers.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 2;
        cell.textContent = buy.dataset.emptyTiers || "Request a quote for this combination.";
        row.append(cell);
        tbody.append(row);
        shown = "Quote on request";
      } else {
        tiers.forEach((tier) => {
          const min = Number(tier.qty_min) || 0;
          const max = Number(tier.qty_max) || 0;
          const on = quantity >= min && (max === 0 || quantity <= max);
          const row = document.createElement("tr");
          row.dataset.qtyMin = String(min || 1);
          if (on) {
            row.classList.add("is-on");
            shown = String(tier.price || shown);
          }
          const th = document.createElement("th");
          th.scope = "row";
          th.textContent = String(tier.range || "");
          const td = document.createElement("td");
          td.textContent = String(tier.price || "");
          row.append(th, td);
          tbody.append(row);
        });
        if (!shown) {
          shown = String(tiers[0]?.price || "Quote on request");
        }
      }
      paintQuote(tiers, quantity, shown);
    };

    const inquiryUrl = () => {
      const base = buy.getAttribute("data-inquiry") || "/contact/";
      const url = new URL(base, window.location.origin);
      const selected = selectedAttrs();
      Object.entries(selected).forEach(([key, value]) => {
        url.searchParams.set(`attr_${key}`, value);
        if (/combin|combo|kit/i.test(key)) {
          url.searchParams.set("combo", value);
        }
        if (/colou?r/i.test(key)) {
          url.searchParams.set("variant", value);
        }
      });
      if (qty instanceof HTMLInputElement && qty.value) {
        url.searchParams.set("qty", qty.value);
      }
      return url.toString();
    };

    const buildCartFormData = () => {
      const productId = buy.getAttribute("data-product-id") || "";
      if (!productId) {
        return null;
      }
      syncWooQty();
      const fd = cartForm instanceof HTMLFormElement ? new FormData(cartForm) : new FormData();
      fd.set("add-to-cart", productId);
      fd.set("product_id", productId);
      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      fd.set("quantity", String(quantity));
      const variationId = currentVariationId();
      if (variationId) {
        fd.set("variation_id", variationId);
      }
      attrSelects().forEach((sel) => {
        if (sel.name && sel.value) {
          fd.set(sel.name, sel.value);
        }
      });
      const cartCfg = window.JustccellCart;
      if (cartCfg?.nonce) {
        fd.set("justccell_cart_nonce", cartCfg.nonce);
      }
      return fd;
    };

    const handleAddToCart = async (trigger) => {
      const api = window.JustccellCartApi;
      const fd = buildCartFormData();
      if (!api?.addToCart || !fd) {
        if (trigger instanceof HTMLAnchorElement) {
          window.location.href = inquiryUrl();
        }
        return;
      }

      const laserApi = window.JustccellLaserApi;
      if (laserApi?.appendToFormData instanceof Function) {
        const laserResult = await laserApi.appendToFormData(fd);
        if (!laserResult?.success) {
          return;
        }
      }

      if (trigger instanceof HTMLButtonElement) {
        await api.addToCart(fd, trigger);
        return;
      }
      if (trigger instanceof HTMLAnchorElement) {
        trigger.preventDefault();
        await api.addToCart(fd, null);
      }
    };

    submits.forEach((el) => {
      el.addEventListener("click", (event) => {
        if (el instanceof HTMLButtonElement) {
          event.preventDefault();
          handleAddToCart(el);
          return;
        }
        if (buy.getAttribute("data-product-id")) {
          event.preventDefault();
          handleAddToCart(el);
        }
      });
    });

    const refresh = () => {
      syncWooQty();
      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      paintTiers(activeTiers(), quantity);
      const href = inquiryUrl();
      submits.forEach((el) => {
        if (el instanceof HTMLAnchorElement) {
          el.href = href;
        }
      });
    };

    tbody?.addEventListener("click", (event) => {
      const row = event.target instanceof Element ? event.target.closest("tr[data-qty-min]") : null;
      if (!(row instanceof HTMLElement) || !(qty instanceof HTMLInputElement)) {
        return;
      }
      const min = Math.max(1, Number(row.dataset.qtyMin) || 1);
      qty.value = String(min);
      refresh();
    });

    attrSelects().forEach((sel) => sel.addEventListener("change", refresh));
    qty?.addEventListener("input", () => {
      syncWooQty();
      refresh();
    });
    if (window.jQuery) {
      const $form = window.jQuery(buy).find("form.variations_form");
      $form.on("show_variation", (_event, variation) => {
        activeVariation = variation && typeof variation === "object" ? variation : null;
        syncStickyFooter(activeVariation);
      });
      $form.on("found_variation hide_variation reset_data", (_event, variation) => {
        if (variation && typeof variation === "object") {
          activeVariation = variation;
        } else if (_event?.type === "hide_variation" || _event?.type === "reset_data") {
          activeVariation = null;
        }
        refresh();
      });
    }
    buy.querySelector("[data-buy-qty-down]")?.addEventListener("click", () => {
      if (qty instanceof HTMLInputElement) {
        qty.value = String(Math.max(1, (Number(qty.value) || 1) - 1));
        refresh();
      }
    });
    buy.querySelector("[data-buy-qty-up]")?.addEventListener("click", () => {
      if (qty instanceof HTMLInputElement) {
        qty.value = String(Math.max(1, (Number(qty.value) || 1) + 1));
        refresh();
      }
    });

    if (sticky instanceof HTMLElement) {
      const inner = buy.querySelector(".p-buy");
      if (inner instanceof HTMLElement && "IntersectionObserver" in window) {
        const io = new IntersectionObserver(
          (entries) => {
            const on = entries.some((entry) => entry.isIntersecting);
            sticky.hidden = on;
          },
          { threshold: 0.15 }
        );
        io.observe(inner);
      }
    }

    document.addEventListener("justccell:laser-quote", refresh);

    refresh();
  }

  const story = document.querySelector("[data-product-story]");
  if (story instanceof HTMLElement) {
    const toggle = story.querySelector("[data-story-toggle]");
    const body = story.querySelector("[data-story-body]");
    const teaser = story.querySelector("[data-story-teaser]");
    const full = story.querySelector("[data-story-full]");
    const moreLabel = story.querySelector("[data-story-label-more]");
    const lessLabel = story.querySelector("[data-story-label-less]");
    if (
      toggle instanceof HTMLButtonElement &&
      body instanceof HTMLElement &&
      teaser instanceof HTMLElement &&
      full instanceof HTMLElement
    ) {
      toggle.addEventListener("click", () => {
        const open = body.classList.toggle("is-open");
        body.classList.toggle("is-clipped", !open);
        teaser.hidden = open;
        full.hidden = !open;
        if (moreLabel instanceof HTMLElement) {
          moreLabel.hidden = open;
        }
        if (lessLabel instanceof HTMLElement) {
          lessLabel.hidden = !open;
        }
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
      });
      toggle.setAttribute("aria-expanded", "false");
    }
  }
})();
