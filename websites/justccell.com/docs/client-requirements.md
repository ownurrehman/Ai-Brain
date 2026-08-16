# Client requirements (source of truth)

Received 2026-08-14 from 3Devices via the project owner. Section **2/6 was not included** in the message. If 2/6 arrives, append it here and update the roadmap — do not guess it.

Copy below is the client’s wording, then our implementation reading.

---

## 1/6 — General idea (one global website)

One single global website. Show the right version from the visitor’s country (IP).

For justccell.com:

| Connection | Path | Default language | Currency |
|---|---|---|---|
| UK | `/uk` | English | GBP |
| Spain | `/es` | Spanish | EUR |
| Germany | `/de` | German | EUR |
| France | `/fr` | French | EUR |
| Italy | `/it` | Italian | EUR |
| Other countries | `/other` | English | EUR |

Defaults only. A **language selector (top-right)** must always let the visitor pick English, Spanish, French, German, or Italian.

**2026-08-16 (owner):** selector also includes **Arabic** and **Russian** for additional customers. Store defaults in the table above are unchanged. Dubai `/ae/` still defaults to English; visitors can switch to Arabic.

Example: visitor from Spain lands on Spanish + EUR, then switches language to English **while remaining on the Spanish / EU store** (EUR, Spain/EU commercial rules).

Other domains (e.g. 3devicescorp.com) redirect into the **same** platform with the same country detection.

**Bottom line:** one website, one platform, multiple domains, countries, languages, and currencies.

### How we will build this

Country and language are **two independent axes**. Country (store) drives currency and tax region. Language drives UI copy only.

Do not use WPML’s default `/es/` = “Spanish language” as the only prefix — that collides with “Spain store”. Canonical pattern:

`https://justccell.com/{store}/{lang}/…`

Example: `/es/en/product/tank` = Spain store, English UI, EUR.

Details: [geo-language-currency.md](geo-language-currency.md).

---

## 3/6 — Customer accounts & VAT

On account creation, identify:

- **B2B** — registered company with a valid VAT number
- **B2C** — private consumer

Two account types: Business and Consumer.

VAT is calculated from **account type + delivery country + VAT status**.

Example for orders **invoiced by the Spanish company**:

**B2B**

1. Spain → Spanish VAT
2. Another EU country + valid EU VAT number → excluding VAT (intra-community)
3. Outside EU → excluding VAT, export rules

**B2C**

1. Spain → Spanish VAT
2. Another EU country → VAT per EU / OSS rules
3. Outside EU → export VAT rules

Details: [accounts-vat.md](accounts-vat.md).

---

## 4/6 — Domains & company emails

- `info@justccell.com` — main inbox
- `info@3devicescorp.com` — forward to `info@justccell.com`

Both domains exist for **trademark protection**. CCELL is a registered trademark. justccell.com may be fine today; 3devicescorp.com is the safety net if they must stop using a CCELL-containing domain.

justccell.com = commercial brand. 3devicescorp.com = same platform, switchable primary domain.

Details: [domains-email.md](domains-email.md).

---

## 5/6 — Ownership & control

3Devices must have **full ownership and control** of everything: domains, hosting, WordPress/WooCommerce admin, database, customer data, emails, backups, FTP/SFTP.

Developers may build and maintain. Developers must **never** be the only people with admin or technical control.

They cannot repeat the previous incident (Indian developers / internal dispute → lost control of their own site).

3Devices remains the ultimate owner and administrator of all assets and access.

Details: [ownership-control.md](ownership-control.md). This is a **process requirement**, not a theme feature — it is blocking.

---

## 6/6 — Security

High security from day one: site, customer data, payment environment. Protect against hacking, breaches, and attacks. Proper measures + regular backups.

Details: [security.md](security.md).

---

## Constraints we already accepted in the build

- Visual/structural clone of ccell.com for design approval; **reference images are temporary**.
- Inquiry-first catalog is acceptable until checkout + VAT is ready; the URL and store model must still allow country prefixes.
- Rank Math SEO Free (not Yoast; AIOSEO Woo module was paid). One SEO plugin only. Hreflang via WPML SEO in the sitemap, not `<head>`.
- Custom lightweight theme (no Elementor). Vanilla JS + BEM/CSS variables.
