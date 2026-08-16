(() => {
  const cfg = window.justccellMediaImport;
  if (!cfg || !cfg.ajax || !cfg.nonce) {
    return;
  }

  const status = () => document.getElementById("justccell-media-import-status");

  const paint = (imported, total, done) => {
    const el = status();
    if (!el) {
      return;
    }
    el.textContent = done
      ? `Done. ${imported} images are in Media → Library.`
      : `Importing into Media Library: ${imported} of ${total}. Keep this page open.`;
  };

  const run = async () => {
    let guard = 0;
    while (guard < 50) {
      guard += 1;
      const body = new FormData();
      body.append("action", "justccell_import_media");
      body.append("nonce", cfg.nonce);
      const res = await fetch(cfg.ajax, { method: "POST", body, credentials: "same-origin" });
      const payload = await res.json();
      if (!payload || !payload.success || !payload.data) {
        break;
      }
      const data = payload.data;
      paint(data.imported, data.total, data.done);
      if (data.done) {
        return;
      }
    }
  };

  run().catch(() => {
    const el = status();
    if (el) {
      el.textContent = "Media import paused. Refresh this page to continue.";
    }
  });
})();
