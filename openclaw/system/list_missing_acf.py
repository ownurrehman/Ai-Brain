import requests
import json

SITES = {
    "rankray": "https://rankray.com",
}

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
    pages = []
    url = f"{base_url}/wp-json/wp/v2/location-page?per_page=100"
    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        pages = response.json()
    except Exception:
        return []

    results = []
    for page in pages:
        if not page.get("service-type"):
            continue
        acf = page.get("acf", {})
        missing = [field for field in MANDATORY_FIELDS if not acf.get(field)]
        if missing:
            results.append({
                "title": page["title"]["rendered"],
                "link": page["link"]
            })
    return results

all_results = {}
for name, url in SITES.items():
    all_results[name] = check_site(name, url)

print(json.dumps(all_results, indent=2))
