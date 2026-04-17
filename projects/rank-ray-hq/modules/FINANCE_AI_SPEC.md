# Finance AI Enhancement Spec

Inspired by TaxHacker (github.com/vas3k/TaxHacker). Adapted for RankRay HQ's NestJS + React architecture.

---

## Features to Add

### Phase 1: Drag-and-Drop Upload (UI only)

Status: ready to implement

Add a document-level drop zone to the Finance module that accepts receipts, invoices, and expense documents.

Pattern (from TaxHacker `screen-drop-area.tsx`):
- Document-level `dragenter`/`dragover`/`dragleave`/`drop` listeners
- `dragCounter` ref to handle nested element events
- Full-screen overlay when dragging ("Drop receipt or invoice here")
- Accepted types: PDF, JPEG, PNG, WebP
- On drop: file goes to an "unsorted" queue or directly creates an expense draft

Implementation:
- Frontend component: `rankray-hq-frontend/src/modules/finance/components/FinanceDropZone.tsx`
- Wrap Finance module layout with drop zone
- On drop: upload file via `/finance/uploads` endpoint → show in expense creation form

### Phase 2: AI Receipt Extraction

Status: planned (requires AI provider API key)

Use the workspace AI provider (OpenAI/Anthropic/Gemini — already configured in SEO AI settings) to extract structured data from uploaded receipts.

Pattern (from TaxHacker `ai/analyze.ts`):
- Convert PDF pages to images (pdf2pic or similar)
- Send images as base64 to LLM with structured output schema
- Extract: merchant, amount, currency, date, category, line items, VAT
- Cache parsed result on the file record
- Pre-fill expense creation form with extracted data

Implementation:
- Backend service: `rankray-hq-backend/src/finance/services/receipt-ai.service.ts`
- Uses existing AI provider config from `rankray-hq-backend/src/seo/ai/`
- JSON schema for extraction: amount, currency, date, merchant, description, category, items[]
- Prompt template with workspace categories for better categorization

### Phase 3: Multi-Provider LLM Fallback

Status: planned

Pattern (from TaxHacker `llmProvider.ts`):
- Configurable provider priority list (e.g. "openai,anthropic,gemini")
- Try providers in order until one succeeds
- Already have provider infrastructure in SEO AI module

### Phase 4: Currency Conversion

Status: planned

Pattern (from TaxHacker `api/currency/route.ts`):
- Historic exchange rate lookup
- `convertedTotal` / `convertedCurrencyCode` on expense/invoice records
- Workspace base currency as target
- Visual converter widget in expense/invoice forms

---

## Implementation Priority

1. Drop zone UI (low risk, high UX impact)
2. AI receipt extraction (medium risk, high value — needs API key)
3. Multi-provider fallback (low risk, uses existing AI infra)
4. Currency conversion (medium risk, schema changes needed)

---

## Key Patterns from TaxHacker

### Drop Zone Pattern
```
- Document body listeners (not just a single div)
- dragCounter ref prevents flickering on nested elements
- Full-screen overlay with animation
- Context-aware routing (unsorted vs specific record)
```

### AI Extraction Pattern
```
- Fields with llm_prompt → JSON schema → structured output
- Multi-page PDF → image previews → base64 → LLM
- Cache parsed result on file record
- Pre-fill form from extraction
```

### Settings Pattern
```
- Key-value Setting model (userId, code, value)
- Categories with colors and llm_prompt
- Configurable field visibility
- Provider priority with drag-and-drop reorder
```

---

## Files to Create

Frontend:
- `rankray-hq-frontend/src/modules/finance/components/FinanceDropZone.tsx`
- `rankray-hq-frontend/src/modules/finance/components/ReceiptPreview.tsx`

Backend:
- `rankray-hq-backend/src/finance/services/receipt-ai.service.ts`
- `rankray-hq-backend/src/finance/dto/receipt-upload.dto.ts`

---

## Non-Goals

- Do not replace the existing expense/invoice creation flow
- Do not add a separate transaction model (use existing Expense/Invoice)
- Do not add payment processing (Stripe) — RankRay is B2B invoicing, not personal finance
- Do not add backup/restore (out of scope for finance)
