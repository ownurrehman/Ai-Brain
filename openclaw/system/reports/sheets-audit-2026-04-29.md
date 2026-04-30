# Google Sheets Audit Report - April 29, 2026

## 1. Khan LLP Citation Findings Sheet
- **ID:** 1EnYut8v6-FO4PtPD7QibhJrvOcvbvXxgXVjpeaGKmmQ
- **Title:** Khan LLP Citation Findings
- **Sheet:** Sheet1 (1003 rows x 935 cols allocated)
- **Data rows:** 233 citation entries
- **Access:** READ/WRITE confirmed

### Existing Columns (A-J):
A: Directory Name | B: URL | C: Category | D: Priority | E: Found | F: NAP | G: Notes | H: Listing Type | I: Email Used | J: Status

### New Column Added (K):
- **K: "Free Account Creation"** - Binary Free/Paid classification derived from Listing Type column

### Classification Logic Applied:
- "Free", "Free/Paid", "Free + Paid", "Free + Paid tiers", "Free / Community", "Free resource", "Free (Clio users)" → Free
- "$X/yr", "Paid", "Paid Membership", "Paid Lead-Gen", "Paid / Editorial", "Membership", "Editorial", "Nomination", "Relationship-based", "Referral" → Paid

### Results:
| Category | Count | % |
|----------|-------|---|
| Free     | 186   | 79.8% |
| Paid     | 47    | 20.2% |
| Total    | 233   | 100% |

### Observations:
- 79.8% of citation targets allow free account creation — good targeting
- High-priority legal directories (Justia, Lawyers.com, Martindale, HG.org) all have free tiers
- Paid-only targets are mainly chambers of commerce ($300-500/yr), premium legal directories (Super Lawyers, Best Lawyers, Lexpert), and data aggregators (Neustar Localeze, Data Axle)
- Some duplicate entries exist (e.g., Milton Chamber appears in rows 9 and 54, Justia in rows 7 and 56)

---

## 2. Rank Ray Lead Tracker Sheet
- **ID:** 11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4
- **Title:** Rank Ray Lead Tracker
- **Sheet:** Sheet1 (999 rows x 26 cols allocated)
- **Access:** READ/WRITE confirmed

### Current Structure (12 headers):
A: Company Name | B: URL | C: Industry | D: Location | E: Contact | F: GEO Pain Point | G: Initial Outreach Date | H: Current Status | I: Sequence Step | J: Last Touchpoint | K: Personalization Note | L: Lead Grade

### Lead Count: 16 entries

### Data Quality Issues Detected:
1. **Misaligned columns (rows 2-9):** Early entries (Al-Mazrouei through Gul Ahmed) have data in wrong columns — Industry field contains GEO pain point description, Location/Contact fields contain status values. These 8 rows need realignment.
2. **Missing fields:** Rows 2-9 missing URL, Location, Contact phone, Initial Outreach Date, Sequence Step, Last Touchpoint, Personalization Note, Lead Grade
3. **Properly formatted rows (10-17):** Legacy Plumbing through Rank Ray Official have correct column alignment
4. **Status distribution (well-formatted rows only):** 5 "Pending Email 1" and 1 "Email 1 Sent"

### Readiness for New Leads: **PARTIALLY READY**
- The header structure is solid and covers all needed fields for a lead tracker
- 8 legacy rows (2-9) need repair before new leads are added
- Rows 10+ are properly formatted and the pipeline is working
- Sheet is ready for new lead updates once legacy rows are cleaned up
