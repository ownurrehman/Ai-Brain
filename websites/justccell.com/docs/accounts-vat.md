# Accounts, B2B/B2C, and VAT

Invoicing entity in the brief: **Spanish company**. Confirm legal name, VAT ID (ES…), and registered address before going live with tax.

## Account types

| Type | Who | Required at registration |
|---|---|---|
| Consumer (B2C) | Private person | Name, email, password, billing country |
| Business (B2B) | Registered company | Company name, country, **VAT number**, VIES check when EU |

WooCommerce roles (proposed): `customer` (B2C) and `customer_b2b` (B2B). Admins never use these for staff.

Registration UI: two tabs or a clear radio — “I’m a business” / “I’m a consumer”. Do not hide VAT behind checkout-only if B2B pricing or net quotes depend on it.

## VAT matrix (Spanish seller)

### B2B

| Delivery | VAT |
|---|---|
| Spain | Spanish VAT (IVA) |
| Other EU + **valid** EU VAT (VIES) | Reverse charge / excluding VAT |
| Other EU + **invalid / missing** VAT | Treat as B2C OSS (do not silently zero-rate) |
| Outside EU | Excluding VAT, export documents |

### B2C

| Delivery | VAT |
|---|---|
| Spain | Spanish VAT |
| Other EU | Destination VAT under **OSS** (goods distance selling) |
| Outside EU | Export rules (typically 0% VAT + customs on buyer; confirm with accountant) |

UK after Brexit is **outside EU** for this matrix (store `uk` is still GBP UX). UK VAT on goods may still apply depending on Incoterms and whether they use a UK IOSS/VAT scheme — **accountant decision**, listed in open questions.

## Implementation

1. WooCommerce Tax enabled; origin Spain.
2. EU VAT Number plugin with VIES (or built-in Woo EU VAT if it covers VIES + B2B exemption).
3. OSS rates for EU B2C (WooCommerce EU VAT compliance / Tax rates CSV from accountant).
4. Checkout: if B2B and VIES pass → tax class `zero-rate` / reverse charge note on invoice.
5. Invoices: PDF with Spanish company details, VAT breakdown, “Reverse charge” wording where required.
6. Quote/inquiry emails (current phase) should still **ask** B2B vs B2C + VAT number so sales does not quote the wrong net/gross.

## What not to automate blindly

- Do not zero-rate B2B without a passing VIES check.
- Do not use IP country as the tax country.
- Do not mix UK GBP display with “EU OSS” as if GB were still in the Union.

## Inquiry-first vs checkout

Until card checkout is on, the **account model and fields** should still exist so quotes and later orders share the same customer records. Theme quote form will gain: account type, company, VAT, country.
