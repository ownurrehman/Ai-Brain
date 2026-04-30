# Agent Team & Operation Rules

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

- **Content Creation:** Any blog, page, or meta-description task $\rightarrow$ `main` (internal Enigma mode)
- **Technical Work:** Any code change, API fix, or WP development $\rightarrow$ `chronos` (standard) or `nemo` (extreme/complex)
- **Deep Research:** Any SERP analysis or keyword clustering $\rightarrow$ `chronos`
- **Outreach & Leads:** Any email management, cold drafting, or prospecting $\rightarrow$ `main` (internal Emilia mode)
- **Complex Audits:** Any multi-step technical or on-page audit $\rightarrow$ `chronos` (or `nemo` for infrastructure)
- **Data Processing:** Any file transformation or large-scale data extraction $\rightarrow$ `chronos`
- **Extreme Engineering:** Complex refactoring, high-level architecture, and critical bug fixing $\rightarrow$ `nemo`

### When NOT to Spawn

- Simple questions or one-liner answers.
- Configuration edits that take 1-2 tool calls.
- Tasks requiring real-time back-and-forth with the user.

**Execution:** Use `sessions_spawn` with `mode="run"`, the correct `agentId`, and the mapped `model`.

---

## Communication Hygiene (Mandatory for All Agents)

**Objective:** Prevent "Machine Language Leaks" and "Silence Gaps."

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
