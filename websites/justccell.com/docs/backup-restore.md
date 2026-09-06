> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/rules|📜 Site Rules]]

# Theme backup & restore — justccell.com

**Goal:** we can always roll the live theme back to a known-good version if a change breaks the site. Keep **at least 10** restorable theme versions at all times.

The live theme is deployed in place (TUS) into `wp-content/themes/justccell-theme/` — there is no "previous folder" on the server to fall back to (Rule §11: one theme folder only). Backups therefore live in the vault + git + Hostinger, never as duplicate theme slugs on the server.

---

## Three backup layers

| Layer | Where | Covers | Retention | Type |
|---|---|---|---|---|
| **1. Git history** (primary) | `.git` + GitHub `origin` | Every committed theme + docs state | Unlimited (lossless) | Offsite when pushed |
| **2. Local snapshots** | `archive/theme-releases/<version>/` (+ `_zips/`) | Full theme (code + assets) per version | Newest **10** (auto-rotated) | Local disk |
| **3. Hostinger UpdraftPlus** | Client-owned offsite (Drive/S3) | Whole site **+ database** | Per schedule | Offsite full-site |

`archive/theme-releases/` is **gitignored** on purpose — snapshots are heavy (~53 MB each) and would bloat the repo. Git already versions the theme losslessly; the snapshot folder is the fast, tangible "grab a whole known-good copy" layer.

> ⚠️ **Commit discipline matters.** Git only protects what is committed. If the working tree is at `0.9.296` but the last commit is `0.9.232`, git can only restore to `0.9.232`. **Commit + tag after every deploy** (below).

---

## Layer 2 — local snapshot script

`scripts/backup-theme.sh` freezes the current working copy and keeps only the newest 10 versions.

```bash
cd "websites/justccell.com"
bash scripts/backup-theme.sh            # snapshot current JUSTCCELL_VERSION
bash scripts/backup-theme.sh 0.9.296    # or snapshot an explicit version
```

- Reads `JUSTCCELL_VERSION` from `justccell-theme/functions.php`.
- Copies to `archive/theme-releases/<version>/` (full `rsync -a --delete`, `.DS_Store` excluded).
- Prunes to the newest 10 version folders (semantic-version sort).
- `archive/theme-releases/_zips/` holds legacy full-theme zips (0.9.93 / 0.9.122 / 0.9.140 / 0.9.144) as extra restore points; not rotated.

**Run it as part of every deploy** (after the version bump, before/after TUS upload).

---

## Layer 1 — git tag on every deploy

After deploying and updating the vault, capture a durable restore point:

```bash
cd "<repo root>"
git add "websites/justccell.com"
git commit -m "justccell theme 0.9.296 — <what shipped>"
git tag justccell-theme-0.9.296
git push origin main --tags
```

Tags give named, one-command restore points and push the theme offsite to GitHub.

---

## Restore runbooks

### A. Roll the theme back to a previous version (git — preferred)

```bash
# See versions in history
git log --oneline -- websites/justccell.com/justccell-theme

# Restore the WHOLE theme from a tag or commit into the working tree
git checkout justccell-theme-0.9.295 -- websites/justccell.com/justccell-theme
#   …or by commit hash:
git checkout <commit> -- websites/justccell.com/justccell-theme
```

Then re-deploy the restored files in place via TUS to `wp-content/themes/justccell-theme/`, bump nothing (version is already in the restored files), and clear cache (Rule §6).

### B. Restore from a local snapshot folder

```bash
cd "websites/justccell.com"
rsync -a --delete archive/theme-releases/0.9.295/ justccell-theme/
```

Then deploy the working copy in place via TUS and clear cache.

### C. Restore from a legacy zip

```bash
cd "websites/justccell.com"
unzip -o archive/theme-releases/_zips/justccell-theme-0.9.144.zip -d /tmp/jc-restore
rsync -a --delete /tmp/jc-restore/justccell-theme/ justccell-theme/
```

Then deploy in place + clear cache.

### D. Full site + database rollback (last resort)

Use **UpdraftPlus** in wp-admin (client-owned offsite storage). This restores WordPress core, plugins, uploads, **and the database** — use it when the problem is data/config, not just theme files. Theme-only regressions should use A/B/C above (faster, no DB risk).

---

## Rules

- **Never** create a second theme slug on the server to "keep a backup" (Rule §11). Backups live here, in git, and in UpdraftPlus — not as `justccell-theme-0.9.x` folders in `wp-content/themes/`.
- **Never** commit `archive/theme-releases/` (it is gitignored — heavy binaries).
- **Never** commit secrets. Theme + docs only; REST keys stay in `wp-config.php` / settings screens.
- After every deploy: run `scripts/backup-theme.sh`, then `git commit` + `git tag` + `git push`.

See also: [[websites/justccell.com/rules|rules.md]] §6 (deploy) · [[websites/justccell.com/backups/INDEX|ACF field-group backups]] · [[websites/justccell.com/docs/ownership-control|Ownership & control]].
