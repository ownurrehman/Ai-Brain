/**
 * Product gallery, 360 spin, wholesale buy box.
 * Colour / combination pickers and variation images come from WooCommerce only
 * (`form.variations_form`, `data-product_variations`) — never legacy ACF `clone_colours`.
 * Rank Ray — https://rankray.com
 */
(() => {
  const spin = document.querySelector("[data-spin]");
  const still = document.querySelector("[data-still]");
  const stillImg = document.querySelector("[data-still] img");
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

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      const img = thumb.querySelector("img");
      const src =
        thumb.getAttribute("data-src") ||
        (img instanceof HTMLImageElement ? img.currentSrc || img.src : "") ||
        "";
      if (!src) {
        return;
      }
      const mode = thumb.getAttribute("data-view") || "still";
      thumbs.forEach((item) => item.classList.toggle("is-on", item === thumb));
      if (mode === "spin" && hasSpin) {
        showView("spin", src);
        return;
      }
      paintGalleryStill(src);
      syncVariationFromThumb(src);
    });
  });

  const buy = document.querySelector("[data-buy-box]");
  if (buy instanceof HTMLElement) {
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
        stockEl.textContent = buy.dataset.buyStockSelect || "Select options to see stock availability";
        if (qty instanceof HTMLInputElement) {
          qty.removeAttribute("max");
        }
        setSubmitEnabled(false);
        return { ok: false, message: stockEl.textContent };
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

    let activeVariation = null;

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
      const canTotal = Number.isFinite(unitNum) && unitNum > 0;
      const hardwareTotal = canTotal ? unitNum * quantity : 0;
      const laser = laserQuote();
      const laserTotal = laser ? Number(laser.total) || 0 : 0;
      const grand = hardwareTotal + laserTotal;
      const hasPricing = canTotal || laser;

      if (totalEl instanceof HTMLElement) {
        if (hasPricing) {
          totalEl.textContent = formatMoney(grand);
        } else {
          totalEl.textContent = empty
            ? buy.dataset.emptyTiers || "Price on request"
            : "Price on request";
        }
      }
      if (heroRow instanceof HTMLElement) {
        heroRow.hidden = false;
      }
      if (unitEl instanceof HTMLElement && unitRow instanceof HTMLElement) {
        if (canTotal && match) {
          const range = String(match.range || "").trim();
          const tierSuffix = range ? ` (${range} ${tierWord})` : "";
          unitEl.textContent = `${formatMoney(unitNum)} / ${unitWord}${tierSuffix}`;
          unitRow.hidden = false;
        } else {
          unitEl.textContent = "";
          unitRow.hidden = true;
        }
      }
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
          if (on) {
            row.classList.add("active-tier");
          }
          const th = document.createElement("th");
          th.scope = "row";
          th.textContent = String(tier.range || "");
          const td = document.createElement("td");
          td.textContent = String(tier.price || "");
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
      const buyNotice = buy.querySelector("[data-buy-laser-notice]");
      const setBuyNotice = (msg) => {
        if (buyNotice instanceof HTMLElement) {
          buyNotice.hidden = !msg;
          buyNotice.textContent = msg ? decodeHtml(msg) : "";
        }
      };
      setBuyNotice("");

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
          const msg =
            laserResult?.message ||
            "Complete laser engraving (text or logo) before adding to cart.";
          setBuyNotice(msg);
          if (typeof laserApi.showError === "function") {
            laserApi.showError(msg);
          }
          if (typeof api.toast === "function") {
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
          msg || "Could not add to cart. Check your options and try again.";
        setBuyNotice(text);
        if (typeof api.toast === "function") {
          api.toast(text, false);
        }
      };

      const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
      const stockCheck = syncStockNotice(quantity);
      if (!stockCheck.ok) {
        showCartError(stockCheck.message);
        return;
      }

      if (trigger instanceof HTMLButtonElement) {
        const result = await api.addToCart(fd, trigger);
        if (!result?.success) {
          showCartError(result?.message);
        }
        return;
      }
      if (trigger instanceof HTMLAnchorElement) {
        trigger.preventDefault();
        const result = await api.addToCart(fd, null);
        if (!result?.success) {
          showCartError(result?.message);
        }
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
    qty?.addEventListener("input", () => {
      syncWooQty();
      refresh();
    });
    if (window.jQuery) {
      const $form = window.jQuery(buy).find("form.variations_form");
      $form.on("show_variation", (_event, variation) => {
        activeVariation = variation && typeof variation === "object" ? variation : null;
        const quantity = qty instanceof HTMLInputElement ? Math.max(1, Number(qty.value) || 1) : 1;
        syncStockNotice(quantity);
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

    document.addEventListener("justccell:laser-quote", refresh);
    document.addEventListener("justccell:laser-clear-notice", () => {
      const buyNotice = buy.querySelector("[data-buy-laser-notice]");
      if (buyNotice instanceof HTMLElement) {
        buyNotice.hidden = true;
        buyNotice.textContent = "";
      }
    });

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
