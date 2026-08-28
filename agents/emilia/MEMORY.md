# Emilia Memory (B2B Cold Outreach & Conversion)

> **Parent Hub:** [[INDEX|🧠 Master Ai Brain Hub]] · **Fleet Dashboard:** [[agents/FLEET-ORCHESTRATION|🤖 Agent Fleet]]
> **Primary Collaborators:** [[agents/hermes/MEMORY|Hermes (Manager)]] · [[agents/scout/MEMORY|Scout (Intel)]] · [[agents/enigma/MEMORY|Enigma (Content)]]
> **Operating Rules:** [[rules/content/content-rules|📜 Content Standards]] · [[skills/marketing-copywriting/SKILL|⚡ Marketing Copywriting]]
> **Swarm Campaigns:** [[reports/growth-swarm-report-2026-08-28_19-39-44|RankRay Cold Outreach Campaign]]

---

AgentMail send endpoint: POST /v0/inboxes/{id}/messages/send (with /send suffix; without = 404). Verified 2026-08-17.
§
AgentMail free tier: new accounts capped at 10 distinct outbound recipients/week (sender counts as 1). Lifts ~7 days after inbox creation.
§
AgentMail: outbound messages have empty from_ field; use labels ('sent'/'received') to filter. /threads lags; /messages?labels=...&limit=50 is reliable.
§
Probe new email endpoints with HEAD/OPTIONS or non-recipient targets; a self-test burned 1 of 10 recipient slots (rankray batch 1).
§
Oliver rejects AI-slop outreach copy. First-pass LLM drafts never final: delegate rewrites to stronger sibling (Hermes main, Enigma) referencing humanizer + marketing-copywriting skills, require real site observations, gate every send with explicit human APPROVE.
§
Cold outreach defaults (Rank Ray): plain text, 80–110 words, lowercase 2–4 word subject, "Hi," opener, sign "Oliver" only, low-friction CTA ("Reply if useful."), 10-prospect batches with vertical diversity, scrape each prospect's site before drafting.
§
Tonic Physio WP: TONICPHYSIO_WP_URL already includes full path .../wp-json/wp/v2/ (don't re-append; 404s). Writes need WP_APP_PASS; user/pass only works for public GETs. Categories 58=Guides, 1=News. Authors: 5=Brenda Azzopardi, 10=Dan Torres. Blogs at /blog/<slug>/.
§
Dedup content plans against live WP slugs + post-sitemap before writing/delegating: 2026-08-28 tonicphysio swarm plan had 13/45 ideas colliding with published posts.
§
2026-08-28: Oliver halted a 4-subagent blog batch mid-run; checkpoint before launching large delegated content batches, like the APPROVE gate for outreach sends.