> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/INDEX|🌐 justccell.com Hub]]

**JUSTCCELL DEV RULES (Confirm before executing):**
- Use only Hostinger MCP to access and update live `justccell.com` (staging site is dead). No GUI logins.
- Never touch WordPress or WooCommerce core. Edit templates/CSS in `justccell-theme/` and PHP logic in `plugins/justccell-features/` (`inc/` is retired).
- Never return `false` or `null` from `acf/load_field_group` (crashes wp-admin). Always test Page and Product edit screens after any ACF edits.
- All text and buttons must be editable in wp-admin (no hardcoded PHP text). Never mention "samples" (wholesale only). No links or images from `ccell.com`.
- Keep the root folder clean (notes/specs go in `docs/`, reports in `reports/`). Read `features-code-map.md` first, and update `features-code-map.md`, `docs/STATUS.md`, and `docs/BUILD-LOG.md` when you ship changes.

Prompt:

