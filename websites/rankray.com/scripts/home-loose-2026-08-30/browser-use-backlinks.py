#!/usr/bin/env python3
"""
Browser-Use Backlink Building Tool
Uses AI-driven browser automation to submit client sites to free web directories.
Designed for Rank Ray clients: rankray.com, tonicphysio.com, coinsfera.com, etc.
"""

import asyncio
import os
import json
from datetime import datetime
from browser_use import Agent, BrowserProfile
from browser_use.llm import ChatOpenAI

OLLAMA_BASE_URL = "https://ollama.com/v1"
OLLAMA_API_KEY = os.environ.get("OLLAMA_API_KEY", "")

llm = ChatOpenAI(
    model="glm-5.2",
    base_url=OLLAMA_BASE_URL,
    api_key=OLLAMA_API_KEY,
)

browser_profile = BrowserProfile(
    headless=True,
    viewport={"width": 1280, "height": 800},
    keep_alive=False,
)

# Client site data for directory submissions
CLIENT_SITES = {
    "rankray": {
        "url": "https://rankray.com",
        "title": "Rank Ray - SEO Agency",
        "description": "Rank Ray is a data-driven SEO agency helping businesses rank higher on Google with technical SEO, content optimization, and link building services.",
        "keywords": "SEO agency, digital marketing, search engine optimization",
        "category": "Marketing / SEO",
        "email": "info@rankray.com",
    },
    "tonicphysio": {
        "url": "https://tonicphysio.com",
        "title": "Tonic Physio - Physiotherapy Clinic in Milton",
        "description": "Tonic Physio offers expert physiotherapy services in Milton, ON including sports rehab, shockwave therapy, dry needling, and custom bracing.",
        "keywords": "physiotherapy, physio clinic, sports rehab, Milton",
        "category": "Health / Medical",
        "email": "info@tonicphysio.com",
    },
    "coinsfera": {
        "url": "https://www.coinsfera.com",
        "title": "Coinsfera - Buy Bitcoin in Istanbul",
        "description": "Coinsfera is a leading crypto OTC shop in Istanbul, Turkey. Buy and sell Bitcoin, USDT, and other cryptocurrencies with cash or bank transfer.",
        "keywords": "buy bitcoin istanbul, crypto exchange, cryptocurrency",
        "category": "Finance / Cryptocurrency",
        "email": "info@coinsfera.com",
    },
}

# Free directory sites that accept submissions
DIRECTORY_SITES = [
    {
        "name": "Jasmine Directory",
        "url": "https://www.jasminedirectory.com",
        "submit_path": "/submit",
        "type": "free",
        "notes": "Free submission, requires title, URL, description, category, email",
    },
    {
        "name": "Directory Critic",
        "url": "https://www.directorycritic.com",
        "submit_path": "/submit-directory.html",
        "type": "free",
        "notes": "Free directory list + submission",
    },
    {
        "name": "Somuch",
        "url": "https://www.somuch.com",
        "submit_path": "/submit-site/",
        "type": "free",
        "notes": "Free submission, requires title, URL, description",
    },
    {
        "name": "ABusinessDirectory",
        "url": "https://www.abusinessdirectory.com",
        "submit_path": "/submit.php",
        "type": "free",
        "notes": "Free business directory submission",
    },
    {
        "name": "Siteswebdirectory",
        "url": "https://www.siteswebdirectory.com",
        "submit_path": "/submit.php",
        "type": "free",
        "notes": "Free web directory submission",
    },
    {
        "name": "Vlib",
        "url": "https://vlib.org",
        "submit_path": "/submit",
        "type": "free",
        "notes": "Free directory, requires category selection",
    },
    {
        "name": "Best of the Web",
        "url": "https://www.botw.org",
        "submit_path": "/submit",
        "type": "paid",
        "notes": "Paid but high authority directory",
    },
]


