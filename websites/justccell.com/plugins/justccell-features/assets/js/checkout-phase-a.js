/**
 * Checkout — shipping card chrome, AJAX skeleton, payment unblock.
 * justCCELL Features plugin (Rank Ray). Do not duplicate in the theme.
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

  /** Woo blocks `.woocommerce-checkout-payment` and only unblocks fragment keys it replaced. */
  const unblockCheckout = () => {
    if (typeof $.fn.unblock !== "function") {
      return;
    }
    $(".woocommerce-checkout-payment, .woocommerce-checkout-review-order-table, form.checkout").unblock();
    setShippingLoading(false);
  };

  /** Keep payment block last in the checkout form when the stack wrapper exists. */
  const pinPaymentStack = () => {
    const form = document.querySelector("form.checkout.woocommerce-checkout");
    const stack = document.querySelector("#jc-checkout-payment-stack");
    if (!(form instanceof HTMLFormElement) || !(stack instanceof HTMLElement)) {
      return;
    }
    if (form.lastElementChild !== stack) {
      form.appendChild(stack);
    }
  };

  const init = () => {
    decorateShippingMethods();
    bindShippingSelection();
    pinPaymentStack();
    unblockCheckout();
  };

  $(document.body).on("updated_checkout init_checkout", init);
  $(document.body).on("update_checkout", () => setShippingLoading(true));
  $(document.body).on("updated_checkout", unblockCheckout);

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})(window.jQuery);
