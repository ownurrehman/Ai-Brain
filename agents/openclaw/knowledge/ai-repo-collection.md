# AI Agent Toolkit — Best Repository Collection
## Curated for Rank Ray / Tonic Physio Agent Deployments

> **Purpose:** Central index of production-grade tools, libraries, and frameworks discovered and validated by the team. Each entry includes the repo URL, what it does, when to use it, and how it integrates with our workflow.

---

## 🦊 BROWSERS & ANTI-DETECTION

### 1. CamoFox Browser (Server Wrapper)
- **Repo:** `https://github.com/redf0x1/camofox-browser`
- **Original Engine:** `https://github.com/daijro/camoufox`
- **Category:** Stealth / Anti-Detect Browser
- **What it does:**
  - Wraps Camoufox (a Firefox fork with C++ engine-level fingerprint spoofing)
  - Sends full accessibility trees to the AI agent (not bloated HTML)
  - No JavaScript-injection stealth — patches at binary level
  - Bypasses Cloudflare, Google ReCAPTCHA, DataDome, GeeTest, bot detection JS
  - Session persistence via cookies and localStorage
- **Use Cases:**
  - SERP scraping without rate limits or CAPTCHA interruptions
  - Competitor content audits where sites detect headless browsers
  - Logged-in workflow automation (persistent cookies)
  - Safe browsing of protected directories during security audits
- **When to deploy:** 
  - Whenever `browser_navigate` fails due to bot detection
  - Google/Amazon/Reddit/YouTube research at scale
  - Any client site behind Cloudflare Bot Fight Mode
- **How to run:**
  ```bash
  git clone https://github.com/redf0x1/camofox-browser.git
  cd camofox-browser && ./run.sh  # starts local server with accessibility API
  ```
- **Token Efficiency:** Accessibility DOM trees vs full HTML = ~90% token reduction
- **Notes:** Built by OpenClaw recommendation. Firefox fork, headless-friendly. Stable ref IDs for UI interaction.

---

## 🌐 PROXY & IP ROTATION

### 2. requests-ip-rotator
- **Repo:** `https://github.com/Ge0rg3/requests-ip-rotator`
- **Category:** IP Address Rotation / Rate-Limit Bypass
- **What it does:**
  - Creates AWS API Gateway proxies across multiple regions
  - Each request emerges from a different AWS egress IP
  - Subclasses `requests.adapters.HTTPAdapter` for transparent integration
  - Free tier: 1M requests/month, then ~$3/million
- **Use Cases:**
  - Bypassing IP-based API rate limits (WordPress REST, Google SERP, SERP APIs)
  - WordPress bulk post uploads after host-level rate blocking
  - Scraping targets that throttle individual IPs
- **When to deploy:**
  - After confirming direct POST is blocked by IP
  - When the server returns 429/403 from repeated requests
  - Bulk operations requiring many sequential API calls
- **How to use:**
  ```python
  from requests_ip_rotator import ApiGateway
  gateway = ApiGateway("https://target-site.com")
  gateway.start()
  session = requests.Session()
  session.mount("https://target-site.com", gateway)
  session.post(".../wp-json/wp/v2/posts", json=...)
  gateway.shutdown()
  ```
- **Requirements:** AWS Access Key + Secret (IAM permissions)
- **Risks:** Requires AWS credentials. Identifiable by `*.execute-api.*` header. Clean up gateways to avoid billing.

### 3. SwiftShadow (Free Proxy Rotator)
- **Repo:** `https://github.com/sachin-sankar/swiftshadow`
- **Category:** Free Proxy Library
- **What it does:**
  - Fetches and validates free public proxies automatically
  - Built-in proxy checker with latency + SSL checks
  - Rotates through verified fresh proxies
  - Proxy filtering by country, speed, anonymity level
- **Use Cases:**
  - Light scraping with rotating IPs (no AWS costs)
  - Quick prototype bypass before deploying AWS solution
- **When to deploy:**
  - Low-stakes scraping where proxy reliability is acceptable
  - Rapid smoke tests for IP-dependent features
- **Limitations:**
  - Free proxies are unreliable (high failure rates in our tests)
  - HTTPS over free proxy = frequent connection resets
  - Not suitable for authenticated APIs
- **How to use:**
  ```python
  import swiftshadow
  proxy = swiftshadow.Proxy()
  requests.get("https://...", proxies={"https": proxy.get()})
  ```

