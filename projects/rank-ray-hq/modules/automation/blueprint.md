# 🤖 RankRay: AUTOMATION MASTER SPECIFICATION

This is the definitive blueprint for the Rank Ray HQ Automations Module, as crafted by the Lead Architect. 

---

## **Phase 1: Context & Initialization**
Before writing any code, read the `.claude/AGENTS.md` file inside the master project folder to understand the architecture, established patterns, and what needs to be done. 

## **Phase 2: Core UI & Navigation Update**
The immediate focus is to develop a new "Automations" module.
1. Add an "Automations" parent item to the main sidebar.
2. Place this directly *above* the existing "SEO" module.
3. Create two submenu items under Automations:
   - Content Automation
   - Bulk Page Generator
*Note: Build this using a lightweight, modular architecture so we can easily plug in more automation submenus in the future.*

## **Phase 3: Develop "Content Automation" Plugin**
Build the framework and UI for a premium, agency-grade content automation tool with the following capabilities:
* **CMS Integration Scaffolding:** Build the connection logic to link external CMS platforms (WordPress, Shopify) via direct API credentials or OAuth. (Structure this so we can easily integrate a custom Rank Ray connection plugin in the future).
* **Content Gap Analysis:** Build the interface and logic placeholder for pulling SERP data and competitor analysis to automatically suggest high-value blog ideas.
* **Granular Generation Rules:** Create a comprehensive settings panel where the user can define strict SEO writing rules (e.g., target word counts, exact match/LSI keyword insertion, internal linking structure, and strict meta description constraints).
* **Auto-Publishing:** Include the API push logic to publish the generated content directly to the connected CMS.

## **Phase 4: Develop "Bulk Page Generator" Plugin**
Build the framework for a programmatic SEO bulk page creator.
* **Matrix Generation:** Allow the user to input arrays of variables (e.g., [City/Location] x [Service]) to generate hundreds of targeted landing pages automatically.
* **Custom Field Mapping:** The generator must dynamically map generated SEO content to standard CMS fields, Advanced Custom Fields (ACF), and specific SEO metadata fields.
* **Bulk Publishing:** Provide a streamlined interface to review the generated pages and push them in bulk directly to the connected website.

---

## **Technical Execution Rules**
- **Establishes Architecture First:** Keep the code lightweight and modular to prevent building a messy, monolithic file for both plugins. 
- **Definition of Awesome:** Granular settings, SERP analysis, and precision ACF field mapping are non-negotiable.
- **Phased Implementation:** Always start with the UI and structural setup before moving to the deep logic.

---

## **Technical Resources (Ai Brain Library)**
For the deep logic in Phases 3 and 4, use the technical patterns in:
- **WordPress Integration**: `Ai Brain/repositories/everything-claude-code/rules/wordpress/`
- **SEO Intelligence**: `Ai Brain/repositories/everything-claude-code/rules/seo/`
- **BullMQ Orchestration**: `Ai Brain/repositories/everything-claude-code/rules/nodejs-backend-patterns/`
