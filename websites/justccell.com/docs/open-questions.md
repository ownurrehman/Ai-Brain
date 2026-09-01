> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Open questions

Ask 3Devices. Do not invent answers in code.

| # | Question | Blocks |
|---|---|---|
| Q1 | **2/6 merchandising** (2026-08-26): ES/CH landings, wholesale tiers + two dropdowns, WhatsApp/Telegram, packaging + laser, collection. Recorded in client-requirements.md. | Done in theme 0.9.0 |
| Q2 | Legal seller name, Spanish VAT ID, registered address, invoice footer | D2 tax / invoices |
| Q3 | Accountant: UK B2C goods VAT (IOSS / UK VAT registered or export-only)? | `uk` store tax |
| Q4 | Are GBP prices a **conversion** of EUR or a separate UK price list? | Currency plugin |
| Q5 | Stay **inquiry-only** long term, or add card checkout? **2026-08-28:** they asked to integrate **their payment gateway** plus **UPS and FedEx**. Wholesale layout is live; Woo cart still off until those credentials + VAT exist. | Payments, PCI, shipping |
| Q6 | Is **justccelldevices.com** in scope (redirect / park / ignore)? | Domains |
| Q7 | Preferred mailbox host: Hostinger email vs Google Workspace (3Devices billing)? | 4/6 email |
| Q8 | Who at 3Devices receives Hostinger, Cloudflare, WP, and registrar invites **this week**? | 5/6 ownership |
| Q9 | Product photography: when do 3Devices assets replace CCELL reference images? | Legal / A5 |
| Q10 | Any countries to **block** or treat differently (shipping restrictions, cannabis hardware rules)? | Store `other` + checkout |
| Q11 | Woo shop default = UK / GBP. Add EUR USD CHF AED in WCML (Germany = EUR). Arabic + Russian stay. | C4 / currencies |
| Q12 | Dubai currency AED vs USD? Switzerland default language EN vs DE? | Store `ae` / `ch` |
| Q13 | Collection: Woo local pickup (address + hours) vs copy-only? Pickup widget like [genuineccell](https://www.genuineccell.co.uk/collections/pod-systems/products/ccell-eazie-pro-battery-vape-pod-system). | Shipping |
| Q14 | First public version: **quotes only** (keep processing paid orders offline) or **paid Woo checkout** as soon as gateway + UPS/FedEx are in? Client wants site ASAP and currently processes orders manually. | Go-live scope |
| Q15 | Spain/EU domain name (client: new domain covering all EU markets)? | Separate Spain/EU site; not justccell.com `/es/` |

When an answer lands, write it here with the date, then update the relevant spec and [BUILD-LOG.md](BUILD-LOG.md).
