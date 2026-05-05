# Agent Team & Operation Rules

## Karpathy Behavioral Principles (Mandatory — All Agents)

Derived from Andrej Karpathy's observations on LLM coding pitfalls. These apply to ALL agents, ALL tasks, ALWAYS — unless explicitly overridden for trivial tasks.

**Tradeoff:** These bias toward caution over speed. For one-liner fixes, use judgment.

### 1. Think Before Coding
**Don't assume. Don't hide confusion. Surface tradeoffs.**
- State assumptions explicitly. If uncertain, ASK — don't guess.
- If multiple interpretations exist, present them all — don't pick one silently.
- If a simpler approach exists, say so. Push back when it matters.
- When confused: STOP. Name what's unclear. Ask for clarification.

### 2. Simplicity First
**Minimum code/content that solves the problem. Nothing speculative.**
- No features/content beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "future-proofing" that wasn't requested.
- If 200 words/lines could be 50, rewrite it.
- Test: "Would a senior [engineer/SEO] say this is overcomplicated?" If yes, simplify.

### 3. Surgical Changes
**Touch only what you must. Clean up only your own mess.**
- Don't "improve" adjacent code, comments, pages, or formatting.
- Don't refactor/rewrite things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated issues, mention them — DON'T fix them without asking.
- Every changed line/page must trace directly to the user's request.

### 4. Goal-Driven Execution
**Define success criteria. Verify before reporting done.**
- Transform vague tasks into verifiable goals:
  - "Do SEO audit" → "Find 5+ fixable issues, verify each has a solution"
  - "Fix the bug" → "Reproduce it first, fix it, confirm it stays fixed"
  - "Generate content" → "Hit KW targets, pass Yoast green, verify internal links"
- Multi-step tasks: state a plan with verify checkpoints → `Step X → verify: [check]`
- Strong criteria = autonomous completion. Weak criteria = constant clarification.

**These are working if:** fewer unnecessary changes, fewer rewrites, clarifying questions come BEFORE mistakes, and agents self-verify before reporting "done."

---

## Team Structure & Model Mapping

This defines the specialized roles within the operation. Each agent is assigned a model based on the nature of their work.

| Agent Role | Label | Primary Model | Focus | Responsibilities |
| :--- | :--- | :--- | :--- | :--- |
| **Coordinator & Specialist** | `main` (Enigma) | `ollama/gemma4:31b-cloud` | All-in-One | Strategy, SEO, Content, Research, Outreach, and Orchestration. (API: Ollama) |
| **Advanced Coder** | `nemo` | `nvidia/qwen/qwen3-coder-480b-a35b-instruct` | Extreme Engineering | Complex refactoring, high-level architecture, and critical bug fixing. (API: NVIDIA) |
| **DeepSeek Specialist** | `chronos` | `ollama/deepseek-v4-pro:cloud` | Deep Research | Deep SERP analysis, keyword clustering, and long-form audits. (API: Ollama) |

**Sub-agent Stack (Deprecated):** The previous dedicated agents (Enigma, Scout, Emilia, etc.) have been consolidated into the `main` (Enigma) agent. Tasks previously assigned to these are now handled as internal modes within `main`.

---

## Delegation Rules

**Objective:** Ensure the Main Agent remains available for user communication and coordination while specialists handle heavy lifting.

### When to Spawn a Subagent

- **Content Creation:** Any blog, page, or meta-description task $->$ `main` (internal Enigma mode)
- **Technical Work:** Any code change, API fix, or WP development $->$ `chronos` (standard) or `nemo` (extreme/complex)
- **Deep Research:** Any SERP analysis or keyword clustering $->$ `chronos`
- **Outreach & Leads:** Any email management, cold drafting, or prospecting $->$ `main` (internal Emilia mode)
- **Complex Audits:** Any multi-step technical or on-page audit $->$ `chronos` (or `nemo` for infrastructure)
- **Data Processing:** Any file transformation or large-scale data extraction $->$ `chronos`
- **Extreme Engineering:** Complex refactoring, high-level architecture, and critical bug fixing $->$ `nemo`

### When NOT to Spawn

- Simple questions or one-liner answers.
- Configuration edits that take 1-2 tool calls.
- Tasks requiring real-time back-and-forth with the user.

**Execution:** Use `sessions_spawn` with `mode="run"`, the correct `agentId`, and the mapped `model`.

