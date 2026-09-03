/**
 * Visual safe-zone mapper for group_jc_laser_engraving (product admin).
 *
 * Uses pointer events (not jQuery UI) for drag + resize so WP admin CSS/JS
 * cannot break handles. Coordinates are Fabric canvas px (640×640), with the
 * plate contain-fitted from the top-left — same as laser-engraving.js.
 *
 * Rank Ray — https://rankray.com
 */
(function ($) {
  "use strict";

  const cfg = window.JustccellLaserZoneAdmin || {};
  const CANVAS_W = Number(cfg.canvasWidth) || 640;
  const CANVAS_H = Number(cfg.canvasHeight) || 640;
  const keys = cfg.fieldKeys || {};
  const names = cfg.fieldNames || {};
  const i18n = cfg.i18n || {};
  const STAGE_MAX = 520;
  const HANDLE_DIRS = ["nw", "n", "ne", "e", "se", "s", "sw", "w"];

  let $mapper = null;
  let $stage = null;
  let $plate = null;
  let $box = null;
  let $readout = null;
  let nativeW = 0;
  let nativeH = 0;
  let writingFields = false;
  let activeRowIndex = 0;
  let bootBound = false;
  let refreshSeq = 0;
  let interaction = null;

  const fieldSelector = (key, name) => {
    const parts = [];
    if (key) {
      parts.push(`.acf-field[data-key="${key}"]`);
    }
    if (name) {
      parts.push(`.acf-field[data-name="${name}"]`);
    }
    return parts.join(", ");
  };

  const $canvasField = () =>
    $(fieldSelector(keys.canvasBg, names.canvasBg)).first();

  const $zonesField = () =>
    $(fieldSelector(keys.zones, names.zones)).first();

  const zoneRows = () => {
    const $field = $zonesField();
    if (!$field.length) {
      return $();
    }
    return $field.find(".acf-repeater .acf-row").filter(function () {
      return !$(this).hasClass("acf-clone");
    });
  };

  const ensureZoneRow = () => {
    const $rows = zoneRows();
    if ($rows.length) {
      return $rows;
    }
    const $btn = $zonesField()
      .find(".acf-actions [data-event='add-row'], a.acf-button[data-event='add-row']")
      .first();
    if ($btn.length) {
      $btn.trigger("click");
    }
    return zoneRows();
  };

  const $activeRow = () => {
    const $rows = ensureZoneRow();
    if (!$rows.length) {
      return $();
    }
    if (activeRowIndex >= $rows.length) {
      activeRowIndex = 0;
    }
    return $rows.eq(activeRowIndex);
  };

  const rowInputs = ($row) => {
    const find = (key, name) => {
      let $el = $();
      if (key) {
        $el = $row
          .find(
            `.acf-field[data-key="${key}"] input[type="number"], .acf-field[data-key="${key}"] input[type="text"]`
          )
          .first();
      }
      if (!$el.length && name) {
        $el = $row.find(`.acf-field[data-name="${name}"] input`).first();
      }
      return $el;
    };
    return {
      x: find(keys.zoneX, "x"),
      y: find(keys.zoneY, "y"),
      w: find(keys.zoneW, "width"),
      h: find(keys.zoneH, "height"),
    };
  };

  const readFields = () => {
    const inputs = rowInputs($activeRow());
    return {
      x: Math.max(0, Math.round(Number(inputs.x.val()) || 0)),
      y: Math.max(0, Math.round(Number(inputs.y.val()) || 0)),
      w: Math.max(1, Math.round(Number(inputs.w.val()) || Math.round(CANVAS_W * 0.4))),
      h: Math.max(1, Math.round(Number(inputs.h.val()) || Math.round(CANVAS_H * 0.25))),
    };
  };

  const writeFields = (coords) => {
    if (writingFields) {
      return;
    }
    writingFields = true;
    const inputs = rowInputs($activeRow());
    const setVal = ($input, value) => {
      if ($input.length) {
        $input.val(String(value)).trigger("change");
      }
    };
    setVal(inputs.x, coords.x);
    setVal(inputs.y, coords.y);
    setVal(inputs.w, coords.w);
    setVal(inputs.h, coords.h);
    writingFields = false;
    updateReadout(coords);
  };

  const updateReadout = (coords) => {
    if (!$readout || !$readout.length) {
      return;
    }
    const plate =
      nativeW && nativeH
        ? `${i18n.nativeHint || "Plate (native)"}: ${nativeW}×${nativeH}px`
        : "";
    $readout.html(
      `<strong>${i18n.readout || "Canvas coords"}:</strong> ` +
        `x ${coords.x}, y ${coords.y}, w ${coords.w}, h ${coords.h}` +
        (plate ? ` <span class="jc-laser-zone-mapper__native">${plate}</span>` : "")
    );
  };

  const fabricFit = () => {
    if (!nativeW || !nativeH) {
      return { scale: 1, drawW: CANVAS_W, drawH: CANVAS_H };
    }
    const scale = Math.min(CANVAS_W / nativeW, CANVAS_H / nativeH);
    return { scale, drawW: nativeW * scale, drawH: nativeH * scale };
  };

  const stageMetrics = () => {
    if (!$stage || !$stage.length) {
      return { stageW: STAGE_MAX, stageH: STAGE_MAX, pxPerCanvas: STAGE_MAX / CANVAS_W };
    }
    const el = $stage[0];
    const stageW = el.clientWidth || STAGE_MAX;
    const stageH = el.clientHeight || STAGE_MAX;
    return {
      stageW,
      stageH,
      pxPerCanvas: stageW / CANVAS_W,
    };
  };

  const clampCoords = (c) => {
    const w = Math.max(1, Math.min(CANVAS_W, Math.round(c.w)));
    const h = Math.max(1, Math.min(CANVAS_H, Math.round(c.h)));
    const x = Math.max(0, Math.min(CANVAS_W - w, Math.round(c.x)));
    const y = Math.max(0, Math.min(CANVAS_H - h, Math.round(c.y)));
    return { x, y, w, h };
  };

  /**
   * Apply canvas coords to the box using explicit left/top/width/height in CSS px.
   * Width/height are border-box visual size (matches getBoundingClientRect).
   */
  const applyBoxFromCoords = (coords) => {
    if (!$box || !$box.length) {
      return;
    }
    const c = clampCoords(coords);
    const { pxPerCanvas } = stageMetrics();
    $box.css({
      left: `${c.x * pxPerCanvas}px`,
      top: `${c.y * pxPerCanvas}px`,
      width: `${c.w * pxPerCanvas}px`,
      height: `${c.h * pxPerCanvas}px`,
    });
    updateReadout(c);
    return c;
  };

  /**
   * Read box from style left/top/width/height (not jQuery .width(), which
   * excludes borders under border-box and caused shrink-on-drag).
   */
  const coordsFromBox = () => {
    if (!$box || !$box.length) {
      return readFields();
    }
    const { pxPerCanvas } = stageMetrics();
    const el = $box[0];
    const left = parseFloat(el.style.left) || 0;
    const top = parseFloat(el.style.top) || 0;
    const width = parseFloat(el.style.width) || el.offsetWidth;
    const height = parseFloat(el.style.height) || el.offsetHeight;
    return clampCoords({
      x: left / pxPerCanvas,
      y: top / pxPerCanvas,
      w: width / pxPerCanvas,
      h: height / pxPerCanvas,
    });
  };

  const ensureHandles = () => {
    if (!$box || !$box.length) {
      return;
    }
    // Strip any leftover jQuery UI handles from earlier versions.
    $box.find(".ui-resizable-handle").remove();
    $box.removeClass("ui-resizable ui-draggable ui-resizable-resizing ui-draggable-dragging");

    HANDLE_DIRS.forEach((dir) => {
      if (!$box.find(`[data-laser-zone-handle="${dir}"]`).length) {
        $box.append(
          `<span class="jc-laser-zone-mapper__handle jc-laser-zone-mapper__handle--${dir}" data-laser-zone-handle="${dir}"></span>`
        );
      }
    });
  };

  const endInteraction = (event) => {
    if (!interaction) {
      return;
    }
    if (event && interaction.pointerId != null && event.pointerId !== interaction.pointerId) {
      return;
    }
    const finalCoords = clampCoords(interaction.current);
    interaction = null;
    if ($box && $box.length) {
      $box.removeClass("is-interacting");
      try {
        if (event && event.target && event.target.releasePointerCapture) {
          event.target.releasePointerCapture(event.pointerId);
        }
      } catch (e) {
        /* ignore */
      }
    }
    applyBoxFromCoords(finalCoords);
    writeFields(finalCoords);
  };

  const onPointerMove = (event) => {
    if (!interaction) {
      return;
    }
    event.preventDefault();
    const { pxPerCanvas, stageW, stageH } = stageMetrics();
    const dx = (event.clientX - interaction.startX) / pxPerCanvas;
    const dy = (event.clientY - interaction.startY) / pxPerCanvas;
    const s = interaction.startCoords;
    let next = { x: s.x, y: s.y, w: s.w, h: s.h };
    const mode = interaction.mode;

    if (mode === "move") {
      next.x = s.x + dx;
      next.y = s.y + dy;
    } else {
      if (mode.includes("e")) {
        next.w = s.w + dx;
      }
      if (mode.includes("s")) {
        next.h = s.h + dy;
      }
      if (mode.includes("w")) {
        next.x = s.x + dx;
        next.w = s.w - dx;
      }
      if (mode.includes("n")) {
        next.y = s.y + dy;
        next.h = s.h - dy;
      }
      // Reject inverted sizes — pin min 8 canvas px.
      if (next.w < 8) {
        if (mode.includes("w")) {
          next.x = s.x + s.w - 8;
        }
        next.w = 8;
      }
      if (next.h < 8) {
        if (mode.includes("n")) {
          next.y = s.y + s.h - 8;
        }
        next.h = 8;
      }
    }

    next = clampCoords(next);
    // Keep inside stage visually as well (canvas already clamped to 640).
    void stageW;
    void stageH;
    interaction.current = next;
    applyBoxFromCoords(next);
  };

  const onPointerUp = (event) => {
    endInteraction(event);
    window.removeEventListener("pointermove", onPointerMove);
    window.removeEventListener("pointerup", onPointerUp);
    window.removeEventListener("pointercancel", onPointerUp);
  };

  const startInteraction = (mode, event) => {
    if (!$box || !$box.length || event.button !== 0) {
      return;
    }
    event.preventDefault();
    event.stopPropagation();
    const startCoords = coordsFromBox();
    interaction = {
      mode,
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      startCoords,
      current: startCoords,
    };
    $box.addClass("is-interacting");
    try {
      event.currentTarget.setPointerCapture(event.pointerId);
    } catch (e) {
      /* ignore */
    }
    window.addEventListener("pointermove", onPointerMove, { passive: false });
    window.addEventListener("pointerup", onPointerUp);
    window.addEventListener("pointercancel", onPointerUp);
  };

  const bindBoxInteraction = () => {
    if (!$box || !$box.length) {
      return;
    }
    ensureHandles();
    $box.off(".jcLaserZone");
    $box.on("pointerdown.jcLaserZone", function (event) {
      if ($(event.target).closest("[data-laser-zone-handle]").length) {
        return;
      }
      startInteraction("move", event.originalEvent || event);
    });
    $box.on("pointerdown.jcLaserZone", "[data-laser-zone-handle]", function (event) {
      const dir = $(this).attr("data-laser-zone-handle") || "se";
      startInteraction(dir, event.originalEvent || event);
    });
  };

  const layoutPlate = () => {
    if (!$plate || !$stage || !$plate.length) {
      return;
    }
    const fit = fabricFit();
    const { pxPerCanvas } = stageMetrics();
    $plate.css({
      width: `${fit.drawW * pxPerCanvas}px`,
      height: `${fit.drawH * pxPerCanvas}px`,
      left: "0px",
      top: "0px",
    });
  };

  const buildShell = () => {
    const $imgField = $canvasField();
    if (!$imgField.length) {
      return;
    }
    if ($imgField.find(".jc-laser-zone-mapper").length) {
      $mapper = $imgField.find(".jc-laser-zone-mapper").first();
      $stage = $mapper.find("[data-laser-zone-stage]");
      $plate = $mapper.find("[data-laser-zone-plate]");
      $box = $mapper.find("[data-laser-zone-box]");
      $readout = $mapper.find("[data-laser-zone-readout]");
      ensureHandles();
      return;
    }

    const handlesHtml = HANDLE_DIRS.map(
      (dir) =>
        `<span class="jc-laser-zone-mapper__handle jc-laser-zone-mapper__handle--${dir}" data-laser-zone-handle="${dir}"></span>`
    ).join("");

    $mapper = $(`
      <div class="jc-laser-zone-mapper" data-laser-zone-mapper>
        <div class="jc-laser-zone-mapper__head">
          <strong class="jc-laser-zone-mapper__title">${i18n.title || "Safe zone mapper"}</strong>
          <p class="jc-laser-zone-mapper__hint">${i18n.hint || ""}</p>
        </div>
        <div class="jc-laser-zone-mapper__empty" data-laser-zone-empty>${i18n.noImage || ""}</div>
        <div class="jc-laser-zone-mapper__stage-wrap" data-laser-zone-stage-wrap hidden>
          <div class="jc-laser-zone-mapper__stage" data-laser-zone-stage>
            <img class="jc-laser-zone-mapper__plate" data-laser-zone-plate alt="" draggable="false" />
            <div class="jc-laser-zone-mapper__box" data-laser-zone-box title="Safe zone">${handlesHtml}</div>
          </div>
          <div class="jc-laser-zone-mapper__readout" data-laser-zone-readout></div>
        </div>
      </div>
    `);

    $imgField.find("> .acf-input").append($mapper);
    $stage = $mapper.find("[data-laser-zone-stage]");
    $plate = $mapper.find("[data-laser-zone-plate]");
    $box = $mapper.find("[data-laser-zone-box]");
    $readout = $mapper.find("[data-laser-zone-readout]");
    $zonesField().addClass("jc-laser-zone-fields--mapped");
  };

  const setEmpty = (empty) => {
    if (!$mapper || !$mapper.length) {
      return;
    }
    $mapper.find("[data-laser-zone-empty]").prop("hidden", !empty);
    $mapper.find("[data-laser-zone-stage-wrap]").prop("hidden", empty);
  };

  const attachmentId = () => {
    const $field = $canvasField();
    const $input = $field.find(".acf-image-uploader input[type='hidden']").first();
    return Number($input.val()) || 0;
  };

  const loadAttachment = (id) =>
    new Promise((resolve, reject) => {
      if (!id || typeof wp === "undefined" || !wp.media || !wp.media.attachment) {
        reject(new Error("no media"));
        return;
      }
      const att = wp.media.attachment(id);
      att
        .fetch()
        .done(() => {
          resolve({
            url: att.get("url"),
            width: Number(att.get("width")) || 0,
            height: Number(att.get("height")) || 0,
          });
        })
        .fail(reject);
    });

  const mountInteractive = (coords) => {
    layoutPlate();
    ensureZoneRow();
    applyBoxFromCoords(coords);
    bindBoxInteraction();
  };

  const refreshFromImage = () => {
    buildShell();
    const id = attachmentId();
    const seq = ++refreshSeq;

    if (!id) {
      setEmpty(true);
      nativeW = 0;
      nativeH = 0;
      return;
    }

    loadAttachment(id)
      .then((meta) => {
        if (seq !== refreshSeq) {
          return;
        }
        nativeW = meta.width;
        nativeH = meta.height;
        setEmpty(false);

        const stageCss = Math.min(
          STAGE_MAX,
          Math.max(280, Math.min(window.innerWidth - 120, STAGE_MAX))
        );
        $stage.css({
          width: `${stageCss}px`,
          height: `${stageCss * (CANVAS_H / CANVAS_W)}px`,
        });

        const finish = () => {
          if (seq !== refreshSeq) {
            return;
          }
          let coords = readFields();
          const inputs = rowInputs($activeRow());
          const empty =
            !Number(inputs.x.val()) &&
            !Number(inputs.y.val()) &&
            (!Number(inputs.w.val()) || !Number(inputs.h.val()));
          if (empty) {
            const fit = fabricFit();
            coords = clampCoords({
              x: Math.round(fit.drawW * 0.15),
              y: Math.round(fit.drawH * 0.2),
              w: Math.round(fit.drawW * 0.5),
              h: Math.round(fit.drawH * 0.35),
            });
            writeFields(coords);
          }
          mountInteractive(coords);
        };

        $plate.off("load.jcLaserZone").one("load.jcLaserZone", finish);
        if ($plate.attr("src") === meta.url && $plate[0].complete && $plate[0].naturalWidth) {
          finish();
        } else {
          $plate.attr("src", meta.url);
        }
      })
      .catch(() => {
        if (seq !== refreshSeq) {
          return;
        }
        const $preview = $canvasField().find(".acf-image-uploader img").first();
        const src = $preview.attr("src");
        if (!src) {
          setEmpty(true);
          return;
        }
        setEmpty(false);
        const stageCss = Math.min(STAGE_MAX, 480);
        $stage.css({ width: `${stageCss}px`, height: `${stageCss}px` });
        $plate.off("load.jcLaserZone").one("load.jcLaserZone", function () {
          if (seq !== refreshSeq) {
            return;
          }
          nativeW = this.naturalWidth || CANVAS_W;
          nativeH = this.naturalHeight || CANVAS_H;
          mountInteractive(readFields());
        });
        $plate.attr("src", src);
      });
  };

  const bindFieldWatchers = () => {
    if (bootBound) {
      return;
    }
    bootBound = true;

    $(document).on(
      "change",
      fieldSelector(keys.zones, names.zones) + " input",
      function () {
        if (writingFields || interaction) {
          return;
        }
        applyBoxFromCoords(readFields());
      }
    );

    $(window).on("resize.jcLaserZone", () => {
      if (!$mapper || !$mapper.length || $mapper.find("[data-laser-zone-stage-wrap]").prop("hidden")) {
        return;
      }
      layoutPlate();
      applyBoxFromCoords(readFields());
    });
  };

  const boot = () => {
    if (!$canvasField().length) {
      return;
    }
    buildShell();
    bindFieldWatchers();
    refreshFromImage();
  };

  if (typeof acf !== "undefined") {
    const isCanvasBgField = (field) => {
      if (!field || typeof field.get !== "function") {
        return false;
      }
      return (
        field.get("key") === (keys.canvasBg || "field_jc_laser_canvas_bg") ||
        field.get("name") === (names.canvasBg || "canvas_background_image")
      );
    };

    acf.addAction("ready", boot);
    acf.addAction("show_field/key=" + (keys.canvasBg || "field_jc_laser_canvas_bg"), refreshFromImage);
    acf.addAction("load_field/key=" + (keys.canvasBg || "field_jc_laser_canvas_bg"), refreshFromImage);
    acf.addAction("show_field/key=field_jc_laser_tab_canvas", () => {
      window.setTimeout(refreshFromImage, 120);
    });
    acf.addAction("change", function (field) {
      if (isCanvasBgField(field)) {
        window.setTimeout(refreshFromImage, 150);
      }
    });

    $(document).on(
      "change",
      ".acf-field[data-key='" +
        (keys.canvasBg || "field_jc_laser_canvas_bg") +
        "'] input[type='hidden'], .acf-field[data-name='canvas_background_image'] input[type='hidden']",
      function () {
        window.setTimeout(refreshFromImage, 100);
      }
    );
  } else {
    $(boot);
  }
})(jQuery);
