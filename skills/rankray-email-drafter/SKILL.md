---
name: rankray-email-drafter
description: "Daily lead email drafting pipeline for Rank Ray outbound sales. Use when: (1) Processing new GMB leads from Google Sheets, (2) Visiting lead websites to identify SEO pain points, (3) Drafting personalized cold emails under 150 words, (4) Saving email drafts to Google Sheets 'Email Drafts' tab. Filters A/B graded leads only, skips C/D. Reports to #claw-emailer. NEVER sends emails automatically — drafts only."
---

# Rank Ray Daily Lead Email Drafter

## Prerequisites

- Google Sheets service account credentials at `~/.config/google-sheets/credentials.json`
- `gspread` Python package: `pip install gspread google-auth`
- Spreadsheet ID configured in environment: `LEADS_SPREADSHEET_ID`

## Workflow

### Step 1: Read Leads from Google Sheet

```python
import gspread
from google.oauth2.service_account import Credentials

scopes = ['https://www.googleapis.com/auth/spreadsheets']
creds = Credentials.from_service_account_file(
    '~/.config/google-sheets/credentials.json', scopes=scopes
)
client = gspread.authorize(creds)

sheet = client.open_by_key(os.environ['LEADS_SPREADSHEET_ID'])
leads_worksheet = sheet.worksheet('Leads')
leads = leads_worksheet.get_all_records()
```

### Step 2: Filter A/B Grade Leads

Skip C and D grade leads. Only process A and B grades.

### Step 3: Visit Website & Identify Pain Points

For each lead:
1. Visit their website URL using browser tool
2. Check for these SEO pain points:
   - **No blog** → No `/blog` path, no recent posts
   - **Slow site** → Page load time > 3s
   - **No schema** → Missing structured data / JSON-LD
   - **Thin content** → Service pages with < 300 words
   - **No GMB** → No Google Business Profile linked
3. Document 2-3 specific pain points per lead

### Step 4: Draft Personalized Email

Template structure (under 150 words):

```
Hi [Company] team,

I was looking at your website and noticed a few quick SEO wins:

1. [Specific pain point 1]
2. [Specific pain point 2]
3. [Specific pain point 3 — if applicable]

Rank Ray can fix these in under 48 hours. Want to see a free audit?

Best,
Rank Ray Team
```

### Step 5: Save to "Email Drafts" Tab

Columns: lead_name, email, subject, body, pain_points

```python
drafts_worksheet = sheet.worksheet('Email Drafts')
for draft in drafts:
    drafts_worksheet.append_row([
        draft['lead_name'],
        draft['email'],
        draft['subject'],
        draft['body'],
        ', '.join(draft['pain_points'])
    ])
```

## Report Format

Post to #claw-emailer:

```
📊 Daily Lead Email Draft Report
Date: [YYYY-MM-DD]

• Leads processed: [N]
• Emails drafted: [N]  
• Avg quality score: [N%]
• Sheet: [link]

⚠️ DRAFTS ONLY — pending human approval
🚫 No emails sent automatically
```

## Red Lines

- **NEVER** send emails automatically
- **NEVER** include sensitive data in reports
- Always confirm drafts are saved before reporting completion
- If Google Sheets API fails, save drafts locally and notify user