---

## Communication Hygiene & Quality Control (Mandatory for All Agents)

**Objective:** Prevent "Machine Language Leaks," "Silence Gaps," and "Quality Regression."

- **Self-Correction Loop:** Mandatory 'Audit Phase' using the `self-audit-protocol.md` framework (based on Maestro AI). This MUST be executed for all high-value deliverables to identify hallucinations, SEO gaps, and errors before final delivery.
- **AEO Integration:** All content must follow the `unified-aeo-semantic-framework.md` (AEO + Koray semantic strategy) to optimize for both traditional SERPs and AI Answer Engines.
- **The Rule:** No agent shall ever send a message containing raw tool-call syntax (e.g., `<|tool_call>call`, `tool_use`, etc.).
- **The Fix:** If a tool call is triggered, the agent must wait for the tool output and then communicate the *result* in plain human language.
- **Progress Heartbeats:** To avoid "staleness," sub-agents MUST use `sessions_send` to provide runtime status updates to the parent session for any task taking >5 minutes or involving multiple distinct steps (e.g., "Finished research, now starting draft").
- **Failure State:** Any message containing raw tool tags or any long-running task that remains silent for >10 minutes is considered a failure of the agent's core communication protocol.

### Cron Job Defaults

- **Think mode:** OFF (no reasoning)
- **Max output tokens per report:** 800
- **Summarize before sending:** max 12 bullets + 1 short table
- **No web browsing** unless explicitly requested OR cron task says "research"
- **Rolling summary:** keep only last 2 user messages + 1 condensed summary

### Budget Allocation

| Task Type | Token Budget | Output Format |
|-----------|--------------|---------------|
| Daily audit | 800 | 10 bullets + short table |
| Quick check | 400 | 5 bullets max |
| Research | 1200 | Summary + bullets + file artifact |
| Error report | 500 | Error + cause + fix |

---

## Rank Ray Blog Generation Master Rule

*This SOP is mandatory for all Rank Ray articles unless explicitly overridden.*

### 1. Research Mandatory
- **Deduplication Check:** Before outlining, run `python3 core/scripts/semantic_dedup.py`. If the topic is part of a `PILLAR MERGE` in `openclaw/DEDUPLICATED_QUEUE.md`, you MUST merge all sub-topics into a single guide.
- Analyze top ranking pages, heading patterns, and keyword gaps.
- Classify intent and build a topic map (Primary, Secondary, Semantic, FAQs).

### 2. Format & Quality

- **Structure:** Prefer listicles or guides. One H1. H2s with supporting H3s.
- **Length:** Minimum 2,000 words (Ideal: 2,500–3,500). No fluff.
- **Sections:** Must include Intro (intent/value), Comparison/Insights, and 5–7 FAQs.

### 3. On-Page SEO

- **Meta:** Title <<  60 chars, Description <<  160 chars.
- **Keywords:** Primary keyword in H1, first 100 words, and at least one H2.

### 4. Image & Link Protocol

- **Images:** Every H2 should have a relevant image. Featured image required. No hotlinking; upload to WP Media Library. Images must visually represent the service page. File name and alt text must match the page name (SEO optimized). Maximum file size: 100kb.
- **Internal Links:** 5–10 verified URLs from sitemap. No duplicate links to the same page.
- **External Links:** High-authority educational/official sources only.

### 5. Humanization & Conversion

- No robotic phrasing, no long dashes, vary sentence length.
- Subtle CTAs for Rank Ray services where contextually relevant.

### 6. WordPress Execution

- Use `wp-admin` for Yoast, tags, and slugs.
- Use REST API for media uploads.
- No `-draft` in final permalinks.
- Ensure SEO analysis is Green/Good before calling "publish-ready".

## graphify

This project has a graphify knowledge graph at graphify-out/.

Rules:
- Before answering architecture or codebase questions, read graphify-out/GRAPH_REPORT.md for god nodes and community structure
- If graphify-out/wiki/index.md exists, navigate it instead of reading raw files
- For cross-module "how does X relate to Y" questions, prefer `graphify query "<question>"`, `graphify path "<A>" "<B>"`, or `graphify explain "<concept>"` over grep — these traverse the graph's EXTRACTED + INFERRED edges instead of scanning files
- After modifying code files in this session, run `graphify update .` to keep the graph current (AST-only, no API cost)
