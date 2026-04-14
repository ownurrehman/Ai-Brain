# Antigravity Awesome Skills (submodule)

[Sickn33/antigravity-awesome-skills](https://github.com/sickn33/antigravity-awesome-skills) is a **git submodule** at **`antigravity-awesome-skills/`** — sibling of **`skills/`** — with 1,300+ MIT **`SKILL.md`** playbooks. Pull updates from **`main`** in that repo, then commit the new submodule pointer in **Ai-Brain**.

## Layout (inside the submodule)

| Path | What |
|------|------|
| `antigravity-awesome-skills/skills/<skill-id>/SKILL.md` | Individual playbooks |
| `antigravity-awesome-skills/skills_index.json` | Search index |
| `antigravity-awesome-skills/CATALOG.md` | Browsable list |
| [Hosted catalog](https://sickn33.github.io/antigravity-awesome-skills/) | Web UI |

## Precedence

**`skills/`** (first-party) wins when both a custom skill and an Antigravity skill apply. Do not copy the whole upstream tree into **`skills/`**.

## Search (from Ai-Brain root)

```bash
python3 scripts/find_antigravity_skill.py security audit
python3 scripts/find_antigravity_skill.py playwright
```

## Clone (submodule missing)

```bash
git submodule update --init --recursive
```

## Update to latest upstream

```bash
cd antigravity-awesome-skills && git fetch origin && git checkout main && git pull
cd .. && git add antigravity-awesome-skills && git commit -m "chore: bump antigravity-awesome-skills submodule"
```

## Optional: Cursor `@skill` discovery

```bash
npx antigravity-awesome-skills --cursor
```

Smaller installs: upstream supports `--path`, `--category`, `--risk`, `--tags`. See the [upstream README](https://github.com/sickn33/antigravity-awesome-skills).
