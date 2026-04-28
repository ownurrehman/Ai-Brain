
import requests
from PIL import Image
import os

# Configuration
LOGO_URL = "https://rankray.com/wp-content/uploads/2025/06/Rank-Ray-HD-Horizontal-Logo-1.webp"
OUTPUT_DIR = "tmp_branded_images"
os.makedirs(OUTPUT_DIR, exist_ok=True)

def download_image(url, path):
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        img_data = requests.get(url, headers=headers, timeout=10).content
        with open(path, 'wb') as f:
            f.write(img_data)
        return True
    except Exception as e:
        print(f"Error downloading {url}: {e}")
        return False

def overlay_logo(image_path, logo_path, output_path):
    try:
        base_img = Image.open(image_path).convert("RGBA")
        logo_img = Image.open(logo_path).convert("RGBA")

        # Resize logo to fit (e.g., 15% of base image width)
        base_w, base_h = base_img.size
        logo_w, logo_h = logo_img.size
        
        target_logo_w = int(base_w * 0.2)
        scale = target_logo_w / logo_w
        target_logo_h = int(logo_h * scale)
        
        logo_img = logo_img.resize((target_logo_w, target_logo_h), Image.Resampling.LANCZOS)

        # Position: Bottom Right with padding
        padding = int(base_w * 0.03)
        position = (base_w - target_logo_w - padding, base_h - target_logo_h - padding)

        base_img.paste(logo_img, position, logo_img)
        
        # Convert back to RGB for saving as JPEG/WebP if needed, or save as PNG
        final_img = base_img.convert("RGB")
        final_img.save(output_path, "JPEG", quality=90)
        return True
    except Exception as e:
        print(f"Error overlaying logo on {image_path}: {e}")
        return False

# Setup logo
logo_path = "rankray_logo.webp"
if not download_image(LOGO_URL, logo_path):
    print("Failed to download logo. Process aborted.")
    exit(1)

# To be called for each image
def process_image(image_url, filename):
    tmp_path = f"tmp_{filename}"
    out_path = os.path.join(OUTPUT_DIR, filename)
    if download_image(image_url, tmp_path):
        if overlay_logo(tmp_path, logo_path, out_path):
            os.remove(tmp_path)
            return out_path
    return None
