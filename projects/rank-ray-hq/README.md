# Documentation

**Start here.** Repo root: **[../AGENTS.md](../AGENTS.md)** (commands + “before you code”).

Everything else is grouped by **purpose** so you only open one folder.

---

## Folder map

| Folder | What it is | When to open |
|--------|------------|----------------|
| **[core/](core/)** | Rules, roadmap, architecture, file map | Almost every task |
| **[modules/](modules/)** | Per-domain contracts (CRM, Finance, HRM, SEO, …) | Changing that module |
| **[seo/](seo/)** | SEO “app inside the app”: routes, APIs, journeys | SEO work only |
| **[product/](product/)** | Features matrix, positioning, release gate, GTM plans | Demos, scope, shipping |
| **[reference/](reference/)** | API notes, env keys, UI↔endpoint matrix | Lookup / integration |
| **[operations/](operations/)** | Bug ledger, QA checklist | Quality + incidents |
| **[audits/](audits/)** | Test results, system audits, click audits | Evidence / debugging |
| **[archive/](archive/)** | Old plans & brainstorms | Rarely |

---

## Read order (agents)

1. [core/RULES.md](core/RULES.md)
2. [core/ROADMAP.md](core/ROADMAP.md)
3. [core/ARCHITECTURE.md](core/ARCHITECTURE.md)
4. [core/FILE_MAP.md](core/FILE_MAP.md)

Then open **only** the folder that matches your task (e.g. `seo/` for SEO, `modules/` for module contract).

---

## Quick links

- [product/FEATURES.md](product/FEATURES.md) — what’s live vs partial + demo matrix  
- [seo/SEO_BLUEPRINT.md](seo/SEO_BLUEPRINT.md) — SEO screens ↔ code ↔ APIs  
- [audits/FULL_SYSTEM_AUDIT.md](audits/FULL_SYSTEM_AUDIT.md) — finance/CRM tab audit  
- [reference/api/crm.md](reference/api/crm.md) — CRM HTTP surface  
- [operations/BUG_LEDGER.md](operations/BUG_LEDGER.md) — regressions  

---

## Discipline

- Put new **module** docs only in `modules/`.
- Put new **SEO route/API** detail in `seo/` (keep blueprint + journey as the two files unless you split intentionally).
- Put **shipping / positioning / checklist** stuff in `product/`.
- Put **keys, matrices, small API dumps** in `reference/`.
- Keep **audits** under `audits/` only.
