#!/usr/bin/env python3
"""
WordPress Image Uploader with Duplicate Detection

Uploads images to WordPress media library via REST API:
- Checks for duplicates before upload
- Proper filename optimization
- Alt text setting
- Returns media ID for embedding

Usage: python wordpress-image-uploader.py <image-path> <alt-text> [filename]
"""

import os
import sys
import requests
import hashlib
from pathlib import Path
from typing import Optional, List, Dict

# WordPress config (from environment)
WP_SITE_URL = os.environ.get('WP_SITE_URL', 'https://rankray.com')
WP_USERNAME = os.environ.get('WP_USERNAME', 'admin')
WP_APP_PASSWORD = os.environ.get('WP_APP_PASSWORD', '')

MEDIA_INDEX_FILE = Path(__file__).parent.parent / 'images' / 'media-index.json'


class WordPressMediaUploader:
    """WordPress media uploader with duplicate detection"""
    
    def __init__(self, site_url: str, username: str, app_password: str):
        self.site_url = site_url.rstrip('/')
        self.auth = (username, app_password)
        self.api_base = f"{self.site_url}/wp-json/wp/v2"
        self.media_index = self._load_media_index()
    
    def _load_media_index(self) -> Dict:
        """Load local media index for duplicate tracking"""
        if MEDIA_INDEX_FILE.exists():
            import json
            try:
                with open(MEDIA_INDEX_FILE, 'r') as f:
                    return json.load(f)
            except:
                return {"uploads": []}
        return {"uploads": []}
    
    def _save_media_index(self):
        """Save media index after upload"""
        import json
        MEDIA_INDEX_FILE.parent.mkdir(parents=True, exist_ok=True)
        with open(MEDIA_INDEX_FILE, 'w') as f:
            json.dump(self.media_index, f, indent=2)
    
    def _generate_file_hash(self, file_path: str) -> str:
        """Generate hash for duplicate detection"""
        with open(file_path, 'rb') as f:
            return hashlib.md5(f.read()).hexdigest()
    
    def check_duplicate_by_filename(self, filename: str) -> Optional[int]:
        """Check if file with same name exists in media library"""
        try:
            response = requests.get(
                f"{self.api_base}/media",
                auth=self.auth,
                params={'search': filename, 'per_page': 10}
            )
            
            if response.status_code == 200:
                media_items = response.json()
                for item in media_items:
                    if item.get('slug') == filename.replace('.jpg', '').replace('.png', ''):
                        print(f"✓ Found existing media: {filename} (ID: {item['id']})")
                        return item['id']
        except Exception as e:
            print(f"Error checking duplicates: {e}")
        
        return None
    
    def check_duplicate_by_hash(self, file_hash: str) -> Optional[int]:
        """Check local index for duplicate by file hash"""
        for upload in self.media_index['uploads']:
            if upload.get('hash') == file_hash:
                print(f"✓ Found duplicate in local index: {upload['filename']} (ID: {upload['media_id']})")
                return upload['media_id']
        return None
    
    def upload_image(self, file_path: str, alt_text: str, custom_filename: str = None) -> Optional[int]:
        """
        Upload image to WordPress media library
        
        Args:
            file_path: Path to image file
            alt_text: Alt text for SEO
            custom_filename: Optional custom filename (will be SEO-optimized)
        
        Returns:
            Media ID if successful, None if failed
        """
        
        if not Path(file_path).exists():
            print(f"✗ File not found: {file_path}")
            return None
        
        # Generate filename
        if custom_filename:
            filename = custom_filename.lower().replace(' ', '-').replace('_', '-')
        else:
            filename = Path(file_path).name
        
        # Check for duplicates
        file_hash = self._generate_file_hash(file_path)
        
        existing_id = self.check_duplicate_by_hash(file_hash)
        if existing_id:
            return existing_id
        
        existing_id = self.check_duplicate_by_filename(filename)
        if existing_id:
            # Update alt text if needed
            self._update_alt_text(existing_id, alt_text)
            return existing_id
        
        # Upload new image
        print(f"Uploading: {filename}...")
        
        try:
            with open(file_path, 'rb') as f:
                image_data = f.read()
            
            # Determine content type
            content_type = 'image/jpeg' if filename.endswith('.jpg') or filename.endswith('.jpeg') else 'image/png'
            
            headers = {
                'Content-Type': content_type,
                'Content-Disposition': f'attachment; filename={filename}'
            }
            
            response = requests.post(
                f"{self.api_base}/media",
                auth=self.auth,
                headers=headers,
                data=image_data
            )
            
            if response.status_code == 201:
                media_id = response.json()['id']
                media_url = response.json().get('source_url', '')
                
                print(f"✓ Uploaded: {filename} (ID: {media_id})")
                
                # Set alt text
                self._update_alt_text(media_id, alt_text)
                
                # Add to index
                self.media_index['uploads'].append({
                    'filename': filename,
                    'hash': file_hash,
                    'media_id': media_id,
                    'url': media_url,
                    'alt_text': alt_text,
                    'upload_date': str(Path(file_path).stat().st_mtime)
                })
                self._save_media_index()
                
                return media_id
            else:
                print(f"✗ Upload failed: {response.status_code} - {response.text[:200]}")
                return None
        
        except Exception as e:
            print(f"✗ Upload error: {e}")
            return None
    
    def _update_alt_text(self, media_id: int, alt_text: str):
        """Update image alt text"""
        try:
            response = requests.post(
                f"{self.api_base}/media/{media_id}",
                auth=self.auth,
                json={'meta': {'_wp_attachment_image_alt': alt_text}}
            )
            
            if response.status_code == 200:
                print(f"  ✓ Alt text set: {alt_text[:50]}...")
            else:
                print(f"  ⚠ Alt text update failed: {response.status_code}")
        
        except Exception as e:
            print(f"  ✗ Alt text error: {e}")
    
    def get_media_url(self, media_id: int) -> str:
        """Get media URL from ID"""
        try:
            response = requests.get(
                f"{self.api_base}/media/{media_id}",
                auth=self.auth
            )
            
            if response.status_code == 200:
                return response.json().get('source_url', '')
        except:
            pass
        
        return f"{self.site_url}/?attachment_id={media_id}"


