import requests
import json

SITES = {
    "rankray": "https://rankray.com",
}

# Fields that are considered mandatory for a "complete" service page
MANDATORY_FIELDS = [
    "location", "state", "country", "select_service", "location_h1", "below_h1",
    "first_paragraph", "below_first_p_long_description", "first_h2", "first_h2_paragraph",
    "second_h2_services", "small_services_paragraph", "seo_service_description",
    "web_dev_description", "smm_description", "ppc_description",
    "ai_automation_description", "content_writing_description", "video_production_description",
    "cro_description", "email_marketing", "why_choose_us_heading", "why_choose_us_paragraph",
    "why_choose_us_paragraph_2", "why_choose_us_paragraph_3", "why_choose_first_service_heading",
    "why_choose_first_service_paragraph", "why_choose_second_service_heading",
    "why_choose_second_service_paragraph", "why_choose_third_service_heading",
    "why_choose_third_service_paragraph", "why_choose_fourth_service_heading",
    "why_choose_fourth_service_paragraph", "why_choose_fifth_service_heading",
    "why_choose_fifth_service_paragraph", "why_choose_sixth_service_heading",
    "why_choose_sixth_service_paragraph", "rank_ray_awards_heading_h3",
    "rank_ray_awards_paragraph", "last_form_heading"
]

def check_site(site_name, base_url):
    print(f"Checking {site_name}...")
    pages = []
    url = f"{base_url}/wp-json/wp/v2/location-page?per_page=100"
    
    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        pages = response.json()
    except Exception as e:
        print(f"Error fetching pages for {site_name}: {e}")
        return

    results = []
    for page in pages:
        # Check if it has service-type assigned
        if not page.get("service-type"):
            continue
            
        acf = page.get("acf", {})
        missing = [field for field in MANDATORY_FIELDS if not acf.get(field)]
        
        if missing:
            results.append({
                "id": page["id"],
                "title": page["title"]["rendered"],
                "slug": page["slug"],
                "missing_fields": missing
            })
            
    return results

all_results = {}
for name, url in SITES.items():
    all_results[name] = check_site(name, url)

print(json.dumps(all_results, indent=2))
