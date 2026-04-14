# Skills in this brain (two layers)

## 1) First-party — this folder (`./`)

These are **not dummy files**. Each **`SKILL.md`** is the **Rank Ray control layer**: triggers, inputs, workflow outline, checks, and **product-specific** rules (e.g. RankRay-HQ SEO UI, no fake metrics). They stay short on purpose.

**Depth** (long playbooks, hundreds of lines) lives in **`../antigravity-awesome-skills/skills/<id>/SKILL.md`**. Every first-party skill links there under **Deep playbooks (Antigravity Awesome Skills)**. See **`_CATALOG_MAP.md`** for the full mapping table.

**Always open the first-party `SKILL.md` first**, then load catalog files if you need more detail. When both could apply, **first-party wins** for Rank Ray constraints; the catalog **extends**, not replaces, this folder.

### [Rank Ray](https://www.rankray.com) service-line skills

| Folder | Use for |
|--------|---------|
| `digital-marketing/` | Multi-channel marketing, retainers, cross-channel plans |
| `seo-services/` | SEO client delivery (audits, roadmaps, implementation support) |
| `web-development/` | Websites, WordPress, React/Next/Vite |
| `app-development/` | Mobile / React Native / Flutter |
| `saas-development/` | SaaS products, tenancy, billing, APIs |
| `ai-automation/` | n8n, MCP, agent tooling, LLM integrations |
| `crypto-trading/` | Trading bots & risk — **`Crypto Trading Bots/Legendary Bot/AGENTS.md`** |

Full one-page map: **`../SKILLS.md`**.

---

## 2) Antigravity Awesome Skills — sibling folder, not inside `skills/`

The full **[Antigravity Awesome Skills](https://github.com/sickn33/antigravity-awesome-skills)** catalog is a **git submodule** at:

```text
../antigravity-awesome-skills/skills/<skill-id>/SKILL.md
```

Same level as **`skills/`** under **Ai-Brain** — fewer nested folders, easy to update from [upstream `main`](https://github.com/sickn33/antigravity-awesome-skills).

**Why we do not copy those folders into `skills/`:**

- **Size & noise** — 1,300+ directories.
- **Updates** — `cd antigravity-awesome-skills && git pull` then commit the submodule pointer in Ai-Brain.
- **Name clashes** — upstream ids could collide with your custom folder names.

Agents **are expected to read Antigravity skills** when first-party coverage is not enough.

See **`../ANTIGRAVITY.md`** for clone, bump, and optional Cursor install.

---

## How agents should find an Antigravity skill

1. **Machine-readable search** (preferred):

   ```bash
   cd /path/to/Ai-Brain
   python3 scripts/find_antigravity_skill.py security
   python3 scripts/find_antigravity_skill.py playwright
   ```

   Then open the printed path under `antigravity-awesome-skills/…`.

2. **JSON** — `../antigravity-awesome-skills/skills_index.json`.

3. **Catalog** — `../antigravity-awesome-skills/CATALOG.md` or the [hosted catalog](https://sickn33.github.io/antigravity-awesome-skills/).

4. **Ripgrep:**

   ```bash
   rg -i "playwright" antigravity-awesome-skills/skills_index.json | head
   ```

---

## Cursor `@` mentions (optional)

Cursor does not auto-index the submodule path. For **@skill** discovery:

```bash
npx antigravity-awesome-skills --cursor
```

See **`../ANTIGRAVITY.md`** and the upstream README.
