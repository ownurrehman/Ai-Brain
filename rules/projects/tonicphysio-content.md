# TonicPhysio Service Page Content Protocol

## What We Learned (2026-04-30)

### Template Structure
Service pages use ACF fields, NOT standard WordPress content. Key setup:
- **Template:** `services-pages.php`
- **Category:** `page_category: [325]` (Service Page)
- **Method:** Create draft → Set template + category → Update ACF fields via REST API
- **Auth:** User `Dan`, App Password `NMwZ 1LyJ YgbE fUjs pUYn 4SoZ`

### Critical Discovery: Content Duplication
**DO NOT** put bullet points inside paragraph fields when dedicated ACF bullet fields exist.

**Wrong:**
```
paragraph_2: "We offer programs including:\n• Program A\n• Program B"
why_choose_us_point_1: "Point 1"
```

**Right:**
```
paragraph_2: "We offer two specialized programs designed for your recovery."
why_choose_us_point_1: "Program A for specific condition"
why_choose_us_point_2: "Program B for another condition"
```

### Field Purposes
| Field Type | Purpose | Content Style |
|-----------|---------|--------------|
| `h1`, `h2`, `h3` | Headings | Include location + benefit keywords |
| `paragraph_*` | Narrative text | Flowing prose, NO bullet lists |
| `why_choose_us_point_*` | Bullet points | Short benefit statements |
| `solution_*` | Bullet points | Outcome-focused benefits |
| `faq_q*` / `faq_a*` | Q&A pairs | Natural language, 2-4 sentences |

### HTML Formatting in ACF Fields
Use these tags when needed:
- `<br>` for line breaks between paragraphs
- `<strong>` for emphasis/headers within text
- `<br><br>` for paragraph spacing

### Content Length Benchmarks
| Section | Minimum | Ideal | Status |
|---------|---------|-------|--------|
| paragraph_1 (Intro) | 400 chars | 600-800 chars | Must hook with pain point |
| paragraph_2 (Overview) | 300 chars | 600-1000 chars | Explain what service is |
| paragraph_for_h2_second | 400 chars | 800-1000 chars | Detail methods/approach |
| paragraph_for_h2_third | 500 chars | 1000-1500 chars | Process with steps |
| paragraph_for_h2_fourth | 400 chars | 800-1200 chars | Job-specific examples |
| paragraph_for_h3_first | 300 chars | 600-800 chars | CTA with phone + steps |
| FAQ answers | 150 chars | 200-250 chars | Complete but concise |
| **Total page** | 1,500 words | 2,000-2,500 words | Competitive benchmark |

### Content Quality Rules
1. **Pain points first** - Start with what the patient is experiencing
2. **Specific examples** - Mention job types, conditions, scenarios
3. **Credibility signals** - Experience, registration, direct billing
4. **Location keywords** - Milton, Halton, Oakville, Burlington
5. **No duplication** - Paragraphs describe, bullets list, FAQs answer
6. **Process steps** - Use numbered or labeled steps (Step 1, Step 2)
7. **CTA specifics** - Phone number, what to bring, what happens

### Update Workflow
1. Fetch current page via REST API
2. Audit content length and structure
3. Rewrite expanded content with proper field separation
4. Update via PUT with `{acf: {field: "content"}}`
5. Verify API response shows updated fields
6. Web fetch may be cached - check API for confirmation

### Example: Properly Structured Page
**paragraph_1:** "Workplace injuries can leave you in pain... [pain point] At Tonic Physio... [solution] We are a WSIB-registered clinic... [credibility]"

**why_choose_us_point_1:** "Direct WSIB billing with no out-of-pocket costs"
**why_choose_us_point_2:** "Personalized return-to-work programs"

**paragraph_for_h2_third:** "Your first visit follows a clear process: <br><br><strong>Step 1: Injury Review</strong><br>We discuss your incident...<br><br><strong>Step 2: Physical Examination</strong><br>We conduct thorough testing..."