def upload_images_from_plan(plan_file: str) -> Dict[str, int]:
    """
    Upload all images from image plan JSON
    
    Returns dict mapping filename to media ID
    """
    import json
    
    with open(plan_file, 'r') as f:
        plan = json.load(f)
    
    uploader = WordPressMediaUploader(WP_SITE_URL, WP_USERNAME, WP_APP_PASSWORD)
    
    uploaded = {}
    
    # Upload featured image
    if 'featured' in plan:
        featured = plan['featured']
        print(f"\n{'='*60}")
        print(f"UPLOADING FEATURED IMAGE")
        print(f"{'='*60}")
        
        # Download image first
        if 'download_url' in featured:
            download_path = f"/tmp/{featured['filename']}"
            download_image(featured['download_url'], download_path)
            
            media_id = uploader.upload_image(
                download_path,
                featured['alt_text'],
                featured['filename']
            )
            
            if media_id:
                uploaded['featured'] = media_id
    
    # Upload body images
    if 'body_images' in plan:
        for i, image in enumerate(plan['body_images'], 1):
            print(f"\n{'='*60}")
            print(f"UPLOADING BODY IMAGE {i}/{len(plan['body_images'])}")
            print(f"{'='*60}")
            
            if 'download_url' in image:
                download_path = f"/tmp/{image['filename']}"
                download_image(image['download_url'], download_path)
                
                media_id = uploader.upload_image(
                    download_path,
                    image['alt_text'],
                    image['filename']
                )
                
                if media_id:
                    uploaded[image['filename']] = media_id
    
    return uploaded


def download_image(url: str, output_path: str):
    """Download image from URL"""
    import requests
    
    print(f"Downloading: {url}")
    response = requests.get(url, stream=True)
    
    if response.status_code == 200:
        with open(output_path, 'wb') as f:
            for chunk in response.iter_content(8192):
                f.write(chunk)
        print(f"✓ Downloaded: {output_path}")
    else:
        print(f"✗ Download failed: {response.status_code}")


if __name__ == "__main__":
    """Upload images from plan"""
    
    if len(sys.argv) < 2:
        print("Usage: python wordpress-image-uploader.py <plan.json>")
        print("       python wordpress-image-uploader.py --single <image.jpg> <alt-text> [filename]")
        sys.exit(1)
    
    if sys.argv[1] == "--single":
        # Single image upload
        if len(sys.argv) < 4:
            print("Need: image path, alt text, [filename]")
            sys.exit(1)
        
        image_path = sys.argv[2]
        alt_text = sys.argv[3]
        filename = sys.argv[4] if len(sys.argv) > 4 else None
        
        uploader = WordPressMediaUploader(WP_SITE_URL, WP_USERNAME, WP_APP_PASSWORD)
        media_id = uploader.upload_image(image_path, alt_text, filename)
        
        if media_id:
            print(f"\n✓ Upload complete! Media ID: {media_id}")
        else:
            print("\n✗ Upload failed")
    
    else:
        # Upload from plan
        plan_file = sys.argv[1]
        uploaded = upload_images_from_plan(plan_file)
        
        print(f"\n{'='*60}")
        print("UPLOAD SUMMARY")
        print(f"{'='*60}")
        print(f"Total uploaded: {len(uploaded)}")
        for filename, media_id in uploaded.items():
            print(f"  - {filename}: ID {media_id}")
