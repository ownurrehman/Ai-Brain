# Machine Runtime SSOT (macOS)

> **Goal:** One clear runtime map so Cursor, OpenClaw, Hermes, Gemini, and Ai Brain stop fighting each other.

Last sealed: 2026-07-15

---

## Why it felt broken

You had **three different Node.js installs** on PATH:

| Source | Typical path | Version seen | Role |
|--------|--------------|--------------|------|
| **nvm (canonical)** | `~/.nvm/versions/node/v24.18.0` | **24.18.0** | Shell + OpenClaw CLI/gateway |
| Homebrew | `/opt/homebrew/opt/node` | 25.6.1 | Gemini CLI shebang only (unlinked from PATH) |
| Official/pkg | `/usr/local/bin/node` | 24.11.1 | Ignore — never put first on PATH |

OpenClaw `2026.7.1` engines: `>=22.22.3 <23 \|\| >=24.15.0 <25 \|\| >=25.9.0`.

**npm globals are per Node version.** If `nvm alias default` is 24 but `openclaw` was only installed under 22, Terminal shows `command not found` even while the gateway LaunchAgent is fine. Fix: `nvm use 24 && npm i -g openclaw@latest && openclaw gateway install --force`.

Homebrew `25.6.1` is outside the supported range — keep it unlinked.

---

## Canonical rule (memorize this)

1. **Interactive shells + npm globals + OpenClaw** → **nvm Node 24** (currently `v24.18.0`)
2. **Gemini CLI** → keeps using Homebrew Cellar Node via shebang (`#!/opt/homebrew/opt/node/bin/node`). Homebrew `node` stays **installed** but **unlinked** from `/opt/homebrew/bin` so it does not steal PATH.
3. **Hermes Agent** → **Python** (`~/.hermes/.../venv`). Bundled Node at `~/.hermes/node` (v22.22.3) is only for Hermes-side JS helpers.
4. **Cursor** → Electron app; its integrated terminal must load `~/.zshrc` (nvm default). Does not need Homebrew Node.
5. **Ai Brain** → knowledge/memory vault at  
   `~/Ai Works - Local/Ai Codes/Ai Brain/`  
   Not a Node runtime. Agents read it by absolute path.

---

## Services (LaunchAgents)

| Service | Label | Runtime |
|---------|-------|---------|
| OpenClaw gateway | `ai.openclaw.gateway` | nvm `v24.18.0` + global `openclaw` |
| Hermes gateway | `ai.hermes.gateway` | Hermes venv Python |
| OpenClaw dashboard | `com.openclaw.dashboard` | system python3 |

Check:

```bash
ai-runtime-check
# or
openclaw gateway status
launchctl print "gui/$(id -u)/ai.hermes.gateway" | head
```

---

## Monthly / after any Node upgrade

```bash
ai-runtime-sync
```

That script:

1. `nvm install 24 && nvm alias default 24`
2. Reinstalls `openclaw@latest` on that Node
3. Runs `openclaw gateway install --force` + restart
4. Keeps Homebrew `node` unlinked
5. Prints a health report

---

## Do / Don’t

**Do**

- Open a **new terminal** after Node changes (`hash -r` or quit Terminal)
- Run `ai-runtime-sync` before big OpenClaw upgrades
- After changing nvm default, reinstall OpenClaw on that Node (`npm i -g openclaw@latest`) so CLI and gateway match
- Keep Ai Brain memory/logs under `Ai Brain/memory/` and `Ai Brain/agents/*`

**Don’t**

- `brew link node` again (puts Node 25 back on PATH)
- `npm i -g openclaw` under a random Node without reinstalling the gateway service
- Assume Cursor’s old tab has the new Node — it often keeps a stale PATH

---

## Quick recovery

```bash
nvm use 24
hash -r
node -v                    # expect v24.15+
openclaw --version
openclaw gateway status
gemini --version           # uses Homebrew Cellar via shebang
hermes --version
```

If `openclaw: command not found`: you switched Node versions without reinstalling the global CLI → `npm i -g openclaw@latest`.
If OpenClaw says Node too old: you are in a stale shell → `nvm use 24` or new tab.

If Gemini breaks after someone ran `brew uninstall node`:  
`brew install node && brew unlink node` (install for shebang, unlink for PATH).
