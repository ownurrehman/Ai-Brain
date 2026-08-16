(() => {
  const table = document.getElementById("bc-inv-table");
  const saveBtn = document.getElementById("bc-inv-save");
  const statusEl = document.getElementById("bc-inv-status");
  const searchEl = document.getElementById("bc-inv-search");
  const featuredOnly = document.getElementById("bc-inv-featured-only");
  if (!table || !saveBtn || typeof bcInventory === "undefined") return;

  const rows = () => Array.from(table.querySelectorAll("tbody tr.bc-inv__row"));

  const setStatus = (text, kind) => {
    if (!statusEl) return;
    statusEl.textContent = text || "";
    statusEl.classList.remove("is-ok", "is-err", "is-busy");
    if (kind) statusEl.classList.add(`is-${kind}`);
  };

  const readRow = (tr) => {
    const get = (field) => {
      const el = tr.querySelector(`[data-field="${field}"]`);
      if (!el) return "";
      if (el.type === "checkbox") return el.checked;
      return el.value;
    };
    return {
      id: Number(tr.dataset.id),
      domain: String(get("domain") || "").trim(),
      da: String(get("da") || "").trim(),
      dr: String(get("dr") || "").trim(),
      traffic: String(get("traffic") || "").trim(),
      niche: String(get("niche") || "").trim(),
      languages: String(get("languages") || "").trim(),
      price: String(get("price") || "").trim(),
      verified: !!get("verified"),
      dofollow: !!get("dofollow"),
      featured: !!get("featured"),
      status: String(get("status") || "publish"),
    };
  };

  const markDirty = (tr) => {
    tr.classList.add("is-dirty");
    const featured = tr.querySelector('[data-field="featured"]');
    tr.dataset.featured = featured && featured.checked ? "1" : "0";
    tr.classList.toggle("is-featured", !!(featured && featured.checked));
    const dirty = rows().filter((r) => r.classList.contains("is-dirty")).length;
    setStatus(dirty ? `${dirty} ${bcInventory.i18n.dirty}` : "", dirty ? "busy" : null);
  };

  const applyFilter = () => {
    const q = (searchEl?.value || "").trim().toLowerCase();
    const onlyFeatured = !!(featuredOnly && featuredOnly.checked);
    rows().forEach((tr) => {
      const domain = tr.dataset.domain || "";
      const domainLive = (tr.querySelector('[data-field="domain"]')?.value || "").toLowerCase();
      const featured = tr.dataset.featured === "1";
      let ok = true;
      if (q && !domain.includes(q) && !domainLive.includes(q)) ok = false;
      if (onlyFeatured && !featured) ok = false;
      tr.classList.toggle("is-hidden", !ok);
    });
  };

  const saveRows = async (list) => {
    if (!list.length) {
      setStatus(bcInventory.i18n.saved, "ok");
      return;
    }
    setStatus(bcInventory.i18n.saving, "busy");
    saveBtn.disabled = true;
    try {
      const body = new FormData();
      body.append("action", "bc_inventory_save");
      body.append("nonce", bcInventory.nonce);
      body.append("rows", JSON.stringify(list));
      const res = await fetch(bcInventory.ajaxUrl, { method: "POST", body, credentials: "same-origin" });
      const json = await res.json();
      if (!json || !json.success) {
        throw new Error((json && json.data && json.data.message) || "error");
      }
      list.forEach((item) => {
        const tr = table.querySelector(`tr[data-id="${item.id}"]`);
        if (!tr) return;
        tr.classList.remove("is-dirty");
        tr.dataset.domain = String(item.domain || "").toLowerCase();
        tr.dataset.featured = item.featured ? "1" : "0";
        tr.classList.toggle("is-featured", !!item.featured);
      });
      setStatus(`${bcInventory.i18n.saved} (${json.data.saved})`, "ok");
    } catch (_) {
      setStatus(bcInventory.i18n.error, "err");
    } finally {
      saveBtn.disabled = false;
    }
  };

  table.addEventListener("input", (e) => {
    const tr = e.target.closest("tr.bc-inv__row");
    if (tr) markDirty(tr);
    if (e.target.matches('[data-field="domain"]')) applyFilter();
  });

  table.addEventListener("change", (e) => {
    const tr = e.target.closest("tr.bc-inv__row");
    if (!tr) return;
    markDirty(tr);
    applyFilter();
  });

  // Auto-save a single dirty row when leaving it
  table.addEventListener(
    "focusout",
    (e) => {
      const tr = e.target.closest("tr.bc-inv__row");
      if (!tr || !tr.classList.contains("is-dirty")) return;
      const next = e.relatedTarget && e.relatedTarget.closest ? e.relatedTarget.closest("tr.bc-inv__row") : null;
      if (next === tr) return;
      window.setTimeout(() => {
        if (!tr.classList.contains("is-dirty")) return;
        if (tr.contains(document.activeElement)) return;
        saveRows([readRow(tr)]);
      }, 120);
    },
    true
  );

  saveBtn.addEventListener("click", () => {
    const dirty = rows().filter((r) => r.classList.contains("is-dirty"));
    const payload = (dirty.length ? dirty : rows()).map(readRow);
    saveRows(payload);
  });

  searchEl?.addEventListener("input", applyFilter);
  featuredOnly?.addEventListener("change", applyFilter);
})();
