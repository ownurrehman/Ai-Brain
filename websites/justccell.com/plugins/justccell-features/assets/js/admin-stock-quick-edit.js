(function () {
  'use strict';

  var cfg = window.justccellStockQuickEdit || {};
  var toolbar = document.getElementById('jc-stock-toolbar');
  var saveBtn = document.getElementById('jc-stock-save');
  var resetBtn = document.getElementById('jc-stock-reset');
  var pendingEl = document.getElementById('jc-stock-pending');
  var feedbackEl = document.getElementById('jc-stock-feedback');

  if (!toolbar || !saveBtn || !resetBtn) {
    return;
  }

  function cells() {
    return Array.prototype.slice.call(document.querySelectorAll('.jc-stock-cell:not(.jc-stock-cell--variable)'));
  }

  function rowPayload(cell) {
    var qtyInput = cell.querySelector('.jc-stock-qty');
    var statusSelect = cell.querySelector('.jc-stock-status');
    if (!qtyInput || !statusSelect) {
      return null;
    }

    return {
      product_id: parseInt(cell.getAttribute('data-product-id') || '0', 10),
      quantity: parseInt(qtyInput.value || '0', 10),
      status: statusSelect.value,
    };
  }

  function isDirty(cell) {
    var qtyInput = cell.querySelector('.jc-stock-qty');
    var statusSelect = cell.querySelector('.jc-stock-status');
    if (!qtyInput || !statusSelect) {
      return false;
    }

    var originalQty = cell.getAttribute('data-original-qty') || '0';
    var originalStatus = cell.getAttribute('data-original-status') || 'instock';

    return qtyInput.value !== originalQty || statusSelect.value !== originalStatus;
  }

  function syncQtyStatus(cell) {
    var qtyInput = cell.querySelector('.jc-stock-qty');
    var statusSelect = cell.querySelector('.jc-stock-status');
    if (!qtyInput || !statusSelect) {
      return;
    }

    var qty = parseInt(qtyInput.value || '0', 10);
    if (Number.isNaN(qty) || qty < 0) {
      qty = 0;
      qtyInput.value = '0';
    }

    if (qty === 0 && statusSelect.value === 'instock') {
      statusSelect.value = 'outofstock';
    } else if (qty > 0 && statusSelect.value === 'outofstock') {
      statusSelect.value = 'instock';
    }
  }

  function refreshUi() {
    var dirtyCount = 0;

    cells().forEach(function (cell) {
      syncQtyStatus(cell);
      var dirty = isDirty(cell);
      cell.classList.toggle('is-dirty', dirty);
      if (dirty) {
        dirtyCount += 1;
      }
    });

    var hasDirty = dirtyCount > 0;
    saveBtn.disabled = !hasDirty;
    resetBtn.disabled = !hasDirty;
    if (pendingEl) {
      pendingEl.hidden = !hasDirty;
    }
  }

  function setFeedback(kind, message) {
    if (!feedbackEl) {
      return;
    }
    feedbackEl.textContent = message || '';
    feedbackEl.classList.remove('is-success', 'is-error');
    if (kind) {
      feedbackEl.classList.add(kind);
    }
  }

  function resetAll() {
    cells().forEach(function (cell) {
      var qtyInput = cell.querySelector('.jc-stock-qty');
      var statusSelect = cell.querySelector('.jc-stock-status');
      if (!qtyInput || !statusSelect) {
        return;
      }
      qtyInput.value = cell.getAttribute('data-original-qty') || '0';
      statusSelect.value = cell.getAttribute('data-original-status') || 'instock';
    });
    setFeedback('', '');
    refreshUi();
  }

  function collectDirtyRows() {
    return cells()
      .filter(isDirty)
      .map(rowPayload)
      .filter(function (row) {
        return row && row.product_id > 0;
      });
  }

  function saveAll() {
    var rows = collectDirtyRows();
    if (!rows.length) {
      setFeedback('is-error', cfg.i18n && cfg.i18n.none ? cfg.i18n.none : 'No changes to save.');
      refreshUi();
      return;
    }

    saveBtn.disabled = true;
    resetBtn.disabled = true;
    setFeedback('', cfg.i18n && cfg.i18n.saving ? cfg.i18n.saving : 'Saving…');

    var body = new URLSearchParams();
    body.append('action', 'justccell_save_bulk_stock');
    body.append('nonce', cfg.nonce || '');
    body.append('rows', JSON.stringify(rows));

    fetch(cfg.ajaxUrl || '', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: body.toString(),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (payload) {
        if (!payload || !payload.success) {
          throw new Error((payload && payload.data && payload.data.message) || (cfg.i18n && cfg.i18n.error));
        }

        cells().forEach(function (cell) {
          if (!isDirty(cell)) {
            return;
          }
          var qtyInput = cell.querySelector('.jc-stock-qty');
          var statusSelect = cell.querySelector('.jc-stock-status');
          if (!qtyInput || !statusSelect) {
            return;
          }
          cell.setAttribute('data-original-qty', qtyInput.value);
          cell.setAttribute('data-original-status', statusSelect.value);
        });

        setFeedback('is-success', payload.data && payload.data.message ? payload.data.message : (cfg.i18n && cfg.i18n.saved));
        refreshUi();
      })
      .catch(function (err) {
        setFeedback('is-error', err && err.message ? err.message : (cfg.i18n && cfg.i18n.error));
        refreshUi();
      });
  }

  cells().forEach(function (cell) {
    var qtyInput = cell.querySelector('.jc-stock-qty');
    var statusSelect = cell.querySelector('.jc-stock-status');
    if (qtyInput) {
      qtyInput.addEventListener('input', refreshUi);
      qtyInput.addEventListener('change', refreshUi);
    }
    if (statusSelect) {
      statusSelect.addEventListener('change', refreshUi);
    }
  });

  saveBtn.addEventListener('click', saveAll);
  resetBtn.addEventListener('click', resetAll);
  refreshUi();
})();
