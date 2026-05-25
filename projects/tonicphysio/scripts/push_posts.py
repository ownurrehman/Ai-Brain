import requests, base64, json, sys

base_url = "https://tonicphysio.com/wp-json/wp/v2/"
user = "Dan"
app_pass = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
creds = base64.b64encode(f"{user}:{app_pass}".encode()).decode()
headers = {
    "Authorization": f"Basic {creds}",
    "Content-Type": "application/json",
}

posts = [
    {
        "title": "Cervical Spondylosis Exercises for Neck Relief in Milton",
        "slug": "cervical-spondylosis-exercises-neck-relief-milton",
        "content": open("blog2-cervical-spondylosis.html").read(),
        "meta": {
            "_yoast_wpseo_title": "Cervical Spondylosis Exercises for Neck Relief in Milton | Tonic Physio",
            "_yoast_wpseo_metadesc": "Gentle exercises for cervical spondylosis in Milton. Reduce neck pain with physiotherapy guidance at Tonic Physio.",
        },
    },
    {
        "title": "Orthopedic Physiotherapy vs Regular Physiotherapy: What's the Difference",
        "slug": "orthopedic-physiotherapy-vs-regular-physiotherapy",
        "content": open("blog3-orthopedic-vs-regular.html").read(),
        "meta": {
            "_yoast_wpseo_title": "Orthopedic Physiotherapy vs Regular Physiotherapy | Tonic Physio",
            "_yoast_wpseo_metadesc": "Discover the difference between orthopedic and regular physiotherapy. Expert care at Tonic Physio in Milton.",
        },
    },
    {
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "slug": "pediatric-physiotherapy-when-your-child-needs-help-milton",
        "content": open("blog4-pediatric-physiotherapy.html").read(),
        "meta": {
            "_yoast_wpseo_title": "Pediatric Physiotherapy Milton | When Your Child Needs Help",
            "_yoast_wpseo_metadesc": "Pediatric physiotherapy in Milton for children. Gentle, evidence-based care at Tonic Physio.",
        },
    },
]

results = []
for p in posts:
    payload = {
        "title": p["title"],
        "slug": p["slug"],
        "content": p["content"],
        "status": "draft",
        "meta": p["meta"],
    }
    resp = requests.post(f"{base_url}posts", headers=headers, json=payload, timeout=60)
    if resp.status_code in (200, 201):
        data = resp.json()
        results.append({
            "id": data.get("id"),
            "slug": data.get("slug"),
            "title": data.get("title", {}).get("rendered"),
            "link": data.get("link"),
            "status": data.get("status"),
        })
    else:
        results.append({
            "title": p["title"],
            "error": f"HTTP {resp.status_code}",
            "body": resp.text[:500],
        })

print(json.dumps(results, indent=2))
