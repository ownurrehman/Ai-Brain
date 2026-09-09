/**
 * Product data → tiered pricing repeater (simple products).
 * Rank Ray — https://rankray.com
 */
(function () {
  "use strict";

  const tbody = document.getElementById("justccell-tier-rows");
  if (!(tbody instanceof HTMLElement)) {
    return;
  }

  const msg = () =>
    (window.justccellVarTiers && window.justccellVarTiers.incomplete) ||
    "Volume tiers: each row you keep needs a starting quantity and a price per unit. Max quantity may be blank or 0 for unlimited. Remove unused rows, then save again.";

  const nextIndex = () => tbody.querySelectorAll(".justccell-tier-row").length;

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

  const rowIsEmpty = (row) => {
    const min = row.querySelector(".justccell-tier-min");
    const price = row.querySelector(".justccell-tier-price");
    const minVal = min instanceof HTMLInputElement ? min.value.trim() : "";
    const priceVal = price instanceof HTMLInputElement ? price.value.trim() : "";
    return minVal === "" && priceVal === "";
  };

  const rowIsComplete = (row) => {
    const min = row.querySelector(".justccell-tier-min");
    const price = row.querySelector(".justccell-tier-price");
    const minVal = min instanceof HTMLInputElement ? min.value.trim() : "";
    const priceVal = price instanceof HTMLInputElement ? price.value.trim() : "";
    const minNum = Number(minVal);
    const priceNum = Number(priceVal.replace(/,/g, ""));
    return Number.isInteger(minNum) && minNum >= 1 && Number.isFinite(priceNum) && priceNum > 0;
  };

  const err = document.getElementById("justccell-tier-pricing-error");

  const validate = (alertOnFail) => {
    let ok = true;
    tbody.querySelectorAll(".justccell-tier-row").forEach((row) => {
      row.classList.remove("is-invalid");
      if (rowIsEmpty(row) || rowIsComplete(row)) {
        return;
      }
      row.classList.add("is-invalid");
      ok = false;
    });
    if (err instanceof HTMLElement) {
      err.hidden = ok;
      err.textContent = ok ? "" : msg();
    }
    if (!ok && alertOnFail) {
      window.alert(msg());
    }
    return ok;
  };

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
            input.value = "";
          }
        });
        row.classList.remove("is-invalid");
        return;
      }
      row.remove();
      reindex();
    });
  };

  tbody.addEventListener("input", () => {
    validate(false);
  });

  tbody.querySelectorAll(".justccell-tier-row").forEach(bindRemove);

  const addBtn = document.getElementById("justccell-tier-add");
  if (addBtn instanceof HTMLButtonElement) {
    addBtn.addEventListener("click", () => {
      const i = nextIndex();
      const tr = document.createElement("tr");
      tr.className = "justccell-tier-row";
      tr.innerHTML = `
        <td><input type="number" min="1" step="1" name="justccell_tier_min[${i}]" value="" class="short justccell-tier-min" /></td>
        <td><input type="number" min="0" step="1" name="justccell_tier_max[${i}]" value="" class="short justccell-tier-max" placeholder="0 = unlimited" /></td>
        <td><input type="text" name="justccell_tier_price[${i}]" value="" class="short wc_input_price justccell-tier-price" placeholder="0.00" /></td>
        <td><button type="button" class="button-link-delete justccell-tier-remove" aria-label="Remove tier">&times;</button></td>
      `;
      tbody.appendChild(tr);
      bindRemove(tr);
    });
  }

  const form = document.getElementById("post");
  if (form instanceof HTMLFormElement) {
    form.addEventListener("submit", (event) => {
      if (!validate(true)) {
        event.preventDefault();
      }
    });
  }
})();