async def submit_to_directory(client_key, directory, max_steps=25):
    """Use browser-use agent to submit a client site to a directory"""
    client = CLIENT_SITES[client_key]
    
    task = f"""
You are building backlinks for a client website. Your task is to submit the client's site to a web directory.

CLIENT INFORMATION:
- Site URL: {client['url']}
- Title: {client['title']}
- Description: {client['description']}
- Keywords: {client['keywords']}
- Category: {client['category']}
- Email: {client['email']}

DIRECTORY INFORMATION:
- Directory Name: {directory['name']}
- Directory URL: {directory['url']}
- Submission Page: {directory['url']}{directory['submit_path']}

INSTRUCTIONS:
1. Navigate to the directory submission page: {directory['url']}{directory['submit_path']}
2. Look for the submission form
3. If there is a form, fill it out with the client information above
4. Use the client's URL, title, description, keywords, category, and email as needed
5. If the form requires a reciprocal link, skip that directory
6. If the form requires payment, do NOT submit — just report that it requires payment
7. If you encounter a CAPTCHA, report that a CAPTCHA was found and you cannot proceed
8. If submission is successful, report the success message
9. If submission fails, report the error

IMPORTANT: Do NOT submit if the directory requires payment or reciprocal link. 
Only submit to FREE directories.

Report the outcome clearly: SUCCESS, PAYMENT_REQUIRED, CAPTCHA_FOUND, RECIPROCAL_REQUIRED, or ERROR with details.
"""
    
    print(f"\n{'='*40}")
    print(f"Submitting {client['title']} to {directory['name']}")
    print(f"URL: {directory['url']}{directory['submit_path']}")
    print(f"{'='*40}")
    
    agent = Agent(
        task=task,
        llm=llm,
        browser_profile=browser_profile,
        use_vision=False,
    )
    
    result = await agent.run(max_steps=max_steps)
    
    # Extract result
    outcome = "UNKNOWN"
    for item in result:
        if hasattr(item, 'is_done') and item.is_done:
            outcome = item.extracted_content or "No content"
    
    print(f"\nOutcome: {outcome}")
    return {"directory": directory['name'], "client": client_key, "outcome": outcome}


async def main():
    print("=" * 60)
    print("  Browser-Use Backlink Building Tool")
    print("  Model: glm-5.2 via Ollama Cloud | Vision: OFF")
    print("=" * 60)
    
    if not OLLAMA_API_KEY:
        print("ERROR: OLLAMA_API_KEY not set!")
        return

    # Select client (default: rankray)
    client_key = os.environ.get("CLIENT", "rankray")
    if client_key not in CLIENT_SITES:
        print(f"Unknown client: {client_key}. Available: {list(CLIENT_SITES.keys())}")
        return
    
    print(f"\nClient: {client_key} ({CLIENT_SITES[client_key]['url']})")
    print(f"Directories to try: {len(DIRECTORY_SITES)}")
    
    results = []
    
    for directory in DIRECTORY_SITES:
        try:
            result = await submit_to_directory(client_key, directory, max_steps=25)
            results.append(result)
        except Exception as e:
            print(f"Error with {directory['name']}: {e}")
            results.append({"directory": directory['name'], "client": client_key, "outcome": f"ERROR: {e}"})
    
    # Summary
    print("\n" + "=" * 60)
    print("  SUBMISSION SUMMARY")
    print("=" * 60)
    for r in results:
        status = "SUCCESS" if "SUCCESS" in str(r['outcome']).upper() else \
                "PAYMENT" if "PAYMENT" in str(r['outcome']).upper() else \
                "CAPTCHA" if "CAPTCHA" in str(r['outcome']).upper() else \
                "ERROR" if "ERROR" in str(r['outcome']).upper() else "UNKNOWN"
        print(f"  {r['directory']:25s} -> {status}")
    
    # Save results
    report_path = f"/Users/sheikhown/backlink-report-{datetime.now().strftime('%Y%m%d')}.json"
    with open(report_path, 'w') as f:
        json.dump(results, f, indent=2)
    print(f"\nReport saved: {report_path}")


if __name__ == "__main__":
    asyncio.run(main())