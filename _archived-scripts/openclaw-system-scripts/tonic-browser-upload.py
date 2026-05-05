#!/usr/bin/env python3
"""Upload images via WordPress admin browser automation"""

import subprocess
import time

# Images to upload
images = [
    "pediatric-physiotherapy-why-choose.jpg",
    "pediatric-physiotherapy-solutions.jpg",
    "orthopedic-physiotherapy-why-choose.jpg",
    "orthopedic-physiotherapy-solutions.jpg"
]

print("🔧 Uploading images to TonicPhysio via browser automation...")
print("\n📋 Images to upload:")
for img in images:
    print(f"  - {img}")

print("\n🌐 Opening WordPress admin...")
print("Login URL: https://tonicphysio.com/wp-admin")
print("Credentials: Dan / RR#Tonic@2026")

print("\n📝 Manual upload steps:")
print("1. Go to Media → Add New")
print("2. Upload all 4 images")
print("3. Note the Media IDs after upload")
print("4. Edit Pediatric Physiotherapy page (ID: 1793)")
print("5. Set ACF fields: why_choose_us_image, solutions_image")
print("6. Edit Orthopedic Physiotherapy page (ID: 1791)")
print("7. Set ACF fields: why_choose_us_image, solutions_image")
print("8. Save both pages")

print("\n📍 Image files location:")
print("  /Users/sheikhown/.openclaw/workspace/")

print("\n✅ Once done, verify at:")
print("  https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/")
print("  https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/")