### 4. FireProx
- **Repo:** `https://github.com/ustayready/fireprox`
- **Category:** AWS API Gateway Proxy Manager
- **What it does:**
  - CLI tool to create/manage API Gateway pass-through proxies
  - Automatically rotates source IP with every request
  - Can bind to multiple AWS regions simultaneously
- **Use Cases:**
  - Web application penetration testing with rotating IPs
  - Red-teaming scenarios requiring IP obfuscation
  - Scalable proxy infrastructure without proxy subscriptions
- **Difference from #2:** FireProx is CLI-oriented; `requests-ip-rotator` is Python programmatic.
- **When to deploy:** CLI-first environments, Docker containers, CI/CD.

---

## 📝 WORDPRESS INTEGRATION

### 5. python-wordpress-xmlrpc
- **Repo:** `https://github.com/maxcutler/python-wordpress-xmlrpc`
- **Category:** WordPress XML-RPC Client
- **What it does:**
  - Python library for WordPress XML-RPC integration
  - Create, read, update, delete posts via `xmlrpc.php`
  - Often works when REST API is blocked or rate-limited
- **Use Cases:**
  - Bulk blog post uploads when REST API is throttled/firewalled
  - Older WordPress sites with limited REST access
  - Alternative path when `wp-json` endpoint is closed
- **When to deploy:**
  - Host blocks `/wp-json/` but leaves `/xmlrpc.php` open
  - REST authentication (Basic Auth / Application Password) fails
- **Limitations:**
  - XML-RPC can be disabled by security plugins
  - Returns binary/XML responses; needs parsing wrapper
  - Modern WordPress heavily favors REST; XML-RPC is legacy

---

## 🧠 MEMORY & KNOWLEDGE GRAPHS

### 6. Memtrace
- **Repo:** `https://github.com/syncable-dev/memtrace-public`
- **Website:** `https://memtrace.io`
- **Category:** Structural Memory Layer for Coding Agents
- **What it does:**
  - Local-first bi-temporal knowledge graph from AST (not vectors)
  - Indexes all code symbols with deterministic typed relationships
  - Tracks code evolution over time (git timeline)
  - Cross-service HTTP call graph mapping
  - Sub-8ms query latency, Rust-native binary
- **Use Cases:**
  - Large monorepo understanding for AI coding agents
  - Refactoring impact analysis (blast radius detection)
  - Cross-service API topology in microservice architectures
  - Finding dead code, architectural modules, hot paths
- **When to deploy:**
  - Codebases >50K LOC where naive vector RAG struggles
  - Refactoring large features across multiple files/services
  - Onboarding new developers with instant codebase Q&A
- **How to run:**
  ```bash
  npm install -g memtrace
  memtrace start
  # Opens localhost:3030 for graph visualization
  ```
- **Requirements:** Node.js 18+, Git, 8-16GB RAM for indexing
- **Not for:** Session/conversation memory — this is code intelligence, not chat history.

---

## 🤖 AI AGENT FRAMEWORKS

### 7. OpenClaw / ClawdBot (Existing System)
- **Current system the team runs on**
- Provides skill management, delegation, cron jobs, toolsets
- Integrates with WordPress via REST and browser automation
- Native MCP (Model Context Protocol) client support

---

## 📋 QUICK REFERENCE TABLE

| Tool | Type | Best For | Cost | Setup Time |
|------|------|----------|------|------------|
| CamoFox | Browser | Anti-detect browsing, audits | Free (open source) | 5 min |
| requests-ip-rotator | Proxy | AWS-scale IP rotation | Free 1M/mo, then cheap | 10 min |
| SwiftShadow | Proxy | Free quick IP rotation | Free | 2 min |
| python-wordpress-xmlrpc | WP Client | Bypass blocked REST API | Free | 5 min |
| Memtrace | Memory | Codebase knowledge graph | Free beta | 15 min |

---

## 🚀 DEPLOYMENT DECISION TREE

```
Need to scrape a site?
  → Is bot detection blocking?
     → YES: Use CamoFox
     → NO: Use standard browser tools

REST API blocked? (e.g., WordPress)
  → Try XML-RPC first (python-wordpress-xmlrpc)
  → If XML-RPC blocked → Use IP rotation (requests-ip-rotator)
  → If both blocked → Upload PHP script to server

Need to understand a large codebase?
  → Use Memtrace (local, agent-friendly)
```

---

*Last updated: April 30, 2026*
*Maintained by: Rank Ray AI Ops*
*For questions or additions, add new entries below with the same format.*
