(function ($) {
  'use strict';

  var cfg = window.jcQuickStock || {};
  var activeProductId = 0;
  var lastFocus = null;

  function i18n(key, fallback) {
    return (cfg.i18n && cfg.i18n[key]) || fallback;
  }

  function $modal() {
    return $('#jc-quick-stock-modal');
  }

  function $body() {
    return $('#jc-quick-stock-modal-body');
  }

  function $productName() {
    return $('#jc-quick-stock-product-name');
  }

  function $save() {
    return $('#jc-quick-stock-save');
  }

  function $spinner() {
    return $('#jc-quick-stock-spinner');
  }

  function $feedback() {
    return $('#jc-quick-stock-feedback');
  }

  function escapeHtml(str) {
    return $('<div>').text(str || '').html();
  }

  function labelFor(template, name) {
    var tpl = template || '%s';
    return tpl.replace('%s', name || '');
  }

  function formatMoney(value) {
    var n = parseFloat(value);
    if (Number.isNaN(n)) {
      return '';
    }
    return (cfg.currency || '£') + n.toFixed(2);
  }

  function focusables() {
    return $modal()
      .find('button:visible, input:visible, select:visible, a[href]:visible, [tabindex]:visible')
      .filter(':not([disabled]):not([hidden])');
  }

  function flashNotice(message, type) {
    var $wrap = $('.wrap h1').first();
    if (!$wrap.length) {
      return;
    }

    $('.jc-quick-stock-notice').remove();

    var $notice = $(
      '<div class="notice notice-' +
        (type || 'success') +
        ' is-dismissible jc-quick-stock-notice"><p></p></div>'
    );
    $notice.find('p').text(message);
    $wrap.after($notice);

    if (window.wp && wp.a11y && wp.a11y.speak) {
      wp.a11y.speak(message);
    }
  }

  function setFeedback(message) {
    $feedback().text(message || '');
  }

  function setSaving(isSaving) {
    $save().prop('disabled', isSaving);
    $spinner().toggleClass('is-active', isSaving);
    if (isSaving) {
      setFeedback(i18n('saving', 'Saving…'));
    }
  }

  function paintPreview($row) {
    var regular = parseFloat($row.find('.jc-quick-stock-regular').val());
    var sale = parseFloat($row.find('.jc-quick-stock-sale').val());
    var $preview = $row.find('.jc-quick-stock-preview');
    var $sale = $row.find('.jc-quick-stock-sale');
    var validSale =
      Number.isFinite(regular) &&
      Number.isFinite(sale) &&
      sale >= 0 &&
      regular > sale;

    $row.toggleClass('has-sale', validSale);
    $sale.removeAttr('aria-invalid');
    $row.find('.jc-quick-stock-row-error').remove();

    if (validSale) {
      $preview
        .html(
          '<del>' +
            escapeHtml(formatMoney(regular)) +
            '</del> <ins>' +
            escapeHtml(formatMoney(sale)) +
            '</ins>'
        )
        .removeAttr('hidden');
      return true;
    }

    $preview.attr('hidden', 'hidden').empty();

    if ($row.find('.jc-quick-stock-sale').val() !== '' && Number.isFinite(sale)) {
      $sale.attr('aria-invalid', 'true');
      $row.find('td').last().append(
        '<p class="jc-quick-stock-row-error">' +
          escapeHtml(i18n('saleInvalid', 'Sale price must be lower than regular price.')) +
          '</p>'
      );
      return false;
    }

    return true;
  }

  function validateTable() {
    var ok = true;
    $body()
      .find('tr[data-variation-id]')
      .each(function () {
        if (!paintPreview($(this))) {
          ok = false;
        }
      });
    return ok;
  }

  function openModal() {
    lastFocus = document.activeElement;
    $modal().removeAttr('hidden').attr('aria-hidden', 'false');
    $('body').addClass('jc-quick-stock-modal-open');
    setFeedback('');
  }

  function closeModal() {
    $modal().attr('hidden', 'hidden').attr('aria-hidden', 'true');
    $('body').removeClass('jc-quick-stock-modal-open');
    activeProductId = 0;
    $body().empty();
    $productName().text('');
    $save().prop('disabled', true);
    setSaving(false);
    setFeedback('');
    if (lastFocus && typeof lastFocus.focus === 'function') {
      lastFocus.focus();
    }
    lastFocus = null;
  }

  function renderTable(payload) {
    var variations = (payload && payload.variations) || [];
    activeProductId = payload && payload.product_id ? parseInt(payload.product_id, 10) : 0;
    $productName().text(payload && payload.product_name ? payload.product_name : '');

    if (!variations.length) {
      $body().html(
        '<p class="jc-quick-stock-modal__empty">' +
          escapeHtml(i18n('empty', 'No variations found.')) +
          '</p>'
      );
      $save().prop('disabled', true);
      return;
    }

    var html =
      '<div class="jc-quick-stock-table-wrap"><table class="widefat striped jc-quick-stock-table"><thead><tr>' +
      '<th scope="col">' +
      escapeHtml(i18n('attribute', 'Variation')) +
      '</th>' +
      '<th scope="col">' +
      escapeHtml(i18n('regular', 'Regular price')) +
      '</th>' +
      '<th scope="col">' +
      escapeHtml(i18n('sale', 'Sale price')) +
      '</th>' +
      '<th scope="col">' +
      escapeHtml(i18n('quantity', 'Stock qty')) +
      '</th>' +
      '</tr></thead><tbody>';

    variations.forEach(function (row) {
      var qty =
        row.stock_quantity !== null && row.stock_quantity !== undefined
          ? row.stock_quantity
          : '';
      var label = row.label || '';
      var id = parseInt(row.variation_id, 10) || 0;
      var note = '';
      if (!row.manage_stock) {
        note =
          '<p class="description jc-quick-stock-manage-note">' +
          escapeHtml(
            i18n(
              'manageOff',
              'Stock was not managed — will enable when you save a quantity.'
            )
          ) +
          '</p>';
      }

      html +=
        '<tr data-variation-id="' +
        id +
        '">' +
        '<td><strong>' +
        escapeHtml(label) +
        '</strong>' +
        note +
        '<p class="jc-quick-stock-preview" hidden></p></td>' +
        '<td><label class="screen-reader-text" for="jc-qs-regular-' +
        id +
        '">' +
        escapeHtml(labelFor(i18n('regularFor', 'Regular price for %s'), label)) +
        '</label><span class="jc-quick-stock-money"><span aria-hidden="true">' +
        escapeHtml(cfg.currency || '£') +
        '</span><input type="number" id="jc-qs-regular-' +
        id +
        '" class="jc-quick-stock-regular" min="0" step="0.01" inputmode="decimal" value="' +
        escapeHtml(row.regular_price || '') +
        '" /></span></td>' +
        '<td><label class="screen-reader-text" for="jc-qs-sale-' +
        id +
        '">' +
        escapeHtml(labelFor(i18n('saleFor', 'Sale price for %s'), label)) +
        '</label><span class="jc-quick-stock-money"><span aria-hidden="true">' +
        escapeHtml(cfg.currency || '£') +
        '</span><input type="number" id="jc-qs-sale-' +
        id +
        '" class="jc-quick-stock-sale" min="0" step="0.01" inputmode="decimal" value="' +
        escapeHtml(row.sale_price || '') +
        '" /></span></td>' +
        '<td><label class="screen-reader-text" for="jc-qs-qty-' +
        id +
        '">' +
        escapeHtml(labelFor(i18n('qtyFor', 'Stock quantity for %s'), label)) +
        '</label><input type="number" id="jc-qs-qty-' +
        id +
        '" class="small-text jc-quick-stock-qty" min="0" step="1" inputmode="numeric" value="' +
        qty +
        '" data-manage-stock="' +
        (row.manage_stock ? '1' : '0') +
        '" /></td>' +
        '</tr>';
    });

    html += '</tbody></table></div>';
    $body().html(html);
    $body().find('tr[data-variation-id]').each(function () {
      paintPreview($(this));
    });
    $save().prop('disabled', false);
    $body().find('.jc-quick-stock-regular').first().trigger('focus');
  }

  function loadVariations(productId) {
    if (!$modal().length) {
      flashNotice(
        i18n('error', 'Quick Stock UI failed to load.') + ' Reload the page and try again.',
        'error'
      );
      return;
    }

    openModal();
    $body().html(
      '<p class="jc-quick-stock-modal__loading">' +
        escapeHtml(i18n('loading', 'Loading…')) +
        '</p>'
    );
    $save().prop('disabled', true);

    $.post(cfg.ajaxUrl, {
      action: 'jc_get_variation_stock',
      nonce: cfg.nonce,
      product_id: productId,
    })
      .done(function (response) {
        if (!response || !response.success) {
          var message =
            (response && response.data && response.data.message) ||
            i18n('error', 'Error');
          $body().html(
            '<p class="jc-quick-stock-modal__error">' + escapeHtml(message) + '</p>'
          );
          return;
        }
        renderTable(response.data);
      })
      .fail(function (xhr) {
        var message =
          (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) ||
          i18n('error', 'Error');
        $body().html('<p class="jc-quick-stock-modal__error">' + escapeHtml(message) + '</p>');
      });
  }

  function ajaxMessage(xhr, response) {
    if (response && response.data && response.data.message) {
      return response.data.message;
    }
    if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
      return xhr.responseJSON.data.message;
    }
    return i18n('error', 'Could not update variations. Try again.');
  }

  function patchListCell(productId, data) {
    var $cell = $('.jc-stock-cell--variable[data-product-id="' + productId + '"]');
    if (!$cell.length) {
      return;
    }

    var summary = data && data.summary ? String(data.summary) : '';
    var $meta = $cell.find('.jc-stock-cell__meta');
    if (!$meta.length) {
      $meta = $('<span class="jc-stock-cell__meta"></span>');
      $cell.find('.jc-quick-stock-btn').before($meta);
    }

    if (summary) {
      $meta.text(summary).removeAttr('hidden');
    }
  }

  function collectRows() {
    var rows = {};
    $body()
      .find('tr[data-variation-id]')
      .each(function () {
        var $row = $(this);
        var id = parseInt($row.attr('data-variation-id'), 10);
        var $qty = $row.find('.jc-quick-stock-qty');
        if (!id || !$qty.length) {
          return;
        }
        var val = parseInt($qty.val(), 10);
        if (Number.isNaN(val) || val < 0) {
          val = 0;
        }
        rows[id] = {
          qty: val,
          regular: $row.find('.jc-quick-stock-regular').val() || '',
          sale: $row.find('.jc-quick-stock-sale').val() || '',
        };
      });
    return rows;
  }

  function saveStock() {
    if (!activeProductId) {
      return;
    }

    if (!validateTable()) {
      setFeedback(i18n('saleInvalid', 'Sale price must be lower than regular price.'));
      $body().find('[aria-invalid="true"]').first().trigger('focus');
      return;
    }

    setSaving(true);

    $.post(cfg.ajaxUrl, {
      action: 'jc_save_variation_stock',
      nonce: cfg.nonce,
      product_id: activeProductId,
      rows: collectRows(),
    })
      .done(function (response) {
        if (!response || !response.success) {
          var failMessage = ajaxMessage(null, response);
          setFeedback(failMessage);
          flashNotice(failMessage, 'error');
          return;
        }

        patchListCell(activeProductId, response.data);
        closeModal();
        flashNotice(
          (response.data && response.data.message) || i18n('saved', 'Saved.'),
          'success'
        );
      })
      .fail(function (xhr) {
        var message = ajaxMessage(xhr, null);
        setFeedback(message);
        flashNotice(message, 'error');
      })
      .always(function () {
        setSaving(false);
      });
  }

  $(document).on('click', '.jc-quick-stock-btn', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var productId = parseInt($(this).attr('data-product-id') || '0', 10);
    if (!productId) {
      return;
    }
    loadVariations(productId);
  });

  $(document).on('click', '[data-jc-quick-stock-close]', function (event) {
    event.preventDefault();
    closeModal();
  });

  $(document).on('click', '#jc-quick-stock-save', function (event) {
    event.preventDefault();
    saveStock();
  });

  $(document).on('input blur', '.jc-quick-stock-regular, .jc-quick-stock-sale', function () {
    paintPreview($(this).closest('tr'));
  });

  $(document).on('keydown', function (event) {
    if ($modal().attr('aria-hidden') !== 'false') {
      return;
    }

    if (event.key === 'Escape') {
      closeModal();
      return;
    }

    if (event.key !== 'Tab') {
      return;
    }

    var $items = focusables();
    if (!$items.length) {
      return;
    }

    var first = $items.get(0);
    var last = $items.get($items.length - 1);
    var current = document.activeElement;

    if (event.shiftKey && current === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && current === last) {
      event.preventDefault();
      first.focus();
    }
  });
})(jQuery);
