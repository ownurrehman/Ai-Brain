/**
 * Checkout Phase B — shipping card chrome + AJAX loading skeleton.
 * Rank Ray — https://rankray.com
 */
(function ($) {
  "use strict";

  if (typeof $ === "undefined") {
    return;
  }

  const decorateShippingMethods = () => {
    document
      .querySelectorAll("#jc-checkout-shipping #shipping_method, #shipping_method, .woocommerce-shipping-methods")
      .forEach((list) => {
        if (!(list instanceof HTMLElement)) {
          return;
        }
        list.classList.add("jc-shipping-rate-cards");
        list.querySelectorAll("li").forEach((li) => {
          if (!(li instanceof HTMLElement)) {
            return;
          }
          li.classList.add("jc-shipping-rate-card");
          const input = li.querySelector('input[type="radio"], input[type="hidden"]');
          if (input instanceof HTMLInputElement && input.checked) {
            li.classList.add("is-selected");
          } else {
            li.classList.remove("is-selected");
          }
        });
      });
  };

  const bindShippingSelection = () => {
    document.querySelectorAll("#jc-checkout-shipping input[type='radio'], #shipping_method input[type='radio']").forEach((input) => {
      if (!(input instanceof HTMLInputElement) || input.dataset.jcShippingBound === "1") {
        return;
      }
      input.dataset.jcShippingBound = "1";
      input.addEventListener("change", decorateShippingMethods);
    });
  };

  const setShippingLoading = (loading) => {
    document.body.classList.toggle("jc-checkout-shipping-loading", loading);
    const wrap = document.querySelector("#jc-checkout-shipping .jc-checkout-shipping-skeleton");
    if (wrap instanceof HTMLElement) {
      wrap.hidden = !loading;
    }
  };

  const init = () => {
    decorateShippingMethods();
    bindShippingSelection();
  };

  $(document.body).on("updated_checkout init_checkout", init);
  $(document.body).on("update_checkout", () => setShippingLoading(true));
  $(document.body).on("updated_checkout", () => setShippingLoading(false));

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})(window.jQuery);
