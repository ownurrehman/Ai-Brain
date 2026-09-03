/**
 * Product edit — collapse empty 360° ACF gallery; keep filled strip compact.
 * Rank Ray — https://rankray.com
 */
(function () {
  "use strict";

  const SPIN_SELECTOR = '.acf-field[data-name="clone_spin"], .acf-field[data-key="field_jc_prod_spin"]';

  function forceAutoHeight(el) {
    if (!el) {
      return;
    }
    el.style.setProperty("height", "auto", "important");
    el.style.setProperty("min-height", "0", "important");
  }

  function collapseSpinGalleries(root) {
    const scope = root && root.querySelectorAll ? root : document;
    scope.querySelectorAll(SPIN_SELECTOR).forEach((field) => {
      const gallery = field.querySelector(".acf-gallery");
      if (!gallery) {
        return;
      }
      const hasItems = gallery.querySelector(".acf-gallery-attachment");
      const main = gallery.querySelector(".acf-gallery-main");
      const attachments = gallery.querySelector(".acf-gallery-attachments");
      const toolbar = gallery.querySelector(".acf-gallery-toolbar");

      forceAutoHeight(gallery);
      forceAutoHeight(main);
      forceAutoHeight(attachments);

      if (hasItems) {
        gallery.classList.remove("jc-spin-empty");
        if (attachments) {
          attachments.style.display = "";
        }
        return;
      }

      gallery.classList.add("jc-spin-empty");
      if (attachments) {
        attachments.style.display = "none";
        attachments.style.padding = "0";
        attachments.style.margin = "0";
        attachments.style.border = "0";
      }
      if (toolbar) {
        toolbar.style.borderTop = "0";
        toolbar.style.paddingTop = "0";
      }
    });
  }

  if (typeof acf !== "undefined" && acf.add_action) {
    acf.add_action("ready", collapseSpinGalleries);
    acf.add_action("append", collapseSpinGalleries);
    acf.add_action("remove", collapseSpinGalleries);
    acf.add_action("sortstop", collapseSpinGalleries);
  } else {
    document.addEventListener("DOMContentLoaded", () => collapseSpinGalleries(document));
  }

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }
    if (target.closest(".acf-gallery .acf-button, .acf-gallery .acf-icon, .acf-gallery-toolbar")) {
      window.setTimeout(() => collapseSpinGalleries(document), 60);
    }
  });

  if (typeof MutationObserver !== "undefined") {
    const watch = () => {
      document.querySelectorAll(SPIN_SELECTOR).forEach((field) => {
        if (field.dataset.jcSpinWatch === "1") {
          return;
        }
        field.dataset.jcSpinWatch = "1";
        const observer = new MutationObserver(() => collapseSpinGalleries(field));
        observer.observe(field, { childList: true, subtree: true });
      });
    };
    watch();
    document.addEventListener("DOMContentLoaded", watch);
  }
})();
