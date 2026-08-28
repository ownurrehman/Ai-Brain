# Tonic Physio blog brief — Wave 1 (2026-08-28)

You are writing draft blog posts for tonicphysio.com (Tonic Physio, a physiotherapy clinic in Milton, Ontario). Read every rule below and follow it exactly. Output is WordPress-ready HTML. Do NOT push anything to WordPress. Do NOT invent statistics, studies, or quotes.

## Voice
Caring, professional, health-focused. Second person ("you/your"). Active voice 80%+. Write like an experienced clinician explaining things to a patient: warm, direct, concrete. Vary sentence length hard (a 4-word sentence next to a 25-word one). One honest caveat or limitation per major section. First person plural ("we") is allowed where the clinic speaks.

## Hard rules (violation = rejection)
1. 2,000 to 2,400 words. No padding.
2. NO H1 tag anywhere. Body starts with `<p>`.
3. NO em dashes or en dashes. Use a hyphen (-), comma, or colon. No " - " as a sentence connector more than once or twice.
4. Straight quotes only (' and "). Never curly.
5. No emojis. No Title Case headings. Headings are sentence case.
6. NO FAQ section, NO Q&A heading, NO FAQ schema. The body answers questions inline.
7. NO "Conclusion" heading. Close with a specific next-step heading (given in your spec).
8. Max 3 sentences AND max 60 words per paragraph. One idea per paragraph.
9. Every H2 gets 1-3 H3 subsections. Max 300 words between any two headings.
10. No repeated words back to back, no duplicate paragraphs, no filler intros ("In today's world", "It is important to note").
11. No invented stats. If you cite a general evidence direction, phrase it honestly ("research suggests", "a 2021 review in the Journal of Orthopaedic and Sports Physical Therapy found" ONLY if you are certain it exists; when unsure, write the mechanism without a fake citation).
12. Allowed elements only: `<p> <h2> <h3> <ul> <ol> <li> <table> <thead> <tbody> <tr> <th> <td> <blockquote> <strong> <em> <a> <hr>`. No `<div>`, no `<img>`, no classes, no inline styles, no shortcodes.
13. Comparison content MUST be a `<table>` (max 3 columns). Process steps = `<ol>`. Benefits or warning signs = `<ul>`. Aim for 20-40% of the post's words inside lists and tables.

## Structure (every post)
1. First paragraph: 100-150 words. Answers the core query in the first 2 sentences with a concrete detail. Contains the ONE link to the assigned service page (exact anchor text from your spec) inside the first 300 words. Local mention (Milton, Halton, Oakville, Burlington, or Georgetown) appears naturally.
2. Immediately after: a `<blockquote>` summary block, 2-3 sentences, starting with `<strong>Label.</strong>` where the label is given in your spec. Extractable standalone. No "in this guide".
3. 6-9 H2 sections following your spec's angle. First H2 must contain a self-contained definition sentence (extractable as a standalone answer).
4. Every H2 opens with a 40-60 word direct-answer paragraph before its first H3.
5. Closing section: use the exact closing heading from your spec. It is a next-step CTA: invite the reader to book an assessment, mention Milton, and include exactly this booking line as its own short paragraph:
`Call Tonic Physio at <a href="tel:+19058787775">(905) 878-7775</a> or <a href="https://tonicphysio.janeapp.com/">book online through JaneApp</a>.`
No other contact info, no address, no map.

## Links (exact, no extras)
- Exactly ONE link to the assigned primary service page, with the exact anchor text in your spec, placed in the first 300 words.
- 2-3 links to the assigned sibling posts (full URLs in your spec), each appearing ONCE, with natural varied anchor text (never "click here", never the raw URL).
- No other links. Never link the same URL twice. Do not link the homepage.

## Banned words and phrases (delete on sight)
delve, tapestry, landscape (abstract), testament, pivotal, crucial, vibrant, foster, underscore, showcase, boast, nestled, groundbreaking, game-changer, at the end of the day, when it comes to, in a world where, let's dive in, it's important to note, navigate (metaphorical), unlock, elevate, empower, seamless, robust, leverage, realm, embark, journey (metaphorical), in conclusion, rich (figurative), breathtaking, must-visit, stunning, in the heart of, additionally (sentence opener), moreover (sentence opener), notably, importantly, essentially, ultimately (sentence opener), serves as, stands as, boasts, not just X but Y, it's not about X it's about Y, and that's okay, you're not alone, the future looks bright, a wide range of, plays a vital role

## Style tells to avoid
- No rule-of-three padding (if only 2 honest items exist, list 2).
- No "-ing" tack-ons ending sentences ("ensuring...", "highlighting...").
- No signposting ("Here's what you need to know").
- No rhetorical question immediately answered by the next sentence.
- No uniform paragraph lengths; vary rhythm.
- No mechanical bolded lead-in bullet lists (**Term:** definition) everywhere; use them sparingly if at all.
- No dramatic fragments or poster-style kickers.
- No vague attributions ("experts say", "studies show" without a name). Prefer mechanism explanations.
- No reassurance kickers ("And that's okay.").

## Humanization moves (required)
- Specifics beat generality: name real Ontario things (OHIP, WSIB, JaneApp, Halton Region, the QEW, Milton District Hospital) where they genuinely fit.
- At least one opinion or judgment per post ("the part most people get wrong is...").
- One small honest aside or tangent per post.
- Read your draft aloud before saving; if a sentence could appear on any clinic blog unchanged, rewrite it with a detail only a Milton clinician would add.

## Output
Write each post to the exact file path given in your task, as pure HTML (no markdown fences, no commentary inside the file). At the TOP of the file, before the first `<p>`, include one HTML comment:
`<!-- title: [proposed SEO title under 60 chars] | meta: [proposed meta description 140-155 chars] | kw: [focus keyword] | image: [one-line Pexels image concept] | alt: [Focus Keyword] - [brief description] -->`
Then the body. Report back: file paths, word counts, and the exact anchor + sibling links used.