import asyncio
from playwright.async_api import async_playwright

async def check_listing(page, domain, query):
    print(f"Checking {domain}...")
    search_url = f"https://www.google.com/search?q=site:{domain}+{query}"
    try:
        await page.goto(search_url, wait_until="domcontentloaded", timeout=30000)
        content = await page.content()
        if "Khan Law" in content or "Khan LLP" in content:
            print(f"[FOUND] {domain}")
            return "Found"
        else:
            print(f"[NOT FOUND] {domain}")
            return "Not Found"
    except Exception as e:
        print(f"[ERROR] {domain}: {e}")
        return "Error"

async def main():
    domains = [
        "lso.ca", "lsrs.lso.ca", "lawyers.com", "lawyers.justia.com", 
        "findlaw.ca", "martindale.com", "cba.org", "superlawyers.ca", 
        "bestlawyers.com", "canadianlawyerdirectory.com", "lexpert.ca", 
        "legal500.com", "lawyerlocate.ca", "bbb.org", "oba.org", 
        "miltonchamber.ca", "bing.com"
    ]
    query = '"Khan Law" OR "Khan LLP"'
    
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()
        
        results = {}
        for domain in domains:
            results[domain] = await check_listing(page, domain, query)
            await asyncio.sleep(2) # Avoid rate limiting
            
        await browser.close()
        print("\nFinal Results:")
        for domain, status in results.items():
            print(f"{domain}: {status}")

if __name__ == "__main__":
    asyncio.run(main())
