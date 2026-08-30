---
name: audit-and-research
description: "Master playbook for technical research, codebase mapping, security audits, AI code quality checks, systematic debugging, and verification gates. Integrates Deep Research, Wiki Systems Analysis, OWASP security assessments, Vibe Code Audits, and automated testing validations."
risk: safe
source: community
date_added: "2026-06-03"
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Technical Audit, Security & Research Playbook

## Overview
This master playbook combines advanced processes for codebase analysis, technical research, vulnerability auditing, rapid code review, and systematic debugging/verification. It ensures all analytical tasks are evidence-based, thorough, and verified using strict gates.

---

## 1. Deep Research & Information Gathering
* **Structured Queries:** When tasked with complex market, competitor, or technical research, design a query plan first.
* **Evidence & Citations:** Ground every claim in verified external documentation, API references, or academic sources. Never hallucinate facts.
* **Output Formats:** Deliver research as structured Markdown reports with executive summaries, comparison tables, and direct source links.

---

## 2. Codebase Mapping & Systems Analysis (Wiki Researcher)
* **Depth Before Breadth:** Trace execution paths all the way down. Read actual file implementations; never assume based on file names or directory structures.
* **Evidence Standard:** Ground architectural claims in concrete code references using the format: `[functionName](file:///path/to/file#L123-L145)`.
* **Structural Diagrams:** Use Mermaid diagrams (with dark-mode compatibility) to visualize data flows and component relationships.
* **Boundary Scopes:** Explicitly document the boundaries of your analysis — clearly state what has been traced and what remains unexplored.

---

## 3. Security Auditing & Vulnerability Scanning
* **OWASP Top 10 Checklist:** Evaluate applications for:
  - Injection (SQL, Command, LDAP)
  - Broken Authentication & JWT misconfigurations
  - Broken Access Control (IDOR, missing middleware gates)
  - Insecure Deserialization
  - Security Misconfigurations (insecure defaults, exposed `.env` files)
* **Middleware & Access Control Gate Checks:** Verify that authentication/authorization checks occur at all entry points. Ensure admin service accounts do not bypass row-level database security policies (RLS).
* **SSRF & DNS Validation:** Check DNS resolution and validate IP pinning when performing calls to internal endpoints.
* **Dependency & SAST Auditing:** Keep dependency versions scanned. Use static analysis (e.g. Semgrep, CodeQL) to catch potential logic leaks.

---

## 4. AI-Generated Code Auditing (Vibe Code Auditor)
* **Architecture Check:** Look for separation of concern violations (e.g. business logic directly inside Next.js routing files or frontend UI views).
* **Silent Failure Detection:** Scan for bare `except:` or empty `catch` blocks that swallow runtime exceptions.
* **Hardcoded Secrets:** Ensure no API keys, tokens, or credentials are left as string literals in the code.
* **Production Readiness Score:** Rate code on a 1-10 scale across performance, safety, consistency, and error handling before production handoff.

---

## 5. Systematic Debugging & Issue Isolation
When resolving a bug, follow this workflow:
1. **Reproduction:** Write a minimal reproduction script or test case that reliably fails.
2. **Root Cause Analysis:** Trace back from the symptom to the exact file and line causing the failure.
3. **Focused Fix:** Write the minimal code necessary to resolve the defect. Avoid large refactors while fixing a bug.
4. **Validation:** Run the reproduction script to verify the fix works, and check that neighboring features are not regressed.

---

## 6. The Iron Law of Verification (Hard Gate)
Never mark a task complete or submit a pull request without running tests.
* **Automated Verification:** Execute the test suite and capture the stdout/stderr. Show the exact test command run.
* **Manual Verification:** If automated tests are not available, create a test script or perform browser/API execution, and present the raw output logs as evidence of success.
