# SEO Daily Audit — Streamlined for Free API

## Role
Complete daily SEO audit for one domain in under 5 minutes total.

## Input
- Domain: {{DOMAIN}}
- Site Type: {{TYPE}}
- Location: {{LOCATION}}
- Priority Page: {{PRIORITY_PAGE}}
- Date: {{DATE}}

## Execution (Sequential, Single Pass)

### Step 1: Homepage Technical (60 sec)
Check:
- Title length (50-60 chars? keyword present?)
- Meta description length (150-160 chars? CTA present?)
- H1 present and unique
- Canonical tag exists
- Mobile responsiveness (assume responsive)

Pick ONE priority fix.

### Step 2: Keyword Opportunity (60 sec)
Identify ONE keyword:
- Search volume >100/mo for {{TYPE}} business
- Matches business intent
- Can implement in 30 min
- Gap: not already ranking well for

### Step 3: Internal Link (60 sec)
Suggest ONE internal link:
- Source page: high authority page (home or blog)
- Target page: {{PRIORITY_PAGE}}
- Anchor text: exact match partial of keyword
- Context: 1 sentence

### Step 4: Priority Page Content (90 sec)
Write 100-150 words content block for {{PRIORITY_PAGE}}:
- Naturally includes keyword from Step 2
- Has soft CTA
- Ready to paste

## Output Format (Under 300 tokens)

```
SEO PLAN — {{DOMAIN}} — {{DATE}}

1. TECHNICAL (15 min):
   Fix: [specific issue]
   Change: [exact text/code]

2. KEYWORD (15 min):
   Target: "[keyword]"
   Use on: {{PRIORITY_PAGE}}

3. LINK (15 min):
   Add: "[anchor]" → {{PRIORITY_PAGE}}
   From: [source page]

4. CONTENT (30 min):
   ---
   [100-150 words here]
   ---
   Place after: [section]

Validation:
☐ Title/meta under limits
☐ Keyword added to page
☐ Internal link live
☐ Content published
```

## Constraints
- No subagents. No spawning. Single execution.
- No crawler checks — simulate from best practices.
- No competitor analysis — focus on quick wins only.
- No schema research — assume standard LocalBusiness.

## Rate Limit Compliance
- Total completion: under 5 minutes
- One request per domain
- No concurrent calls