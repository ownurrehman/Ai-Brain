(function ($) {
  'use strict';

  var cfg = window.jcQuickStock || {};
  var activeProductId = 0;

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

  function setSaving(isSaving) {
    $save().prop('disabled', isSaving);
    $spinner().toggleClass('is-active', isSaving);
  }

  function openModal() {
    $modal().removeAttr('hidden').attr('aria-hidden', 'false');
    $('body').addClass('jc-quick-stock-modal-open');
  }

  function closeModal() {
    $modal().attr('hidden', 'hidden').attr('aria-hidden', 'true');
    $('body').removeClass('jc-quick-stock-modal-open');
    activeProductId = 0;
    $body().empty();
    $productName().text('');
    $save().prop('disabled', true);
    setSaving(false);
  }

  function renderTable(payload) {
    var variations = (payload && payload.variations) || [];
    activeProductId = payload && payload.product_id ? parseInt(payload.product_id, 10) : 0;
    $productName().text(payload && payload.product_name ? payload.product_name : '');

    if (!variations.length) {
      $body().html(
        '<p class="jc-quick-stock-modal__empty">' +
          (cfg.i18n && cfg.i18n.empty ? cfg.i18n.empty : 'No variations found.') +
          '</p>'
      );
      $save().prop('disabled', true);
      return;
    }

    var html =
      '<table class="widefat striped jc-quick-stock-table"><thead><tr>' +
      '<th scope="col">' +
      (cfg.i18n && cfg.i18n.attribute ? cfg.i18n.attribute : 'Variation') +
      '</th>' +
      '<th scope="col">' +
      (cfg.i18n && cfg.i18n.quantity ? cfg.i18n.quantity : 'Stock qty') +
      '</th>' +
      '</tr></thead><tbody>';

    variations.forEach(function (row) {
      var qty =
        row.stock_quantity !== null && row.stock_quantity !== undefined
          ? row.stock_quantity
          : '';
      var note = '';
      if (!row.manage_stock) {
        note =
          '<p class="description jc-quick-stock-manage-note">' +
          (cfg.i18n && cfg.i18n.manageOff
            ? cfg.i18n.manageOff
            : 'Stock was not managed — will enable when you save a quantity.') +
          '</p>';
      }

      html +=
        '<tr data-variation-id="' +
        row.variation_id +
        '">' +
        '<td><strong>' +
        $('<div>').text(row.label || '').html() +
        '</strong>' +
        note +
        '</td>' +
        '<td><input type="number" class="small-text jc-quick-stock-qty" min="0" step="1" inputmode="numeric" value="' +
        qty +
        '" data-manage-stock="' +
        (row.manage_stock ? '1' : '0') +
        '" /></td>' +
        '</tr>';
    });

    html += '</tbody></table>';
    $body().html(html);
    $save().prop('disabled', false);
  }

  function loadVariations(productId) {
    if (!$modal().length) {
      flashNotice(
        (cfg.i18n && cfg.i18n.error ? cfg.i18n.error : 'Quick Stock UI failed to load.') +
          ' Reload the page and try again.',
        'error'
      );
      return;
    }

    openModal();
    $body().html(
      '<p class="jc-quick-stock-modal__loading">' +
        (cfg.i18n && cfg.i18n.loading ? cfg.i18n.loading : 'Loading…') +
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
          throw new Error(
            (response && response.data && response.data.message) ||
              (cfg.i18n && cfg.i18n.error)
          );
        }
        renderTable(response.data);
      })
      .fail(function (xhr) {
        var message =
          (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) ||
          (cfg.i18n && cfg.i18n.error) ||
          'Error';
        $body().html('<p class="jc-quick-stock-modal__error">' + message + '</p>');
      });
  }

  function collectStockMap() {
    var stock = {};
    $body()
      .find('tr[data-variation-id]')
      .each(function () {
        var $row = $(this);
        var id = parseInt($row.attr('data-variation-id'), 10);
        var $input = $row.find('.jc-quick-stock-qty');
        if (!id || !$input.length) {
          return;
        }
        var val = parseInt($input.val(), 10);
        if (Number.isNaN(val) || val < 0) {
          val = 0;
        }
        stock[id] = val;
      });
    return stock;
  }

  function saveStock() {
    if (!activeProductId) {
      return;
    }

    setSaving(true);

    $.post(cfg.ajaxUrl, {
      action: 'jc_save_variation_stock',
      nonce: cfg.nonce,
      product_id: activeProductId,
      stock: collectStockMap(),
    })
      .done(function (response) {
        if (!response || !response.success) {
          throw new Error(
            (response && response.data && response.data.message) ||
              (cfg.i18n && cfg.i18n.error)
          );
        }

        closeModal();
        flashNotice(
          (response.data && response.data.message) ||
            (cfg.i18n && cfg.i18n.saved) ||
            'Saved.',
          'success'
        );
      })
      .fail(function (xhr) {
        flashNotice(
          (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) ||
            (cfg.i18n && cfg.i18n.error) ||
            'Error',
          'error'
        );
      })
      .always(function () {
        setSaving(false);
      });
  }

  $(document).on('click', '.jc-quick-stock-btn', function (event) {
    event.preventDefault();
    event.stopPropagation();
    // Use attr — jQuery .data() camelCases data-product-id to productId, so .data('product-id') is undefined.
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

  $(document).on('keydown', function (event) {
    if (event.key === 'Escape' && $modal().attr('aria-hidden') === 'false') {
      closeModal();
    }
  });
})(jQuery);
