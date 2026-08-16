(() => {
  const table = document.getElementById("bc-table");
  const form = document.getElementById("bc-filters");
  const cardsRoot = document.getElementById("bc-market-cards");
  if (!form) return;

  const tbody = table ? table.querySelector("tbody") : null;
  let tableRows = table ? Array.from(table.querySelectorAll("tbody tr.bc-row")) : [];
  let cardRows = cardsRoot ? Array.from(cardsRoot.querySelectorAll(".bc-market-card")) : [];
  const empty = document.getElementById("bc-empty");
  const countEl = document.querySelector("#bc-result-count strong");
  const nicheSel = document.getElementById("bc-niche");
  const langSel = document.getElementById("bc-language");
  const sortButtons = table ? Array.from(table.querySelectorAll(".bc-sort")) : [];

  let sortKey = "dr";
  let sortDir = "desc";
  let sortType = "number";
  let syncingUrl = false;

  const optionsEl = document.getElementById("bc-filter-options");
  if (optionsEl && nicheSel && langSel) {
    try {
      const opts = JSON.parse(optionsEl.textContent || "{}");
      (opts.niches || [])
        .sort((a, b) => a.localeCompare(b))
        .forEach((n) => {
          const o = document.createElement("option");
          o.value = n;
          o.textContent = n;
          nicheSel.appendChild(o);
        });
      (opts.languages || [])
        .sort((a, b) => a.localeCompare(b))
        .forEach((n) => {
          const o = document.createElement("option");
          o.value = n;
          o.textContent = n;
          langSel.appendChild(o);
        });
    } catch (_) {
      /* ignore */
    }
  }

  const readFilters = () => ({
    q: (document.getElementById("bc-q")?.value || "").trim().toLowerCase(),
    minDr: Number(document.getElementById("bc-min-dr")?.value || 0),
    maxDr:
      document.getElementById("bc-max-dr")?.value === ""
        ? 100
        : Number(document.getElementById("bc-max-dr")?.value || 100),
    minPrice: Number(document.getElementById("bc-min-price")?.value || 0),
    maxPrice:
      document.getElementById("bc-max-price")?.value === ""
        ? Number.POSITIVE_INFINITY
        : Number(document.getElementById("bc-max-price")?.value),
    minTraffic: Number(document.getElementById("bc-min-traffic")?.value || 0),
    niche: nicheSel?.value || "",
    language: langSel?.value || "",
    verifiedOnly: !!document.getElementById("bc-verified")?.checked,
  });

  const rowMatches = (row, f) => {
    const domain = row.dataset.domain || "";
    const name = row.dataset.name || "";
    const dr = Number(row.dataset.dr);
    const traffic = Number(row.dataset.traffic || 0);
    const price = Number(row.dataset.price || 0);
    const rowNiche = row.dataset.niche || "";
    const rowLangs = (row.dataset.languages || row.dataset.language || "")
      .toLowerCase()
      .split(",")
      .map((s) => s.trim())
      .filter(Boolean);
    const verified = row.dataset.verified === "1";

    if (f.q && !domain.includes(f.q) && !name.includes(f.q)) return false;
    if (dr >= 0 && dr < f.minDr) return false;
    if (dr >= 0 && dr > f.maxDr) return false;
    if (price < f.minPrice) return false;
    if (price > f.maxPrice) return false;
    if (traffic < f.minTraffic) return false;
    if (f.niche && rowNiche !== f.niche) return false;
    if (f.language && !rowLangs.includes(f.language.toLowerCase())) return false;
    if (f.verifiedOnly && !verified) return false;
    return true;
  };

  const valueOf = (row, key, type) => {
    const raw = row.dataset[key];
    if (type === "number") {
      const n = Number(raw);
      if (raw === undefined || raw === "" || Number.isNaN(n) || n < 0) {
        return sortDir === "asc" ? Number.POSITIVE_INFINITY : Number.NEGATIVE_INFINITY;
      }
      return n;
    }
    return (raw || "").toString().toLowerCase();
  };

  const sortList = (list) => {
    list.sort((a, b) => {
      const af = a.dataset.featured === "1" ? 1 : 0;
      const bf = b.dataset.featured === "1" ? 1 : 0;
      if (af !== bf) return bf - af;
      const av = valueOf(a, sortKey, sortType);
      const bv = valueOf(b, sortKey, sortType);
      let cmp = 0;
      if (sortType === "number") cmp = av - bv;
      else cmp = String(av).localeCompare(String(bv));
      return sortDir === "asc" ? cmp : -cmp;
    });
  };

  const writeUrl = () => {
    if (syncingUrl) return;
    const f = readFilters();
    const params = new URLSearchParams();
    if (f.q) params.set("q", f.q);
    if (f.minDr > 0) params.set("min_dr", String(f.minDr));
    if (f.maxDr < 100) params.set("max_dr", String(f.maxDr));
    if (f.minPrice > 0) params.set("min_price", String(f.minPrice));
    if (Number.isFinite(f.maxPrice)) params.set("max_price", String(f.maxPrice));
    if (f.minTraffic > 0) params.set("min_traffic", String(f.minTraffic));
    if (f.niche) params.set("niche", f.niche);
    if (f.language) params.set("language", f.language);
    if (f.verifiedOnly) params.set("verified", "1");
    if (sortKey !== "dr" || sortDir !== "desc") {
      params.set("sort", sortKey);
      params.set("dir", sortDir);
    }
    const qs = params.toString();
    const next = qs ? `${window.location.pathname}?${qs}${window.location.hash}` : `${window.location.pathname}${window.location.hash}`;
    window.history.replaceState({}, "", next);
  };

  const loadUrl = () => {
    const params = new URLSearchParams(window.location.search);
    const setVal = (id, key) => {
      const el = document.getElementById(id);
      if (el && params.has(key)) el.value = params.get(key) || "";
    };
    setVal("bc-q", "q");
    setVal("bc-min-dr", "min_dr");
    setVal("bc-max-dr", "max_dr");
    setVal("bc-min-price", "min_price");
    setVal("bc-max-price", "max_price");
    setVal("bc-min-traffic", "min_traffic");
    if (nicheSel && params.has("niche")) nicheSel.value = params.get("niche") || "";
    if (langSel && params.has("language")) langSel.value = params.get("language") || "";
    const verified = document.getElementById("bc-verified");
    if (verified) verified.checked = params.get("verified") === "1";
    if (params.has("sort")) {
      sortKey = params.get("sort") || "dr";
      const btn = sortButtons.find((b) => b.dataset.sort === sortKey);
      sortType = btn?.dataset.type || (["dr", "da", "traffic", "price"].includes(sortKey) ? "number" : "string");
    }
    if (params.has("dir")) sortDir = params.get("dir") === "asc" ? "asc" : "desc";
  };

  const applyFilters = () => {
    const f = readFilters();
    let visible = 0;
    const applySet = (rows) => {
      rows.forEach((row) => {
        const ok = rowMatches(row, f);
        row.classList.toggle("is-hidden", !ok);
        if (ok) visible += 1;
      });
    };
    applySet(tableRows);
    // Cards share the same filter; count once from table if present, else cards.
    if (tableRows.length) {
      cardRows.forEach((row) => {
        row.classList.toggle("is-hidden", !rowMatches(row, f));
      });
      visible = tableRows.filter((r) => !r.classList.contains("is-hidden")).length;
    } else {
      applySet(cardRows);
      visible = cardRows.filter((r) => !r.classList.contains("is-hidden")).length;
    }

    if (countEl) countEl.textContent = String(visible);
    if (empty) empty.hidden = visible > 0;
    writeUrl();
    if (typeof window.__bcSyncBulkBar === "function") window.__bcSyncBulkBar();
  };

  const refresh = () => {
    if (tbody && tableRows.length) {
      sortList(tableRows);
      tableRows.forEach((row) => tbody.appendChild(row));
    }
    if (cardsRoot && cardRows.length) {
      sortList(cardRows);
      cardRows.forEach((row) => cardsRoot.appendChild(row));
    }
    sortButtons.forEach((btn) => {
      const active = btn.dataset.sort === sortKey;
      btn.classList.toggle("is-active", active);
      btn.classList.toggle("is-asc", active && sortDir === "asc");
      btn.classList.toggle("is-desc", active && sortDir === "desc");
      if (active) btn.setAttribute("aria-sort", sortDir === "asc" ? "ascending" : "descending");
      else btn.removeAttribute("aria-sort");
    });
    applyFilters();
  };

  sortButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const key = btn.dataset.sort;
      const type = btn.dataset.type || "string";
      if (sortKey === key) sortDir = sortDir === "asc" ? "desc" : "asc";
      else {
        sortKey = key;
        sortType = type;
        sortDir = type === "number" ? "desc" : "asc";
      }
      refresh();
    });
  });

  form.addEventListener("input", applyFilters);
  form.addEventListener("change", applyFilters);
  document.getElementById("bc-reset")?.addEventListener("click", () => {
    form.reset();
    sortKey = "dr";
    sortDir = "desc";
    sortType = "number";
    refresh();
  });

  syncingUrl = true;
  loadUrl();
  syncingUrl = false;
  refresh();

  // Multi-select → bulk add bar
  const bulkBar = document.getElementById("bc-bulk-bar");
  const selectedCountEl = document.querySelector("[data-bc-selected-count]");
  const selectAll = document.getElementById("bc-select-all");

  const visibleChecks = () => {
    const fromTable = tableRows
      .filter((r) => !r.classList.contains("is-hidden"))
      .map((r) => r.querySelector(".bc-row-check"))
      .filter(Boolean);
    if (fromTable.length) return fromTable;
    return cardRows
      .filter((r) => !r.classList.contains("is-hidden"))
      .map((r) => r.querySelector(".bc-row-check"))
      .filter(Boolean);
  };

  const syncBulkBar = () => {
    const checks = Array.from(document.querySelectorAll(".bc-row-check"));
    const selected = checks.filter((c) => c.checked);
    const n = new Set(selected.map((c) => c.value)).size;
    if (selectedCountEl) selectedCountEl.textContent = String(n);
    if (bulkBar) {
      bulkBar.hidden = n === 0;
      document.body.classList.toggle("bc-has-bulk", n > 0);
    }
    if (selectAll) {
      const vis = visibleChecks();
      const visChecked = vis.filter((c) => c.checked).length;
      selectAll.checked = vis.length > 0 && visChecked === vis.length;
      selectAll.indeterminate = visChecked > 0 && visChecked < vis.length;
    }
  };
  window.__bcSyncBulkBar = syncBulkBar;

  const setCheckedForId = (id, on) => {
    document.querySelectorAll(".bc-row-check").forEach((el) => {
      if (el.value === id) el.checked = on;
    });
  };

  document.addEventListener("change", (e) => {
    const t = e.target;
    if (!(t instanceof HTMLInputElement)) return;
    if (t.classList.contains("bc-row-check")) {
      setCheckedForId(t.value, t.checked);
      syncBulkBar();
    }
  });

  selectAll?.addEventListener("change", () => {
    const on = !!selectAll.checked;
    const seen = new Set();
    visibleChecks().forEach((c) => {
      if (seen.has(c.value)) return;
      seen.add(c.value);
      setCheckedForId(c.value, on);
    });
    syncBulkBar();
  });

  document.getElementById("bc-clear-selected")?.addEventListener("click", () => {
    document.querySelectorAll(".bc-row-check").forEach((c) => {
      c.checked = false;
    });
    if (selectAll) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    }
    syncBulkBar();
  });

  syncBulkBar();
})();
