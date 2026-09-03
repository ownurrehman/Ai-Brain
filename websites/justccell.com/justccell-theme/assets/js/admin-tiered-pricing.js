/**
 * Product data → tiered pricing repeater.
 * Rank Ray — https://rankray.com
 */
(function ($) {
  "use strict";

  const tbody = document.getElementById("justccell-tier-rows");
  if (!(tbody instanceof HTMLElement)) {
    return;
  }

  const nextIndex = () => tbody.querySelectorAll(".justccell-tier-row").length;

  const bindRemove = (row) => {
    const btn = row.querySelector(".justccell-tier-remove");
    if (!(btn instanceof HTMLButtonElement)) {
      return;
    }
    btn.addEventListener("click", () => {
      const rows = tbody.querySelectorAll(".justccell-tier-row");
      if (rows.length <= 1) {
        row.querySelectorAll("input").forEach((input) => {
          if (input instanceof HTMLInputElement) {
            input.value = input.name.includes("[min]") ? "1" : "";
          }
        });
        return;
      }
      row.remove();
      reindex();
    });
  };

  const reindex = () => {
    tbody.querySelectorAll(".justccell-tier-row").forEach((row, index) => {
      row.querySelectorAll("input").forEach((input) => {
        if (!(input instanceof HTMLInputElement) || !input.name) {
          return;
        }
        input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
      });
    });
  };

  tbody.querySelectorAll(".justccell-tier-row").forEach(bindRemove);

  const addBtn = document.getElementById("justccell-tier-add");
  if (addBtn instanceof HTMLButtonElement) {
    addBtn.addEventListener("click", () => {
      const i = nextIndex();
      const tr = document.createElement("tr");
      tr.className = "justccell-tier-row";
      tr.innerHTML = `
        <td><input type="number" min="1" step="1" name="justccell_tier_min[${i}]" value="1" class="short" /></td>
        <td><input type="number" min="0" step="1" name="justccell_tier_max[${i}]" value="0" class="short" /></td>
        <td><input type="text" name="justccell_tier_price[${i}]" value="" class="short wc_input_price" placeholder="0.00" /></td>
        <td><button type="button" class="button-link-delete justccell-tier-remove" aria-label="Remove tier">&times;</button></td>
      `;
      tbody.appendChild(tr);
      bindRemove(tr);
    });
  }
})(jQuery);
