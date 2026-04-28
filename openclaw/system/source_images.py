
import requests
from PIL import Image
import os
import sys
from brand_images import download_image, overlay_logo, OUTPUT_DIR

# Images for each H2
IMAGES_MAP = {
    "The Recommendation Engine Theory: Ranking vs. Recommendation": "https://images.unsplash.com/photo-167744213 جذور-artificial-intelligence-network-digital-brain", # Using keywords in URL logic or specific known assets
    "The 3-Pillar GEO Framework: The Architecture of AI Visibility": "https://images.unsplash.com/photo-1486406146988-482c6070ef04", # Architecture/Design
    "Platform-Specific Strategies: Nuances of the AI Ecosystem": "https://images.unsplash.com/photo-1550751827-4c392c6218c3", # Tech ecosystem
    "The 'AI Audit' Methodology: Closing the Visibility Gap": "https://images.unsplash.com/photo-1460925895068-5f53159336e1", # Analysis/Audit
    "Conclusion: The Future of Digital Authority": "https://images.unsplash.com/photo-1451187580459-43490279c0d0", # Future/Growth
    "Frequently Asked Questions (FAQs)": "https://images.unsplash.com/photo-1523240795612-9a054b0db644", # Q&A
}

# Correcting Unsplash URLs to actual working ones
ACTUAL_IMAGES = {
    "The Recommendation Engine Theory: Ranking vs. Recommendation": "https://images.unsplash.com/photo-1639320379133-727207d15b68?q=80&w=1200", # Abstract AI
    "The 3-Pillar GEO Framework: The Architecture of AI Visibility": "https://images.unsplash.com/photo-1486406146988-482c6070ef04?q=80&w=1200", # Architecture
    "Platform-Specific Strategies: Nuances of the AI Ecosystem": "https://images.unsplash.com/photo-1519389950473-aef763851f3a?q=80&w=1200", # Ecosystem/Tech
    "The 'AI Audit' Methodology: Closing the Visibility Gap": "https://images.unsplash.com/photo-1454165833767-2356b99c7523?q=80&w=1200", # Data/Audit
    "Conclusion: The Future of Digital Authority": "https://images.unsplash.com/photo-1504384308090-c56d929351ad?q=80&w=1200", # Horizon/Future
    "Frequently Asked Questions (FAQs)": "https://images.unsplash.com/photo-1557804506-669a67965f63?q=80&w=1200", # Help/Support
}

def main():
    logo_path = "rankray_logo.webp"
    processed = {}

    for h2, url in ACTUAL_IMAGES.items():
        filename = f"{h2.lower().replace(' ', '_').replace(':', '').replace('(', '').replace(')', '').replace('/', '_')}.jpg"
        tmp_path = f"tmp_{filename}"
        out_path = os.path.join(OUTPUT_DIR, filename)
        
        print(f"Processing image for: {h2}...")
        if download_image(url, tmp_path):
            if overlay_logo(tmp_path, logo_path, out_path):
                os.remove(tmp_path)
                processed[h2] = out_path
                print(f"Successfully branded: {out_path}")
            else:
                print(f"Failed to brand: {h2}")
        else:
            print(f"Failed to download: {h2}")

    # Output the map as a file for the next step
    with open("image_map.txt", "w") as f:
        for h2, path in processed.items():
            f.write(f"{h2}::: {path}\n")

if __name__ == "__main__":
    main()
