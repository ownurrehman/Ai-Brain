# IMAGE VERIFICATION RULE — CRITICAL FOR ALL CONTENT

**Effective:** 2026-04-21  
**Status:** MANDATORY — ALL AGENTS MUST FOLLOW  
**Issue:** Irrelevant images uploaded (Coca-Cola, tractor in SEO article)

---

## 🚨 CRITICAL FAILURE IDENTIFIED

**Problem:** Images were uploaded with correct filenames/alt text, but the **actual image content was irrelevant**:
- Coca-Cola image in semantic SEO article
- Tractor image in SEO services content
- Random business stock photos unrelated to topic

**Root Cause:** Used hardcoded Pexels fallback URLs without verifying what the images actually show.

---

## ✅ NEW IMAGE SOURCING WORKFLOW (MANDATORY)

### Step 1: Source Images with Context Verification
```python
# WRONG - What was done before
PEXELS_FALLBACKS = {
    "analytics": "https://images.pexels.com/photos/326503/pexels-photo-326503.jpeg",
    # ❌ No verification of actual image content
}

# CORRECT - What must be done now
# 1. Search for image with specific query
# 2. Download image
# 3. VERIFY image matches topic (manual or AI vision check)
# 4. ONLY THEN upload to WordPress
```

### Step 2: Image Verification Checklist (BEFORE Upload)
- [ ] **Download image first** to /tmp/
- [ ] **Open and verify** the image actually shows what the filename suggests
- [ ] **Check for:**
  - ❌ Brand logos (Coca-Cola, Nike, etc.) — REJECT unless topic is about that brand
  - ❌ Irrelevant objects (tractors, food, etc.) — REJECT
  - ❌ Wrong context (party photos for business topics) — REJECT
  - ✅ Relevant business/tech/dashboard imagery — ACCEPT
  - ✅ Abstract concepts (charts, graphs, workflows) — ACCEPT
  - ✅ Industry-specific imagery — ACCEPT

### Step 3: Use Firecrawl Search (NOT Hardcoded URLs)
```bash
# CORRECT WORKFLOW
# 1. Search Pexels/Unsplash via Firecrawl
firecrawl_search "semantic SEO dashboard analytics" site:pexels.com

# 2. Get direct image URL from results
# 3. Download and VERIFY
# 4. Upload to WordPress
```

### Step 4: Fallback Strategy (If Firecrawl Fails)
If Firecrawl image search fails:
1. **Manual curation required** — Don't auto-upload
2. **Use Unsplash source URLs directly** (they're more reliable than Pexels fallbacks)
3. **Minimum verification:** Open image URL in browser before uploading

---

## 📋 IMAGE RELEVANCE RULES

### ACCEPTABLE Images for SEO/Business Content:
- ✅ Analytics dashboards
- ✅ Computer/screens with code/data
- ✅ Abstract technology backgrounds
- ✅ Charts, graphs, data visualization
- ✅ Office/workspace environments
- ✅ Network/connection diagrams
- ✅ Search/ magnifying glass icons
- ✅ Content writing/typing hands

### UNACCEPTABLE Images (ALWAYS REJECT):
- ❌ Brand logos (Coca-Cola, Pepsi, Nike, Apple, etc.)
- ❌ Random objects (tractors, food, animals, sports)
- ❌ Party/celebration photos
- ❌ Fashion/lifestyle unrelated to topic
- ❌ Political/religious imagery
- ❌ Celebrity faces

---

## 🔧 CORRECTED IMAGE SOURCING SCRIPT

```python
#!/usr/bin/env python3
"""
Image Sourcing with Verification — Rank Ray Standard
"""

import requests
from pathlib import Path
import subprocess

def search_and_verify_images(queries: list, output_dir: str):
    """
    Search, download, and VERIFY images before upload
    """
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)
    
    verified_images = []
    
    for query in queries:
        print(f"Searching: {query}")
        
        # Use Firecrawl to search Pexels
        search_results = firecrawl_search(f"{query} site:pexels.com")
        
        if not search_results:
            print(f"  ✗ No results for {query}")
            continue
        
        # Get first image URL
        image_url = extract_image_url(search_results[0])
        
        # Download
        filename = f"{query.replace(' ', '-')}.jpg"
        download_path = output_path / filename
        download_image(image_url, download_path)
        
        # CRITICAL: VERIFY IMAGE
        print(f"  ⚠️ VERIFY: Open {download_path} and confirm it matches '{query}'")
        print(f"  URL: {image_url}")
        
        # Manual verification required (or use AI vision)
        verified = input("  Does this image match the topic? (y/n): ")
        
        if verified.lower() == 'y':
            verified_images.append({
                "filename": filename,
                "path": str(download_path),
                "query": query,
                "url": image_url
            })
        else:
            print(f"  ✗ Rejected - finding alternative...")
            # Try next result
    
    return verified_images

def firecrawl_search(query: str):
    """Search using Firecrawl"""
    # Implementation depends on Firecrawl API
    pass

def download_image(url: str, path: Path):
    """Download image to path"""
    response = requests.get(url, stream=True)
    with open(path, 'wb') as f:
        for chunk in response.iter_content(8192):
            f.write(chunk)
```

---

## 🎯 PRE-UPLOAD CHECKLIST (MANDATORY)

Before uploading ANY image to WordPress:

- [ ] **Downloaded to /tmp/ first**
- [ ] **Opened and visually verified** (image matches topic)
- [ ] **No brand logos** (unless topic is about that brand)
- [ ] **No irrelevant objects** (tractors, food, random items)
- [ ] **Filename matches topic** (keyword-based)
- [ ] **Alt text prepared** (descriptive with keywords)
- [ ] **Image orientation correct** (landscape for Rank Ray blogs)
- [ ] **File size optimized** (WebP, <500KB preferred)

---

## 📖 EXAMPLES

### ❌ WRONG (What Happened):
```
Query: "semantic SEO analytics"
Downloaded: https://images.pexels.com/photos/326503/...
Result: Coca-Cola bottles (completely irrelevant!)
Uploaded: YES (without verification)
```

### ✅ CORRECT (New Standard):
```
Query: "semantic SEO analytics"
Search: Firecrawl "semantic SEO dashboard site:pexels.com"
Downloaded: https://images.pexels.com/photos/326514/...
Verified: Opens image, confirms it shows analytics dashboard
Result: Dashboard with charts/graphs (relevant!)
Uploaded: ONLY after manual verification
```

---

## 🚨 ENFORCEMENT

**ALL agents (main, enigma, chronos, researcher, subagents) MUST:**
1. Download images BEFORE uploading
2. Verify image content matches topic
3. Reject irrelevant images (brands, random objects)
4. Use Firecrawl search (not hardcoded fallback URLs)
5. Log verification in publishing checklist

**Files Updated:**
- `IMAGE-VERIFICATION-RULE.md` (NEW — this file)
- `MASTER-RULES.md` — Image sourcing section updated
- `CONTENT-QUALITY-RULES.md` — Image relevance added
- `agents/enigma.md` — Image verification workflow
- `agents/chronos.md` — Image verification workflow
- `agents/researcher.md` — Image verification workflow

---

## 🔧 IMMEDIATE FIX FOR POST 19812

**Action Required:**
1. Identify which images are irrelevant (Coca-Cola, tractor, etc.)
2. Find replacement images with proper verification
3. Replace images in post
4. Update media library (delete irrelevant images)

**Estimated Time:** 15-20 minutes for full replacement

---

**Last Updated:** 2026-04-21  
**Version:** 1.0  
**Status:** ACTIVE — MANDATORY COMPLIANCE
