/**
 * Product edit — compact ACF layout + Woo description field order.
 * Rank Ray — https://rankray.com
 */
(function () {
  "use strict";

  const SPIN_SELECTOR =
    '.acf-field[data-name="clone_spin"], .acf-field[data-key="field_jc_prod_spin"]';
  const PRODUCT_BOX =
    '.acf-postbox[data-key="group_jc_product_clone"], #acf-group_jc_product_clone';

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

  function postboxFrom(el) {
    if (!el) {
      return null;
    }
    return el.closest(".postbox") || el;
  }

  function moveAfter(anchorSelector, target) {
    const anchor = document.querySelector(anchorSelector);
    if (!anchor || !target) {
      return false;
    }
    const anchorField = anchor.closest(".acf-field") || anchor;
    const box = postboxFrom(target);
    if (!box || !anchorField.parentNode) {
      return false;
    }
    anchorField.insertAdjacentElement("afterend", box);
    return true;
  }

  function reorderProductDescriptions() {
    const box = document.querySelector(PRODUCT_BOX);
    if (!box) {
      return;
    }

    const fieldsWrap =
      box.querySelector(".acf-fields") || box.querySelector(".inside");
    if (!fieldsWrap) {
      return;
    }

    const excerptBox = document.getElementById("postexcerpt");
    const editorBox = document.getElementById("postdivrich");

    if (excerptBox) {
      moveAfter('[data-key="field_jc_prod_subtitle"]', excerptBox);
    }

    if (editorBox) {
      moveAfter('[data-key="field_jc_prod_detail_3"]', editorBox);
    }

    const wooShort = document.querySelector(
      "#general_product_data .woocommerce-product-details__short-description, #general_product_data textarea#excerpt"
    );
    if (wooShort) {
      const tagline = document.querySelector('[data-key="field_jc_prod_subtitle"]');
      const specsHeading = document.querySelector(
        '[data-key="field_jc_prod_specs_heading"]'
      );
      if (tagline && specsHeading && tagline.parentNode === specsHeading.parentNode) {
        specsHeading.insertAdjacentElement("beforebegin", wooShort.closest("p, .form-field, .options_group") || wooShort);
      }
    }
  }

  function initProductEditorLayout() {
    collapseSpinGalleries(document);
    reorderProductDescriptions();
  }

  if (typeof acf !== "undefined" && acf.add_action) {
    acf.add_action("ready", initProductEditorLayout);
    acf.add_action("append", collapseSpinGalleries);
    acf.add_action("remove", collapseSpinGalleries);
    acf.add_action("sortstop", collapseSpinGalleries);
  } else {
    document.addEventListener("DOMContentLoaded", initProductEditorLayout);
  }

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) {
      return;
    }
    if (
      target.closest(
        ".acf-gallery .acf-button, .acf-gallery .acf-icon, .acf-gallery-toolbar"
      )
    ) {
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
