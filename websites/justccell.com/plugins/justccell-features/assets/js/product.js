/**
 * Product gallery, 360 spin, wholesale buy box.
 * Colour / combination pickers and variation images come from WooCommerce only
 * (`form.variations_form`, `data-product_variations`) — never legacy ACF `clone_colours`.
 * Rank Ray — https://rankray.com
 */
(() => {
  const spin = document.querySelector("[data-spin]");
  const still = document.querySelector("[data-still]");
  const stillImg =
    document.querySelector("[data-still] img.p-stage-slide--current") ||
    document.querySelector("[data-still] img");
  const incomingImg = document.querySelector("[data-stage-incoming]");
  const thumbs = [...document.querySelectorAll("[data-thumb]")];
  const stage = document.querySelector("[data-product-stage]");
  const defaultImageId = Number(stage?.getAttribute("data-default-image-id") || 0);
  const defaultImageUrl = stage?.getAttribute("data-default-image-url") || "";
  const hasSpin = stage?.getAttribute("data-has-spin") === "1";
  let keepSpinOnStage = hasSpin;

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

  const normalizeUrl = (url) => (url ? String(url).split("?")[0] : "");

  const highlightThumbForSrc = (src) => {
    const target = normalizeUrl(src);
    if (!target || thumbs.length === 0) {
      return;
    }
    let matched = false;
    thumbs.forEach((thumb) => {
      const thumbSrc = normalizeUrl(
        thumb.getAttribute("data-src") ||
          (thumb.querySelector("img") instanceof HTMLImageElement
            ? thumb.querySelector("img").currentSrc || thumb.querySelector("img").src
            : "")
      );
      const on = thumbSrc === target;
      thumb.classList.toggle("is-on", on);
      if (on) {
        matched = true;
      }
    });
    if (!matched && thumbs[0]) {
      thumbs.forEach((item, i) => item.classList.toggle("is-on", i === 0));
    }
    const onThumb = thumbs.find((thumb) => thumb.classList.contains("is-on"));
    if (onThumb instanceof HTMLElement) {
      onThumb.scrollIntoView({ block: "nearest", inline: "nearest", behavior: "smooth" });
    }
  };

  const showSpinView = () => {
    if (!hasSpin) {
      return;
    }
    spin?.classList.add("is-on");
    still?.classList.remove("is-on");
    thumbs.forEach((thumb) => {
      thumb.classList.toggle("is-on", thumb.getAttribute("data-view") === "spin");
    });
  };

  const paintStageStill = (src) => {
    spin?.classList.remove("is-on");
    still?.classList.add("is-on");
    paintStill(src || variationImage.src || defaultImageUrl);
    highlightThumbForSrc(src || variationImage.src || defaultImageUrl);
  };

  const paintGalleryStill = (src) => {
    keepSpinOnStage = false;
    paintStageStill(src);
  };

  let slideToken = 0;
  let sliding = false;

  const prefersReducedMotion = () =>
    typeof window.matchMedia === "function" &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const thumbSource = (thumb) => {
    if (!(thumb instanceof HTMLElement)) {
      return "";
    }
    const img = thumb.querySelector("img");
    return (
      thumb.getAttribute("data-src") ||
      (img instanceof HTMLImageElement ? img.currentSrc || img.src : "") ||
      ""
    );
  };

  const currentThumbIndex = () => {
    const index = thumbs.findIndex((thumb) => thumb.classList.contains("is-on"));
    return index >= 0 ? index : 0;
  };

  const clearSlideClasses = (el) => {
    if (!(el instanceof HTMLElement)) {
      return;
    }
    el.classList.remove("is-from-start", "is-from-end", "is-in", "is-to-start", "is-to-end");
  };

  const slideToStill = (src, dir, done) => {
    keepSpinOnStage = false;
    spin?.classList.remove("is-on");
    still?.classList.add("is-on");

    const finishInstant = () => {
      paintStill(src);
      highlightThumbForSrc(src);
      if (typeof done === "function") {
        done();
      }
    };

    if (
      !(stillImg instanceof HTMLImageElement) ||
      !(incomingImg instanceof HTMLImageElement) ||
      !src ||
      dir === 0 ||
      prefersReducedMotion() ||
      sameUrl(stillImg.currentSrc || stillImg.src, src)
    ) {
      finishInstant();
      return;
    }

    const token = ++slideToken;
    sliding = true;
    let settled = false;
    clearSlideClasses(stillImg);
    clearSlideClasses(incomingImg);
    incomingImg.hidden = false;
    incomingImg.removeAttribute("srcset");
    incomingImg.removeAttribute("sizes");
    incomingImg.src = src;
    incomingImg.classList.add(dir > 0 ? "is-from-end" : "is-from-start");

    const finish = () => {
      if (token !== slideToken || settled) {
        return;
      }
      settled = true;
      paintStill(src);
      clearSlideClasses(stillImg);
      clearSlideClasses(incomingImg);
      incomingImg.hidden = true;
      incomingImg.removeAttribute("src");
      sliding = false;
      highlightThumbForSrc(src);
      if (typeof done === "function") {
        done();
      }
    };

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        if (token !== slideToken) {
          return;
        }
        incomingImg.classList.add("is-in");
        stillImg.classList.add(dir > 0 ? "is-to-start" : "is-to-end");
      });
    });

    stillImg.addEventListener("transitionend", finish, { once: true });
    window.setTimeout(finish, 420);
  };

  const selectGalleryIndex = (index, options = {}) => {
    const thumb = thumbs[index];
    if (!(thumb instanceof HTMLElement)) {
      return;
    }
    const src = thumbSource(thumb);
    if (!src) {
      return;
    }
    const mode = thumb.getAttribute("data-view") || "still";
    const dir = Number(options.dir) || 0;
    thumbs.forEach((item, i) => item.classList.toggle("is-on", i === index));
    if (mode === "spin" && hasSpin) {
      showView("spin", src);
      return;
    }
    if (dir !== 0) {
      slideToStill(src, dir, () => syncVariationFromThumb(src));
      return;
    }
    paintGalleryStill(src);
    syncVariationFromThumb(src);
  };

  const stepGallery = (delta) => {
    if (thumbs.length < 2 || sliding) {
      return;
    }
    const from = currentThumbIndex();
    const to = (from + delta + thumbs.length) % thumbs.length;
    selectGalleryIndex(to, { dir: delta > 0 ? 1 : -1 });
  };

  let syncVariationFromThumb = () => {};

  const showView = (mode, src) => {
    if (mode === "spin" && hasSpin) {
      keepSpinOnStage = true;
      showSpinView();
      return;
    }
    paintGalleryStill(src || variationImage.src || defaultImageUrl);
  };

  const setVariationImageData = (variation) => {
    const image = variation && typeof variation === "object" ? variation.image || {} : {};
    const nextSrc = String(
      image.full_src ||
        image.src ||
        variation?.image_url ||
        variationImage.src ||
        defaultImageUrl
    );
    const nextId = Number(variation?.image_id || image.id || 0);
    if (!nextSrc && nextId < 1) {
      return;
    }
    variationImage = {
      id: nextId,
      src: nextSrc,
    };
  };

  const applyVariationImage = (variation, options = {}) => {
    const userDriven = options.userDriven === true;
    setVariationImageData(variation);
    if (hasSpin && keepSpinOnStage && !userDriven) {
      showSpinView();
      return;
    }
    if (hasSpin && !userDriven && colourMatchesDefault()) {
      showSpinView();
      return;
    }
    if (userDriven) {
      keepSpinOnStage = false;
    }
    paintStageStill(variationImage.src);
  };

  const readFormVariations = (form) => {
    if (!(form instanceof HTMLFormElement)) {
      return [];
    }
    try {
      const raw = form.getAttribute("data-product_variations") || "[]";
      const parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : [];
    } catch {
      return [];
    }
  };

  const matchFormVariation = (form) => {
    if (!(form instanceof HTMLFormElement)) {
      return null;
    }
    const selects = [...form.querySelectorAll("select[name^='attribute_']")];
    if (selects.length === 0) {
      return null;
    }
    const attrs = {};
    selects.forEach((sel) => {
      if (!(sel instanceof HTMLSelectElement) || sel.value === "") {
        return;
      }
      attrs[sel.name] = sel.value;
    });
    if (Object.keys(attrs).length !== selects.length) {
      return null;
    }
    return (
      readFormVariations(form).find((row) =>
        Object.keys(attrs).every((key) => String(row?.attributes?.[key] ?? "") === String(attrs[key]))
      ) || null
    );
  };

  const setFormVariationId = (form, variation) => {
    if (!(form instanceof HTMLFormElement)) {
      return;
    }
    const vidInput = form.querySelector('input.variation_id, input[name="variation_id"]');
    if (vidInput instanceof HTMLInputElement) {
      vidInput.value =
        variation && variation.variation_id ? String(variation.variation_id) : "";
    }
  };

  const emitVariationEvents = (form, variation) => {
    if (!window.jQuery) {
      return;
    }
    const $form = window.jQuery(form);
    if (variation && typeof variation === "object") {
      $form.trigger("found_variation", [variation]);
      $form.trigger("show_variation", [variation]);
      return;
    }
    $form.trigger("reset_data");
    $form.trigger("hide_variation");
  };

  syncVariationFromThumb = (src) => {
    const form = document.querySelector("form.variations_form");
    if (!(form instanceof HTMLFormElement) || !src) {
      return;
    }
    const target = normalizeUrl(src);
    const rows = readFormVariations(form).filter((row) => {
      const image = row?.image || {};
      const candidates = [image.full_src, image.src, row?.image_url].filter(Boolean);
      return candidates.some((url) => normalizeUrl(url) === target);
    });
    if (rows.length === 0) {
      return;
    }
    const locked = {};
    form.querySelectorAll("select[name^='attribute_']").forEach((sel) => {
      if (sel instanceof HTMLSelectElement && sel.value) {
        locked[sel.name] = sel.value;
      }
    });
    const match =
      rows.find((row) =>
        Object.entries(locked).every(
          (entry) => String(row.attributes?.[entry[0]] ?? "") === String(entry[1])
        )
      ) || rows[0];
    if (!match?.attributes) {
      return;
    }
    Object.entries(match.attributes).forEach(([name, value]) => {
      const sel = form.querySelector(`select[name="${name}"]`);
      if (!(sel instanceof HTMLSelectElement) || !value) {
        return;
      }
      const next = String(value);
      if (sel.value !== next) {
        sel.value = next;
        sel.dispatchEvent(new Event("change", { bubbles: true }));
      }
    });
    const resolved = resolveFormVariation(form);
    if (resolved) {
      setFormVariationId(form, resolved);
      applyVariationImage(resolved);
      emitVariationEvents(form, resolved);
    }
  };

  const resolveFormVariation = (form) => {
    const matched = matchFormVariation(form);
    setFormVariationId(form, matched);
    return matched;
  };

  const ensureVariationForm = (form) => {
    if (!(form instanceof HTMLFormElement) || !window.jQuery?.fn?.wc_variation_form) {
      return;
    }
    const $form = window.jQuery(form);
    if (!$form.data("wc_variation_form")) {
      $form.wc_variation_form();
    }
  };

  const bindVariationGallery = (form) => {
    // Gallery swaps follow Woo variation JSON only (not ACF clone_colours postmeta).
    if (!(form instanceof HTMLFormElement) || form.dataset.jcVariationGallery === "1") {
      return;
    }
    form.dataset.jcVariationGallery = "1";

    const onVariation = (variation, options = {}) => {
      applyVariationImage(variation || {}, options);
    };

    const onReset = () => {
      keepSpinOnStage = hasSpin;
      variationImage = { id: defaultImageId, src: defaultImageUrl };
      if (hasSpin) {
        showSpinView();
        return;
      }
      const first = thumbs[0];
      const firstOn = !first || first.classList.contains("is-on");
      const mode = firstOn && first?.getAttribute("data-view") === "spin" ? "spin" : "still";
      showView(mode, defaultImageUrl);
    };

    form.querySelectorAll("select[name^='attribute_']").forEach((sel) => {
      sel.addEventListener("change", () => {
        const matched = resolveFormVariation(form);
        if (matched) {
          onVariation(matched, { userDriven: true });
          emitVariationEvents(form, matched);
        } else {
          onReset();
          emitVariationEvents(form, null);
        }
      });
    });

    ensureVariationForm(form);

    if (window.jQuery) {
      const $form = window.jQuery(form);
      $form.on("show_variation found_variation", (_event, variation) => {
        onVariation(variation || {});
      });
      $form.on("hide_variation reset_data reset_image", onReset);
      const matched = resolveFormVariation(form);
      if (matched) {
        onVariation(matched);
        emitVariationEvents(form, matched);
      } else {
        $form.trigger("check_variations");
      }
    }
  };

  document.querySelectorAll("form.variations_form").forEach((form) => {
    bindVariationGallery(form);
  });

  thumbs.forEach((thumb, index) => {
    thumb.addEventListener("click", () => {
      const from = currentThumbIndex();
      let dir = 0;
      if (index !== from) {
        dir = index > from ? 1 : -1;
      }
      selectGalleryIndex(index, { dir });
    });
  });

  stage?.querySelectorAll("[data-stage-prev]").forEach((btn) => {
    btn.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      stepGallery(-1);
    });
  });
  stage?.querySelectorAll("[data-stage-next]").forEach((btn) => {
    btn.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      stepGallery(1);
    });
  });

  const viewport = stage?.querySelector(".p-stage-viewport");
  if (viewport instanceof HTMLElement && thumbs.length > 1) {
    let touchStartX = 0;
    viewport.addEventListener(
      "touchstart",
      (event) => {
        if (spin?.classList.contains("is-on")) {
          return;
        }
        touchStartX = event.changedTouches[0]?.clientX || 0;
      },
      { passive: true }
    );
    viewport.addEventListener(
      "touchend",
      (event) => {
        if (spin?.classList.contains("is-on")) {
          return;
        }
        const dx = (event.changedTouches[0]?.clientX || 0) - touchStartX;
        if (Math.abs(dx) < 48) {
          return;
        }
        stepGallery(dx < 0 ? 1 : -1);
      },
      { passive: true }
    );
  }

  const buy = document.querySelector("[data-buy-box]");
  if (buy instanceof HTMLElement && buy.dataset.jcProductBound !== "1") {
    buy.dataset.jcProductBound = "1";
    const qty = buy.querySelector("[data-buy-qty]");
    const tbody = buy.querySelector("[data-buy-tiers]");
    const submits = buy.querySelectorAll("[data-buy-submit]");
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
          stock: parsed.stock && typeof parsed.stock === "object" ? parsed.stock : null,
          variation_stock:
            parsed.variation_stock && typeof parsed.variation_stock === "object" ? parsed.variation_stock : {},
        };
      }
    } catch {
      config = { tiers: [], variation_tiers: {}, tier_overrides: {}, attributes: [], stock: null, variation_stock: {} };
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

    let activeVariation = null;

    const isVariableProduct = () =>
      Boolean(config.variation_stock && Object.keys(config.variation_stock).length > 0);

    const isVariableForm = () =>
      isVariableProduct() ||
      (cartForm instanceof HTMLFormElement && cartForm.classList.contains("variations_form"));

    const activeTiers = () => {
      const vid = String(
        (activeVariation && activeVariation.variation_id) || currentVariationId() || ""
      );
      if (vid && config.variation_tiers && Object.prototype.hasOwnProperty.call(config.variation_tiers, vid)) {
        const mapped = config.variation_tiers[vid];
        return Array.isArray(mapped) ? mapped : [];
      }
      if (isVariableProduct()) {
        return [];
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
        return parts.map((part) => part.value).join("");
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

    const tierWasAmount = (tier) => {
      const now = Number(tier?.unit);
      const stored = Number(tier?.regular);
      if (Number.isFinite(stored) && Number.isFinite(now) && stored > now) {
        return stored;
      }
      return 0;
    };

    const saleAnnouncement = (was, now) => {
      const wasLabel = buy.dataset.buyWasLabel || "";
      const nowLabel = buy.dataset.buyNowLabel || "";
      return `${wasLabel} ${formatMoney(was)}, ${nowLabel} ${formatMoney(now)}`.replace(/\s+/g, " ").trim();
    };

    const paintSalePrice = (el, now, was) => {
      if (!(el instanceof HTMLElement)) {
        return;
      }
      el.replaceChildren();
      if (!Number.isFinite(now) || now <= 0) {
        return;
      }
      if (Number.isFinite(was) && was > now) {
        const del = document.createElement("del");
        del.className = "p-buy__was";
        del.setAttribute("aria-hidden", "true");
        del.textContent = formatMoney(was);
        const ins = document.createElement("ins");
        ins.className = "p-buy__now";
        ins.setAttribute("aria-hidden", "true");
        ins.textContent = formatMoney(now);
        const pair = document.createElement("span");
        pair.className = "p-buy__pair";
        pair.setAttribute("aria-hidden", "true");
        pair.append(del, ins);
        const sr = document.createElement("span");
        sr.className = "p-buy__sr";
        sr.textContent = saleAnnouncement(was, now);
        el.append(pair, sr);
        return;
      }
      el.textContent = formatMoney(now);
    };

    const stockEl = buy.querySelector("[data-buy-stock]");
    const decodeHtml = (str) => {
      const node = document.createElement("textarea");
      node.innerHTML = String(str || "");
      return node.value;
    };

    const stockMsg = (template, count) => {
      const n = Math.max(0, Number(count) || 0);
      const tpl = String(template || "%s");
      return tpl.replace("%s", n.toLocaleString("en-GB"));
    };

    const stockFromVariation = (variation) => {
      if (!variation || typeof variation !== "object") {
        return null;
      }
      if (variation.justccell_manage_stock === true || variation.justccell_manage_stock === "yes") {
        const qty = Number(variation.justccell_stock_qty);
        return {
          managed: true,
          quantity: Number.isFinite(qty) ? Math.max(0, qty) : 0,
          in_stock: Boolean(variation.justccell_in_stock ?? variation.is_in_stock),
        };
      }
      if (variation.max_qty !== "" && variation.max_qty !== null && variation.max_qty !== undefined) {
        const max = Number(variation.max_qty);
        if (Number.isFinite(max) && max >= 0) {
          return {
            managed: true,
            quantity: max,
            in_stock: Boolean(variation.is_in_stock),
          };
        }
      }
      return {
        managed: false,
        quantity: null,
        in_stock: Boolean(variation.is_in_stock ?? true),
      };
    };

    const resolveStockState = () => {
      const vid = currentVariationId();
      if (vid && config.variation_stock && config.variation_stock[vid]) {
        const row = config.variation_stock[vid];
        if (row && typeof row === "object") {
          return {
            managed: Boolean(row.managed),
            quantity: row.quantity === null || row.quantity === undefined ? null : Number(row.quantity),
            in_stock: Boolean(row.in_stock),
          };
        }
      }
      if (activeVariation) {
        return stockFromVariation(activeVariation);
      }
      if (vid && config.variation_stock && Object.keys(config.variation_stock).length > 0) {
        return null;
      }
      if (config.stock && typeof config.stock === "object") {
        return {
          managed: Boolean(config.stock.managed),
          quantity:
            config.stock.quantity === null || config.stock.quantity === undefined
              ? null
              : Number(config.stock.quantity),
          in_stock: Boolean(config.stock.in_stock),
        };
      }
      return null;
    };

    const setSubmitEnabled = (enabled) => {
      submits.forEach((el) => {
        if (el instanceof HTMLButtonElement) {
          el.disabled = !enabled;
          el.setAttribute("aria-disabled", enabled ? "false" : "true");
        }
      });
    };

    const syncStockNotice = (quantity) => {
      const qtyNum = Math.max(1, Number(quantity) || 1);
      const state = resolveStockState();
      const isVariable = config.variation_stock && Object.keys(config.variation_stock).length > 0;

      if (!(stockEl instanceof HTMLElement)) {
        if (isVariable && !currentVariationId()) {
          setSubmitEnabled(true);
          return { ok: true, message: "" };
        }
        if (!state || !state.managed) {
          setSubmitEnabled(true);
        } else if (!state.in_stock || state.quantity === 0) {
          setSubmitEnabled(false);
        } else {
          setSubmitEnabled(qtyNum <= (state.quantity ?? 0));
        }
        return { ok: true, message: "" };
      }

      stockEl.classList.remove("is-error");

      if (isVariable && !currentVariationId()) {
        stockEl.hidden = false;
        stockEl.textContent = buy.dataset.buyStockSelect || "";
        if (qty instanceof HTMLInputElement) {
          qty.removeAttribute("max");
        }
        setSubmitEnabled(true);
        return { ok: true, message: "" };
      }

      if (!state) {
        stockEl.hidden = true;
        stockEl.textContent = "";
        if (qty instanceof HTMLInputElement) {
          qty.removeAttribute("max");
        }
        setSubmitEnabled(true);
        return { ok: true, message: "" };
      }

      if (!state.in_stock || (state.managed && state.quantity === 0)) {
        stockEl.hidden = false;
        stockEl.classList.add("is-error");
        stockEl.textContent = buy.dataset.buyStockOut || "Out of stock";
        if (qty instanceof HTMLInputElement) {
          qty.setAttribute("max", "0");
        }
        setSubmitEnabled(false);
        return { ok: false, message: stockEl.textContent };
      }

      if (!state.managed || state.quantity === null) {
        stockEl.hidden = true;
        stockEl.textContent = "";
        if (qty instanceof HTMLInputElement) {
          qty.removeAttribute("max");
        }
        setSubmitEnabled(true);
        return { ok: true, message: "" };
      }

      const available = Math.max(0, Number(state.quantity) || 0);
      if (qty instanceof HTMLInputElement) {
        qty.setAttribute("max", String(available));
      }

      stockEl.hidden = false;
      if (qtyNum > available) {
        stockEl.classList.add("is-error");
        stockEl.textContent = stockMsg(
          buy.dataset.buyStockOver || "Only %s available — reduce quantity to continue",
          available
        );
        setSubmitEnabled(false);
        return { ok: false, message: stockEl.textContent };
      }

      if (qtyNum > 1) {
        stockEl.textContent = stockMsg(buy.dataset.buyStockRemaining || "%s remaining", available - qtyNum);
      } else {
        stockEl.textContent = stockMsg(buy.dataset.buyStockAvailable || "%s in stock", available);
      }
      setSubmitEnabled(true);
      return { ok: true, message: "" };
    };

    const paintQuote = (tiers, quantity) => {
      const unitEl = buy.querySelector("[data-buy-unit]");
      const unitRow = buy.querySelector("[data-buy-unit-row]");
      const totalEl = buy.querySelector("[data-buy-total]");
      const heroRow = buy.querySelector("[data-buy-total-row]");
      const quote = buy.querySelector("[data-buy-quote]");
      const hardwareRow = buy.querySelector("[data-buy-hardware-row]");
      const hardwareEl = buy.querySelector("[data-buy-hardware]");
      const laserRow = buy.querySelector("[data-buy-laser-row]");
      const laserEl = buy.querySelector("[data-buy-laser]");
      const unitWord = buy.dataset.buyUnitWord || "unit";
      const tierWord = buy.dataset.buyTierWord || "tier";
      const empty = !Array.isArray(tiers) || tiers.length === 0;
      let match = null;
      if (!empty) {
        match =
          tiers.find((tier) => {
            const min = Number(tier.qty_min) || 0;
            const max = Number(tier.qty_max) || 0;
            return quantity >= min && (max === 0 || quantity <= max);
          }) || tiers[0];
      }
      const unitNum = match ? Number(match.unit) : NaN;
      const wasUnit = match ? tierWasAmount(match) : 0;
      const canTotal = Number.isFinite(unitNum) && unitNum > 0;
      const hardwareTotal = canTotal ? unitNum * quantity : 0;
      const hardwareWas = canTotal && wasUnit > unitNum ? wasUnit * quantity : 0;
      const laser = laserQuote();
      const laserTotal = laser ? Number(laser.total) || 0 : 0;
      const grand = hardwareTotal + laserTotal;
      const grandWas = hardwareWas > 0 ? hardwareWas + laserTotal : 0;
      const hasPricing = canTotal || laser;

      if (totalEl instanceof HTMLElement) {
        if (hasPricing) {
          paintSalePrice(totalEl, grand, grandWas);
        } else {
          totalEl.textContent = "";
        }
      }
      if (heroRow instanceof HTMLElement) {
        heroRow.hidden = !hasPricing;
      }
      if (unitEl instanceof HTMLElement && unitRow instanceof HTMLElement) {
        if (canTotal && match) {
          const range = String(match.range || "").trim();
          const tierSuffix = range ? ` (${range} ${tierWord})` : "";
          paintSalePrice(unitEl, unitNum, wasUnit);
          const suffix = document.createElement("span");
          suffix.className = "p-buy__unit-suffix";
          suffix.textContent = ` / ${unitWord}${tierSuffix}`;
          unitEl.append(suffix);
          unitRow.hidden = false;
        } else {
          unitEl.textContent = "";
          unitRow.hidden = true;
        }
      }
      if (hardwareEl instanceof HTMLElement) {
        if (canTotal) {
          paintSalePrice(hardwareEl, hardwareTotal, hardwareWas);
        } else {
          hardwareEl.textContent = "";
        }
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
      if (quote instanceof HTMLElement) {
        quote.classList.toggle("is-quote", !hasPricing);
        quote.classList.toggle("has-laser", Boolean(laser));
      }
    };

    const paintTiers = (tiers, quantity) => {
      if (!(tbody instanceof HTMLElement)) {
        paintQuote(tiers, quantity);
        return;
      }
      tbody.replaceChildren();
      if (!Array.isArray(tiers) || tiers.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 2;
        cell.textContent = buy.dataset.emptyTiers || "Select options to see pricing for this combination.";
        row.append(cell);
        tbody.append(row);
      } else {
        tiers.forEach((tier) => {
          const min = Number(tier.qty_min) || 0;
          const max = Number(tier.qty_max) || 0;
          const on = quantity >= min && (max === 0 || quantity <= max);
          const row = document.createElement("tr");
          row.dataset.qtyMin = String(min || 1);
          row.dataset.qtyMax = String(max || 0);
          if (on) {
            row.classList.add("active-tier");
          }
          const th = document.createElement("th");
          th.scope = "row";
          th.textContent = String(tier.range || "");
          const td = document.createElement("td");
          paintSalePrice(td, Number(tier.unit) || 0, tierWasAmount(tier));
          if (!td.textContent && td.childNodes.length === 0) {
            td.textContent = String(tier.price || "");
          }
          row.append(th, td);
          tbody.append(row);
        });
      }
      paintQuote(tiers, quantity);
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
      } else {
        fd.delete("variation_id");
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

    const missingVariationSelection = () => {
      if (!isVariableForm()) {
        return false;
      }
      const selects = attrSelects();
      if (selects.some((sel) => !sel.value)) {
        return true;
      }
      return !currentVariationId();
    };

    let addInFlight = false;

    const handleAddToCart = async (trigger) => {
      const api = window.JustccellCartApi;
      const fd = buildCartFormData();
      const buyNotice = buy.querySelector("[data-buy-laser-notice]");
      const setBuyNotice = (msg) => {
        if (buyNotice instanceof HTMLElement) {
          buyNotice.hidden = !msg;
          buyNotice.textContent = msg ? decodeHtml(msg) : "";
        }
      };
      setBuyNotice("");

      if (addInFlight || trigger?.dataset?.busy === "1") {
        return;
      }

      if (!api?.addToCart || !fd) {
        if (trigger instanceof HTMLAnchorElement) {
          window.location.href = inquiryUrl();
        }
        return;
      }

      if (missingVariationSelection()) {
        const text =
          buy.dataset.buySelectOptions ||
          window.JustccellCart?.i18n?.selectOptions ||
          "";
        setBuyNotice(text);
        if (typeof api.toast === "function" && text) {
          api.toast(text, false);
        }
        const firstEmpty = attrSelects().find((sel) => !sel.value);
        firstEmpty?.focus();
        firstEmpty?.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
      }

      const laserApi = window.JustccellLaserApi;
      if (laserApi?.appendToFormData instanceof Function) {
        const laserResult = await laserApi.appendToFormData(fd);
        if (!laserResult?.success) {
          const msg =
            laserResult?.message ||
            buy.dataset.buyLaserIncomplete ||
            "";
          setBuyNotice(msg);
          if (typeof laserApi.showError === "function" && msg) {
            laserApi.showError(msg);
          }
          if (typeof api.toast === "function" && msg) {
            api.toast(msg, false);
          }
          const laserRoot = document.querySelector("[data-laser-engraving]");
          const laserError = laserRoot?.querySelector("[data-laser-error]");
          const scrollTarget = laserError instanceof HTMLElement ? laserError : laserRoot;
          scrollTarget?.scrollIntoView({ behavior: "smooth", block: "center" });
          return;
        }
      }

      const showCartError = (msg) => {
        const text =
          msg || window.JustccellCart?.i18n?.error || "";
        setBuyNotice(text);
        if (typeof api.toast === "function" && text) {
          api.toast(text, false);
        }
      };

      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      const stockCheck = syncStockNotice(quantity);
      if (!stockCheck.ok) {
        showCartError(stockCheck.message);
        return;
      }

      addInFlight = true;
      try {
        if (trigger instanceof HTMLButtonElement) {
          const result = await api.addToCart(fd, trigger);
          if (result?.skipped) {
            return;
          }
          if (!result?.success) {
            showCartError(result?.message);
          }
          return;
        }
        if (trigger instanceof HTMLAnchorElement) {
          const result = await api.addToCart(fd, null);
          if (!result?.skipped && !result?.success) {
            showCartError(result?.message);
          }
        }
      } finally {
        addInFlight = false;
      }
    };

    if (cartForm instanceof HTMLFormElement) {
      cartForm.addEventListener("submit", (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
      });
    }

    submits.forEach((el) => {
      el.addEventListener("click", (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        if (el instanceof HTMLButtonElement || buy.getAttribute("data-product-id")) {
          handleAddToCart(el);
        }
      });
    });

    const refresh = () => {
      if (cartForm instanceof HTMLFormElement) {
        const matched = resolveFormVariation(cartForm);
        if (matched) {
          activeVariation = matched;
        } else if (!currentVariationId()) {
          activeVariation = null;
        }
      }
      syncWooQty();
      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      paintTiers(activeTiers(), quantity);
      syncStockNotice(quantity);
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
    const variationIdInput = buy.querySelector("input.variation_id, input[name='variation_id']");
    if (variationIdInput instanceof HTMLInputElement) {
      variationIdInput.addEventListener("change", refresh);
    }
    qty?.addEventListener("input", () => {
      syncWooQty();
      refresh();
    });
    if (window.jQuery) {
      const $form = window.jQuery(buy).find("form.variations_form");
      $form.on("show_variation found_variation", (_event, variation) => {
        activeVariation = variation && typeof variation === "object" ? variation : null;
        refresh();
      });
      $form.on("hide_variation reset_data", () => {
        activeVariation = null;
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

    document.addEventListener("justccell:laser-quote", refresh);
    document.addEventListener("justccell:laser-clear-notice", () => {
      const buyNotice = buy.querySelector("[data-buy-laser-notice]");
      if (buyNotice instanceof HTMLElement) {
        buyNotice.hidden = true;
        buyNotice.textContent = "";
      }
    });

    if (cartForm instanceof HTMLFormElement) {
      ensureVariationForm(cartForm);
    }

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

  const bindThumbsRail = (rail) => {
    if (!(rail instanceof HTMLElement)) {
      return;
    }
    const scroller = rail.querySelector("[data-product-thumbs]");
    const prev = rail.querySelector("[data-thumbs-prev]");
    const next = rail.querySelector("[data-thumbs-next]");
    if (!(scroller instanceof HTMLElement)) {
      return;
    }

    const step = () => {
      const first = scroller.querySelector(".p-thumbs__btn");
      if (!(first instanceof HTMLElement)) {
        return Math.max(72, Math.round(scroller.clientWidth * 0.8));
      }
      const styles = window.getComputedStyle(scroller);
      const gap = Number.parseFloat(styles.columnGap || styles.gap || "8") || 8;
      return Math.round(first.getBoundingClientRect().width + gap);
    };

    const sync = () => {
      const max = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
      const overflow = max > 4;
      const atStart = scroller.scrollLeft <= 4;
      const atEnd = scroller.scrollLeft >= max - 4;
      rail.classList.toggle("is-overflow", overflow);
      rail.classList.toggle("is-overflow-start", overflow && !atStart);
      rail.classList.toggle("is-overflow-end", overflow && !atEnd);
      if (prev instanceof HTMLButtonElement) {
        prev.hidden = !overflow || atStart;
        prev.disabled = atStart;
      }
      if (next instanceof HTMLButtonElement) {
        next.hidden = !overflow || atEnd;
        next.disabled = atEnd;
      }
    };

    prev?.addEventListener("click", () => {
      scroller.scrollBy({ left: -step(), behavior: "smooth" });
    });
    next?.addEventListener("click", () => {
      scroller.scrollBy({ left: step(), behavior: "smooth" });
    });
    scroller.addEventListener("scroll", sync, { passive: true });
    window.addEventListener("resize", sync, { passive: true });
    if (typeof ResizeObserver === "function") {
      new ResizeObserver(sync).observe(scroller);
    }
    sync();
  };

  document.querySelectorAll("[data-thumbs-rail]").forEach(bindThumbsRail);
})();
