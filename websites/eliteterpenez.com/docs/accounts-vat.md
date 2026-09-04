> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Accounts, B2B/B2C & VAT Model — eliteterpenez.com

Taxation architecture, wholesale customer account models, and invoicing standards for **Elite Terpenes** (`eliteterpenez.com`).

---

## 1. Customer Account Models

Elite Terpenes serves both formulation laboratories/manufacturers (B2B) and individual enthusiasts/flavor creators (B2C):

| Account Type | Audience | Mandatory Fields at Registration / Checkout |
|---|---|---|
| **B2C (Retail)** | Individual consumers, home extractors | Full Name, Email, Shipping & Billing Address |
| **B2B (Wholesale)** | Licensed producers, vape brands, labs | Company Name, Business Email, VAT / Tax ID, VIES status (if EU) |

WooCommerce customer roles:
- `customer`: Default retail customer.
- `customer_b2b`: Approved wholesale account eligible for volume pricing tiers.

---

## 2. VAT & Tax Matrix (Spanish Operating Entity)

The client operates via a Spanish corporate entity:

### 2.1 B2B Wholesale Sales
| Delivery Destination | Tax Treatment | Requirements |
|---|---|---|
| **Spain (Domestic)** | Spanish VAT (IVA 21%) | Standard domestic transaction |
| **EU Member States (Intra-Community)** | 0% Reverse Charge (Excl. VAT) | Validated EU VAT number via VIES check |
| **EU Member States (Invalid/No VAT)** | Destination Country VAT (OSS) | Charged at local destination rate; do NOT zero-rate |
| **United Kingdom & Non-EU** | 0% Export (Excl. VAT) | Export documentation; duties & local taxes handled by buyer |

### 2.2 B2C Retail Sales
| Delivery Destination | Tax Treatment |
|---|---|
| **Spain (Domestic)** | Spanish VAT (IVA 21%) |
| **EU Member States** | Destination Country VAT under One-Stop-Shop (OSS) scheme |
| **United Kingdom & Non-EU** | Export rules (0% VAT charged; customs fees collected upon arrival) |

---

## 3. Inquiry-First vs Open Checkout

During the initial rollout phase:
1. Standard bottle sizes (5ml, 30ml) support open WooCommerce checkout (with 48-hour Justccell free shipping coupon support).
2. Bulk wholesale volumes (500ml, 1L, multi-liter drums) route through the **Wholesale Inquiry Modal** (`template-parts/inquiry/wholesale-modal.php`), capturing company name, VAT number, target volume, and delivery destination.
3. Once gateway and merchant accounts are approved by the client, bulk tiers will transition smoothly into automated WooCommerce B2B checkout.

---

## 4. Compliance & Invoice Generation

- **PDF Invoices:** Generated automatically upon order completion containing Spanish entity tax credentials, itemized VAT breakdown, and legal "Reverse charge / Inversión del sujeto pasivo" notices where applicable.
- **Never Guess Tax Rates:** Never rely on IP geolocation for tax calculation; always use the customer's verified billing/shipping country.
