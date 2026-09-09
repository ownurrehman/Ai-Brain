/**
 * Variation panel volume-tier repeater (WooCommerce AJAX-loaded rows).
 * Rank Ray — https://rankray.com
 */
(function ($) {
  "use strict";

  const msg = () =>
    (window.justccellVarTiers && window.justccellVarTiers.incomplete) ||
    "Volume tiers: each row you keep needs a starting quantity and a price per unit. Max quantity may be blank or 0 for unlimited. Remove unused rows, then save again.";

  const rowHtml = (variationId, index, removeLabel) => `
        <td><input type="number" min="1" step="1" name="justccell_var_tier_min[${variationId}][${index}]" value="" class="short justccell-var-tier-min" /></td>
        <td><input type="number" min="0" step="1" name="justccell_var_tier_max[${variationId}][${index}]" value="" class="short justccell-var-tier-max" placeholder="0 = unlimited" /></td>
        <td><input type="text" name="justccell_var_tier_price[${variationId}][${index}]" value="" class="short wc_input_price justccell-var-tier-price" placeholder="0.00" /></td>
        <td><button type="button" class="button-link-delete justccell-var-tier-remove" aria-label="${removeLabel}">&times;</button></td>
      `;

  const wrapOf = (el) => el.closest(".justccell-var-tiers");

  const variationIdOf = (wrap) => {
    if (wrap instanceof HTMLElement && wrap.dataset.variationId) {
      return wrap.dataset.variationId;
    }
    return "0";
  };

  const enableTierInputs = () => {
    document.querySelectorAll(".justccell-var-tiers input").forEach((input) => {
      if (input instanceof HTMLInputElement) {
        input.disabled = false;
      }
    });
  };

  const reindex = (wrap) => {
    if (!(wrap instanceof HTMLElement)) {
      return;
    }
    const vid = variationIdOf(wrap);
    wrap.querySelectorAll(".justccell-var-tier-row").forEach((row, index) => {
      row.querySelectorAll("input").forEach((input) => {
        if (!(input instanceof HTMLInputElement) || !input.name) {
          return;
        }
        input.name = input.name.replace(/\[\d+\]\[\d+\]/, `[${vid}][${index}]`);
      });
    });
  };

  const rowIsEmpty = (row) => {
    const min = row.querySelector(".justccell-var-tier-min");
    const price = row.querySelector(".justccell-var-tier-price");
    const minVal = min instanceof HTMLInputElement ? min.value.trim() : "";
    const priceVal = price instanceof HTMLInputElement ? price.value.trim() : "";
    return minVal === "" && priceVal === "";
  };

  const rowIsComplete = (row) => {
    const min = row.querySelector(".justccell-var-tier-min");
    const price = row.querySelector(".justccell-var-tier-price");
    const minVal = min instanceof HTMLInputElement ? min.value.trim() : "";
    const priceVal = price instanceof HTMLInputElement ? price.value.trim() : "";
    const minNum = Number(minVal);
    const priceNum = Number(priceVal.replace(/,/g, ""));
    return Number.isInteger(minNum) && minNum >= 1 && Number.isFinite(priceNum) && priceNum > 0;
  };

  const validateWrap = (wrap) => {
    if (!(wrap instanceof HTMLElement)) {
      return true;
    }
    let ok = true;
    wrap.querySelectorAll(".justccell-var-tier-row").forEach((row) => {
      row.classList.remove("is-invalid");
      if (rowIsEmpty(row) || rowIsComplete(row)) {
        return;
      }
      row.classList.add("is-invalid");
      ok = false;
    });
    const err = wrap.querySelector(".justccell-var-tiers__error");
    if (err instanceof HTMLElement) {
      err.hidden = ok;
      err.textContent = ok ? "" : msg();
    }
    return ok;
  };

  const validateAll = () => {
    let ok = true;
    document.querySelectorAll(".justccell-var-tiers").forEach((wrap) => {
      if (!validateWrap(wrap)) {
        ok = false;
      }
    });
    if (!ok) {
      window.alert(msg());
    }
    return ok;
  };

  $(document).on("input", ".justccell-var-tier-min, .justccell-var-tier-max, .justccell-var-tier-price", function () {
    validateWrap(wrapOf(this));
  });

  $(document).on("click", ".justccell-var-tier-add", function (event) {
    event.preventDefault();
    const wrap = wrapOf(this);
    if (!(wrap instanceof HTMLElement)) {
      return;
    }
    const tbody = wrap.querySelector(".justccell-var-tier-rows");
    if (!(tbody instanceof HTMLElement)) {
      return;
    }
    const vid = variationIdOf(wrap);
    const index = tbody.querySelectorAll(".justccell-var-tier-row").length;
    const removeLabel = wrap.dataset.removeLabel || "Remove tier";
    const tr = document.createElement("tr");
    tr.className = "justccell-var-tier-row";
    tr.innerHTML = rowHtml(vid, index, removeLabel);
    tbody.appendChild(tr);
  });

  $(document).on("click", ".justccell-var-tier-remove", function (event) {
    event.preventDefault();
    const wrap = wrapOf(this);
    const row = this.closest(".justccell-var-tier-row");
    if (!(wrap instanceof HTMLElement) || !(row instanceof HTMLElement)) {
      return;
    }
    const rows = wrap.querySelectorAll(".justccell-var-tier-row");
    if (rows.length <= 1) {
      row.querySelectorAll("input").forEach((input) => {
        if (input instanceof HTMLInputElement) {
          input.value = "";
        }
      });
      row.classList.remove("is-invalid");
      validateWrap(wrap);
      return;
    }
    row.remove();
    reindex(wrap);
    validateWrap(wrap);
  });

  document.addEventListener(
    "click",
    (event) => {
      const target = event.target;
      if (!(target instanceof Element)) {
        return;
      }
      const btn = target.closest(".save-variation-changes");
      if (!(btn instanceof HTMLElement)) {
        return;
      }
      enableTierInputs();
      if (!validateAll()) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
      }
    },
    true
  );

  $(document.body).on("woocommerce_variations_save_variations_button", enableTierInputs);

  const productForm = document.getElementById("post");
  if (productForm instanceof HTMLFormElement) {
    productForm.addEventListener("submit", (event) => {
      enableTierInputs();
      if (!validateAll()) {
        event.preventDefault();
      }
    });
  }
})(jQuery);
