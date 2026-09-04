/**
 * Replace leftover Woo / Cart-block "basket" copy with Cart.
 * Rank Ray — https://rankray.com
 */
(() => {
  const rewrite = (value) =>
    value
      .replace(/\bBASKET\b/g, "CART")
      .replace(/\bBasket\b/g, "Cart")
      .replace(/\bbasket\b/g, "cart");

  const walk = (root) => {
    if (!(root instanceof Node)) {
      return;
    }
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    const nodes = [];
    while (walker.nextNode()) {
      nodes.push(walker.currentNode);
    }
    nodes.forEach((node) => {
      if (node.nodeValue && /basket/i.test(node.nodeValue)) {
        node.nodeValue = rewrite(node.nodeValue);
      }
    });
    if (document.title && /basket/i.test(document.title)) {
      document.title = rewrite(document.title);
    }
  };

  const run = () => walk(document.body);
  run();
  document.addEventListener("DOMContentLoaded", run);
  window.addEventListener("load", run);

  const target = document.querySelector(".woocommerce, .wp-block-woocommerce-cart, .wc-block-cart, .jc-shop");
  if (target && "MutationObserver" in window) {
    const observer = new MutationObserver(run);
    observer.observe(target, { childList: true, subtree: true, characterData: true });
  }

  document.addEventListener("click", (event) => {
    const btn = event.target instanceof Element ? event.target.closest(".jc-qty-btn") : null;
    if (!btn) {
      return;
    }
    const wrap = btn.closest(".quantity");
    const input = wrap ? wrap.querySelector("input.qty") : null;
    if (!(input instanceof HTMLInputElement)) {
      return;
    }
    event.preventDefault();
    const step = Number.parseFloat(input.step || "1") || 1;
    const min = input.min !== "" ? Number.parseFloat(input.min) : 1;
    const max = input.max !== "" ? Number.parseFloat(input.max) : Number.POSITIVE_INFINITY;
    let next = Number.parseFloat(input.value || "0") || 0;
    next = btn.classList.contains("jc-qty-btn--plus") ? next + step : next - step;
    next = Math.min(max, Math.max(min, next));
    input.value = String(next);
    input.dispatchEvent(new Event("change", { bubbles: true }));
    input.dispatchEvent(new Event("input", { bubbles: true }));
  });
})();
