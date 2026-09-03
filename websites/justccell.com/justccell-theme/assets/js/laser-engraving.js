/**
 * Inline laser engraving — Fabric.js editor (§5).
 * Rank Ray — https://rankray.com
 */
(() => {
  const cfg = window.JustccellLaser;
  const root = document.querySelector("[data-laser-engraving]");
  if (!cfg || !cfg.enabled || !(root instanceof HTMLElement)) {
    return;
  }

  const toggle = root.querySelector("[data-laser-toggle]");
  const panel = root.querySelector("[data-laser-panel]");

  if (cfg.editorReady === false) {
    const showIncomplete = (msg) => {
      const errorEl = root.querySelector("[data-laser-error]");
      if (errorEl instanceof HTMLElement) {
        errorEl.hidden = !msg;
        errorEl.textContent = msg || "";
      }
    };

    toggle?.addEventListener("change", () => {
      const open = toggle instanceof HTMLInputElement && toggle.checked;
      if (panel instanceof HTMLElement) {
        panel.classList.toggle("is-open", open);
        panel.setAttribute("aria-hidden", open ? "false" : "true");
      }
      if (!open) {
        showIncomplete("");
      }
    });

    window.JustccellLaserApi = {
      isActive: () => toggle instanceof HTMLInputElement && toggle.checked,
      quote: () => ({
        active: toggle instanceof HTMLInputElement && toggle.checked,
        setup: 0,
        unit: 0,
        qty: 1,
        total: 0,
      }),
      async appendToFormData(fd) {
        if (!(toggle instanceof HTMLInputElement) || !toggle.checked) {
          fd.set("justccell_laser_enabled", "0");
          return { success: true };
        }
        const message =
          cfg.i18n?.incomplete ||
          "Laser engraving is enabled for this product but the editor is still being configured.";
        showIncomplete(message);
        return { success: false, message };
      },
      showError: showIncomplete,
    };
    return;
  }

  if (typeof window.fabric === "undefined") {
    return;
  }
  const canvasEl = root.querySelector("[data-laser-canvas]");
  const errorEl = root.querySelector("[data-laser-error]");
  const fontSelect = root.querySelector("[data-laser-font]");
  const sizeRange = root.querySelector("[data-laser-size]");
  const spacingRange = root.querySelector("[data-laser-spacing]");
  const setupEl = root.querySelector("[data-laser-setup]");
  const unitEl = root.querySelector("[data-laser-unit]");
  const totalEl = root.querySelector("[data-laser-total]");
  const removeBtn = root.querySelector("[data-laser-remove]");
  const whatsappInput = root.querySelector("[data-laser-whatsapp]");

  if (!(canvasEl instanceof HTMLCanvasElement)) {
    return;
  }

  const buyWrap = document.querySelector("[data-buy-box]");
  const buyQty = buyWrap?.querySelector("[data-buy-qty]");

  const W = Number(cfg.canvas?.width) || 640;
  const H = Number(cfg.canvas?.height) || 640;
  const zones = Array.isArray(cfg.safeZones) ? cfg.safeZones : [];
  const primaryZone = zones[0] || { x: 0, y: 0, width: W, height: H };
  const zoneBox = {
    x: Number(primaryZone.x) || 0,
    y: Number(primaryZone.y) || 0,
    w: Number(primaryZone.width) || W,
    h: Number(primaryZone.height) || H,
  };


  const overlayRects = [];

  const canvas = new window.fabric.Canvas(canvasEl, {
    width: W,
    height: H,
    preserveObjectStacking: true,
    selection: true,
  });

  const paintOverlays = () => {
    overlayRects.splice(0).forEach((rect) => canvas.remove(rect));
    zones.forEach((z) => {
      const rect = new window.fabric.Rect({
        left: Number(z.x) || 0,
        top: Number(z.y) || 0,
        width: Number(z.width) || 0,
        height: Number(z.height) || 0,
        fill: "rgba(220, 38, 38, 0.04)",
        stroke: "#dc2626",
        strokeDashArray: [8, 5],
        strokeWidth: 2,
        selectable: false,
        evented: false,
        excludeFromExport: true,
        objectCaching: false,
      });
      overlayRects.push(rect);
      canvas.add(rect);
      canvas.bringToFront(rect);
    });
  };

  const setBackground = () => {
    const url = cfg.canvas?.backgroundUrl;
    if (!url) {
      paintOverlays();
      return;
    }
    window.fabric.Image.fromURL(
      url,
      (img) => {
        img.set({ selectable: false, evented: false, originX: "left", originY: "top" });
        const scale = Math.min(W / (img.width || W), H / (img.height || H));
        img.scale(scale);
        canvas.setBackgroundImage(img, () => {
          paintOverlays();
          canvas.requestRenderAll();
        });
      },
      { crossOrigin: "anonymous" }
    );
  };

  /**
   * Strict AABB clamp into the primary ACF safe zone.
   * Scales down when larger than the zone, then pins left/top so the
   * bounding rect cannot exit the box.
   */
  const clampObject = (obj) => {
    if (!obj || obj.excludeFromExport) {
      return;
    }

    const { x: zx, y: zy, w: zw, h: zh } = zoneBox;

    obj.setCoords();
    let bound = obj.getBoundingRect(true);

    if (bound.width > zw || bound.height > zh) {
      const factor = Math.min(zw / Math.max(bound.width, 1), zh / Math.max(bound.height, 1));
      obj.scaleX = (obj.scaleX || 1) * factor;
      obj.scaleY = (obj.scaleY || 1) * factor;
      obj.setCoords();
      bound = obj.getBoundingRect(true);
    }

    let left = obj.left || 0;
    let top = obj.top || 0;

    if (bound.left < zx) {
      left += zx - bound.left;
    }
    if (bound.top < zy) {
      top += zy - bound.top;
    }

    obj.set({ left, top });
    obj.setCoords();
    bound = obj.getBoundingRect(true);

    if (bound.left + bound.width > zx + zw) {
      left -= bound.left + bound.width - (zx + zw);
    }
    if (bound.top + bound.height > zy + zh) {
      top -= bound.top + bound.height - (zy + zh);
    }

    obj.set({ left, top });
    obj.setCoords();

    // Final hard pin after rotation / odd origins.
    bound = obj.getBoundingRect(true);
    left = obj.left || 0;
    top = obj.top || 0;
    if (bound.left < zx) {
      left += zx - bound.left;
    }
    if (bound.top < zy) {
      top += zy - bound.top;
    }
    if (bound.left + bound.width > zx + zw) {
      left -= bound.left + bound.width - (zx + zw);
    }
    if (bound.top + bound.height > zy + zh) {
      top -= bound.top + bound.height - (zy + zh);
    }
    obj.set({ left, top });
    obj.setCoords();
  };

  canvas.on("object:moving", (e) => clampObject(e.target));
  canvas.on("object:scaling", (e) => clampObject(e.target));
  canvas.on("object:rotating", (e) => clampObject(e.target));
  canvas.on("object:modified", (e) => clampObject(e.target));

  const money = (n) => {
    const symbol = cfg.currency?.symbol || "£";
    const precision = Number(cfg.currency?.precision ?? 2);
    return `${symbol}\u00A0${Number(n || 0).toFixed(precision)}`;
  };

  const setMoney = (el, n) => {
    if (el instanceof HTMLElement) {
      el.textContent = money(n);
    }
  };

  const ppuForQty = (qty) => {
    const tiers = Array.isArray(cfg.tiers) ? cfg.tiers : [];
    const q = Math.max(1, Number(qty) || 1);
    for (const tier of tiers) {
      const min = Number(tier.minQty) || 1;
      const max = Number(tier.maxQty) || 0;
      if (q >= min && (max === 0 || q <= max)) {
        return Number(tier.pricePerUnit) || 0;
      }
    }
    return 0;
  };

  const currentQty = () => {
    if (buyQty instanceof HTMLInputElement && buyQty.value) {
      return Math.max(1, Number(buyQty.value) || 1);
    }
    return 1;
  };

  const highlightTier = (qty) => {
    const rows = root.querySelectorAll("[data-laser-tier]");
    if (!rows.length) {
      return;
    }
    const q = Math.max(1, Number(qty) || 1);
    let matched = false;
    rows.forEach((row) => {
      if (!(row instanceof HTMLElement)) {
        return;
      }
      const min = Number(row.dataset.min) || 1;
      const max = Number(row.dataset.max) || 0;
      const on = !matched && q >= min && (max === 0 || q <= max);
      if (on) {
        matched = true;
      }
      row.classList.toggle("is-on", on);
    });
  };

  const refreshSummary = () => {
    const qty = currentQty();
    const setup = Number(cfg.setupFee) || 0;
    const unit = ppuForQty(qty);
    const total = setup + unit * qty;
    setMoney(setupEl, setup);
    setMoney(unitEl, unit);
    setMoney(totalEl, total);
    highlightTier(qty);
  };

  const quote = () => {
    const active = toggle instanceof HTMLInputElement && toggle.checked;
    const qty = currentQty();
    const setup = Number(cfg.setupFee) || 0;
    const unit = ppuForQty(qty);
    return {
      active,
      setup,
      unit,
      qty,
      total: active ? setup + unit * qty : 0,
    };
  };

  const emitQuote = () => {
    refreshSummary();
    document.dispatchEvent(new CustomEvent("justccell:laser-quote", { detail: quote() }));
  };

  const collectLayout = () => ({
    canvas: { width: W, height: H },
    objects: canvas
      .getObjects()
      .filter((o) => o && !o.excludeFromExport)
      .map((o) => ({
        type: String(o.type || ""),
        text: String(o.text || ""),
        fontFamily: String(o.fontFamily || ""),
        fontSize: Number(o.fontSize) || 0,
        charSpacing: Number(o.charSpacing) || 0,
        left: Number(o.left) || 0,
        top: Number(o.top) || 0,
        scaleX: Number(o.scaleX) || 1,
        scaleY: Number(o.scaleY) || 1,
        angle: Number(o.angle) || 0,
      })),
  });

  const storageKey = () => "jc-laser:" + String(cfg.productId || "0");

  const persistHidden = (layout) => {
    const enabledInput = root.querySelector('[data-laser-field="enabled"]');
    const layoutInput = root.querySelector('[data-laser-field="layout"]');
    if (enabledInput instanceof HTMLInputElement) {
      enabledInput.value = toggle instanceof HTMLInputElement && toggle.checked ? "1" : "0";
    }
    if (layoutInput instanceof HTMLInputElement) {
      layoutInput.value = JSON.stringify(layout || collectLayout());
    }
  };

  const persistSession = () => {
    const layout = collectLayout();
    persistHidden(layout);
    if (hydrating) {
      return layout;
    }
    const payload = {
      v: 2,
      enabled: toggle instanceof HTMLInputElement && toggle.checked,
      font: fontSelect instanceof HTMLSelectElement ? fontSelect.value : "",
      size: sizeRange instanceof HTMLInputElement ? sizeRange.value : "",
      spacing: spacingRange instanceof HTMLInputElement ? spacingRange.value : "",
      whatsapp: whatsappInput instanceof HTMLInputElement ? whatsappInput.value : "",
      canvas: canvas.toJSON(),
      layout,
    };
    try {
      sessionStorage.setItem(storageKey(), JSON.stringify(payload));
    } catch {
      // Quota exceeded — hidden layout input still holds compact metadata.
    }
    return layout;
  };

  let persistTimer = 0;
  let hydrating = true;
  const schedulePersist = () => {
    if (hydrating) {
      return;
    }
    window.clearTimeout(persistTimer);
    persistTimer = window.setTimeout(() => {
      persistSession();
    }, 400);
  };

  const markSaved = () => {
    const status = root.querySelector("[data-laser-save-status]");
    if (!(status instanceof HTMLElement)) {
      return;
    }
    status.hidden = false;
    status.textContent = cfg.i18n?.saved || "Engraving saved for this session";
    window.setTimeout(() => {
      status.hidden = true;
    }, 2500);
  };

  const setOpen = (open) => {
    if (panel instanceof HTMLElement) {
      panel.classList.toggle("is-open", open);
      panel.setAttribute("aria-hidden", open ? "false" : "true");
    }
    if (open) {
      window.requestAnimationFrame(() => {
        canvas.calcOffset();
        canvas.requestRenderAll();
      });
    }
    if (!open) {
      showError("");
    }
    emitQuote();
    schedulePersist();
  };

  toggle?.addEventListener("change", () => {
    setOpen(toggle instanceof HTMLInputElement && toggle.checked);
  });

  const activeFont = () =>
    fontSelect instanceof HTMLSelectElement && fontSelect.value
      ? fontSelect.value
      : "Montserrat, sans-serif";

  const bringOverlaysFront = () => {
    overlayRects.forEach((rect) => canvas.bringToFront(rect));
  };

  root.querySelector("[data-laser-add-text]")?.addEventListener("click", () => {
    const size = sizeRange instanceof HTMLInputElement ? Number(sizeRange.value) || 28 : 28;
    const spacing = spacingRange instanceof HTMLInputElement ? Number(spacingRange.value) || 0 : 0;
    const text = new window.fabric.IText("Your brand", {
      left: zoneBox.x + 16,
      top: zoneBox.y + 16,
      fill: "#111111",
      fontFamily: activeFont(),
      fontSize: size,
      charSpacing: spacing,
    });
    canvas.add(text);
    canvas.setActiveObject(text);
    clampObject(text);
    bringOverlaysFront();
    canvas.requestRenderAll();
  });

  const applyTypeControls = () => {
    const obj = canvas.getActiveObject();
    if (!obj || (obj.type !== "i-text" && obj.type !== "text")) {
      return;
    }
    obj.set("fontFamily", activeFont());
    if (sizeRange instanceof HTMLInputElement) {
      obj.set("fontSize", Number(sizeRange.value) || 28);
    }
    if (spacingRange instanceof HTMLInputElement) {
      obj.set("charSpacing", Number(spacingRange.value) || 0);
    }
    clampObject(obj);
    canvas.requestRenderAll();
  };

  fontSelect?.addEventListener("change", applyTypeControls);
  sizeRange?.addEventListener("input", applyTypeControls);
  spacingRange?.addEventListener("input", applyTypeControls);

  /**
   * Remove the active canvas object (not while editing text inline).
   */
  const removeSelected = () => {
    const obj = canvas.getActiveObject();
    if (!obj || obj.excludeFromExport) {
      return;
    }
    if (obj.isEditing) {
      return;
    }
    if (obj.type === "activeSelection" && typeof obj.forEachObject === "function") {
      obj.forEachObject((child) => {
        if (child && !child.excludeFromExport) {
          canvas.remove(child);
        }
      });
      canvas.discardActiveObject();
    } else {
      canvas.remove(obj);
      canvas.discardActiveObject();
    }
    bringOverlaysFront();
    canvas.requestRenderAll();
  };

  removeBtn?.addEventListener("click", removeSelected);

  window.addEventListener("keydown", (event) => {
    if (!(toggle instanceof HTMLInputElement) || !toggle.checked) {
      return;
    }
    if (event.key !== "Delete" && event.key !== "Backspace") {
      return;
    }
    const target = event.target;
    if (
      target instanceof HTMLInputElement ||
      target instanceof HTMLTextAreaElement ||
      target instanceof HTMLSelectElement ||
      (target instanceof HTMLElement && target.isContentEditable)
    ) {
      return;
    }
    const obj = canvas.getActiveObject();
    if (!obj || obj.isEditing) {
      return;
    }
    event.preventDefault();
    removeSelected();
  });

  /**
   * Load file with EXIF orientation applied (phone photos) when supported.
   */
  const readFileAsDataUrl = (file) =>
    new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(String(reader.result || ""));
      reader.onerror = () => reject(new Error("read failed"));
      reader.readAsDataURL(file);
    });

  const canvasToImage = (sourceCanvas) =>
    new Promise((resolve, reject) => {
      const out = new Image();
      out.onload = () => resolve(out);
      out.onerror = reject;
      out.src = sourceCanvas.toDataURL("image/png");
    });

  const loadImageElement = (src) =>
    new Promise((resolve, reject) => {
      const el = new Image();
      el.onload = () => resolve(el);
      el.onerror = reject;
      el.src = src;
    });

  const loadImageFromFile = async (file) => {
    if (typeof createImageBitmap === "function") {
      try {
        const bitmap = await createImageBitmap(file, { imageOrientation: "from-image" });
        const off = document.createElement("canvas");
        off.width = bitmap.width;
        off.height = bitmap.height;
        const ctx = off.getContext("2d");
        if (!ctx) {
          throw new Error("no ctx");
        }
        ctx.drawImage(bitmap, 0, 0);
        if (typeof bitmap.close === "function") {
          bitmap.close();
        }
        return canvasToImage(off);
      } catch {
        // Fall back to data URL when EXIF / bitmap decode fails.
      }
    }
    return loadImageElement(await readFileAsDataUrl(file));
  };

  /**
   * Laser stencil: dark marks on transparent ground, soft edges for anti-aliasing.
   * Uses inverted luminance so coloured logos on white stock keep their shape.
   */
  const toMonochrome = (imgEl) =>
    new Promise((resolve, reject) => {
      const w = imgEl.naturalWidth || imgEl.width;
      const h = imgEl.naturalHeight || imgEl.height;
      if (!w || !h) {
        reject(new Error("bad image"));
        return;
      }
      const off = document.createElement("canvas");
      off.width = w;
      off.height = h;
      const ctx = off.getContext("2d");
      if (!ctx) {
        reject(new Error("no ctx"));
        return;
      }
      ctx.drawImage(imgEl, 0, 0);
      const data = ctx.getImageData(0, 0, w, h);
      const px = data.data;
      const inkFloor = 16;
      for (let i = 0; i < px.length; i += 4) {
        const r = px[i];
        const g = px[i + 1];
        const b = px[i + 2];
        const a = px[i + 3];
        if (a < 8) {
          px[i + 3] = 0;
          continue;
        }
        const lum = 0.299 * r + 0.587 * g + 0.114 * b;
        const ink = (255 - lum) * (a / 255);
        if (ink < inkFloor) {
          px[i + 3] = 0;
          continue;
        }
        const alpha = Math.min(255, Math.round(((ink - inkFloor) / (255 - inkFloor)) * 255));
        px[i] = 0;
        px[i + 1] = 0;
        px[i + 2] = 0;
        px[i + 3] = alpha;
      }
      ctx.putImageData(data, 0, 0);
      canvasToImage(off).then(resolve).catch(reject);
    });

  const showError = (msg) => {
    if (!(errorEl instanceof HTMLElement)) {
      return;
    }
    errorEl.hidden = !msg;
    errorEl.textContent = msg || "";
  };

  root.querySelector("[data-laser-upload]")?.addEventListener("change", (event) => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.files?.[0]) {
      return;
    }
    const file = input.files[0];
    (async () => {
      try {
        showError("");
        const img = await loadImageFromFile(file);
        const mono = await toMonochrome(img);
        window.fabric.Image.fromURL(mono.src, (fabImg) => {
          const imgW = fabImg.width || 1;
          const imgH = fabImg.height || 1;
          const maxW = Math.max(40, zoneBox.w * 0.85);
          const maxH = Math.max(40, zoneBox.h * 0.85);
          const scale = Math.min(maxW / imgW, maxH / imgH);
          const scaledW = imgW * scale;
          const scaledH = imgH * scale;
          fabImg.set({
            left: zoneBox.x + Math.max(0, (zoneBox.w - scaledW) / 2),
            top: zoneBox.y + Math.max(0, (zoneBox.h - scaledH) / 2),
            originX: "left",
            originY: "top",
            scaleX: scale,
            scaleY: scale,
          });
          canvas.add(fabImg);
          canvas.setActiveObject(fabImg);
          clampObject(fabImg);
          bringOverlaysFront();
          canvas.requestRenderAll();
        });
      } catch {
        showError(cfg.i18n?.uploadFailed || "Could not process that image. Try a PNG logo on a transparent background.");
      }
      input.value = "";
    })();
  });

  const collectText = () =>
    canvas
      .getObjects()
      .filter((o) => o && !o.excludeFromExport && (o.type === "i-text" || o.type === "text"))
      .map((o) => String(o.text || "").trim())
      .filter(Boolean)
      .join(" · ");

  const hasDesign = () =>
    canvas.getObjects().some((o) => o && !o.excludeFromExport && o.type !== "rect");

  const MAX_ARTWORK_CHARS = 1200000;

  const exportArtwork = async () => {
    overlayRects.forEach((r) => {
      r.visible = false;
    });
    canvas.discardActiveObject();
    canvas.requestRenderAll();

    const artwork = canvas.toDataURL({ format: "png", multiplier: 1, enableRetinaScaling: false });
    const preview = canvas.toDataURL({ format: "jpeg", quality: 0.65, multiplier: 0.35 });

    overlayRects.forEach((r) => {
      r.visible = true;
    });
    canvas.requestRenderAll();

    if (artwork.length > MAX_ARTWORK_CHARS) {
      return {
        success: false,
        message: cfg.i18n?.payloadTooLarge || "Engraving file is too large. Simplify the design.",
      };
    }

    return {
      success: true,
      artwork,
      preview,
      text: collectText(),
      cost: (Number(cfg.setupFee) || 0) + ppuForQty(currentQty()) * currentQty(),
      unit: ppuForQty(currentQty()),
    };
  };

  window.JustccellLaserApi = {
    isActive: () => toggle instanceof HTMLInputElement && toggle.checked,
    quote,
    save() {
      persistSession();
      markSaved();
      emitQuote();
      return quote();
    },
    async appendToFormData(fd) {
      if (!(fd instanceof FormData)) {
        return { success: false, message: "Cart payload is invalid." };
      }
      if (!(toggle instanceof HTMLInputElement) || !toggle.checked) {
        fd.set("justccell_laser_enabled", "0");
        persistHidden({ canvas: { width: W, height: H }, objects: [] });
        return { success: true };
      }

      showError("");
      refreshSummary();

      if (!hasDesign()) {
        const message = cfg.i18n?.needDesign || "Add text or a logo before adding to cart.";
        showError(message);
        return { success: false, message };
      }

      const whatsapp =
        whatsappInput instanceof HTMLInputElement ? whatsappInput.value.trim() : "";
      if (cfg.whatsappRequired && !whatsapp) {
        const message =
          cfg.i18n?.whatsappRequired ||
          "Enter a WhatsApp number so we can send your artwork proof before production.";
        showError(message);
        return { success: false, message };
      }

      const exported = await exportArtwork();
      if (!exported.success) {
        showError(exported.message || "Could not save engraving.");
        return exported;
      }

      const qty = currentQty();
      const layout = persistSession();
      fd.set("justccell_laser_enabled", "1");
      fd.set("justccell_laser_artwork", exported.artwork);
      fd.set("justccell_laser_preview", exported.preview);
      fd.set("justccell_laser_text", exported.text);
      fd.set("justccell_laser_layout", JSON.stringify(layout));
      fd.set("justccell_laser_whatsapp", whatsapp);
      fd.set("justccell_laser_cost", String(Number(exported.cost || 0).toFixed(4)));
      fd.set("justccell_laser_setup_fee", String(Number(cfg.setupFee) || 0));
      fd.set("justccell_laser_unit", String(Number(exported.unit || 0).toFixed(4)));
      fd.set("quantity", String(qty));
      return { success: true };
    },
    showError,
  };

  buyQty?.addEventListener("input", emitQuote);
  whatsappInput?.addEventListener("input", schedulePersist);
  buyWrap?.querySelector("[data-buy-qty-up]")?.addEventListener("click", () => {
    window.setTimeout(emitQuote, 0);
  });
  buyWrap?.querySelector("[data-buy-qty-down]")?.addEventListener("click", () => {
    window.setTimeout(emitQuote, 0);
  });
  buyWrap?.querySelector("[data-buy-table]")?.addEventListener("click", () => {
    window.setTimeout(emitQuote, 0);
  });

  canvas.on("object:added", schedulePersist);
  canvas.on("object:modified", schedulePersist);
  canvas.on("object:removed", schedulePersist);

  root.querySelector("[data-laser-save]")?.addEventListener("click", () => {
    persistSession();
    markSaved();
    emitQuote();
  });

  const restoreSession = () => {
    let raw = "";
    try {
      raw = sessionStorage.getItem(storageKey()) || "";
    } catch {
      return false;
    }
    if (!raw) {
      return false;
    }
    let payload;
    try {
      payload = JSON.parse(raw);
    } catch {
      return false;
    }
    if (!payload || (payload.v !== 1 && payload.v !== 2)) {
      return false;
    }
    if (fontSelect instanceof HTMLSelectElement && payload.font) {
      fontSelect.value = payload.font;
    }
    if (sizeRange instanceof HTMLInputElement && payload.size) {
      sizeRange.value = payload.size;
    }
    if (spacingRange instanceof HTMLInputElement && payload.spacing) {
      spacingRange.value = payload.spacing;
    }
    if (whatsappInput instanceof HTMLInputElement && payload.whatsapp) {
      whatsappInput.value = payload.whatsapp;
    }
    const finish = () => {
      hydrating = false;
      if (toggle instanceof HTMLInputElement && payload.enabled) {
        toggle.checked = true;
        setOpen(true);
      } else {
        setOpen(false);
      }
      persistHidden(payload.layout);
    };
    if (payload.canvas && typeof canvas.loadFromJSON === "function") {
      canvas.loadFromJSON(payload.canvas, () => {
        paintOverlays();
        canvas.requestRenderAll();
        finish();
      });
      return true;
    }
    finish();
    return false;
  };

  setBackground();
  if (!restoreSession()) {
    hydrating = false;
    setOpen(false);
    emitQuote();
  }
})();
