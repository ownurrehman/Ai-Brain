#!/usr/bin/env python3
"""
Fix Yoast SEO fields for 5 Tonic Physio pages via browser automation.
"""

import subprocess
import json
import time

# Credentials
WP_ADMIN = "https://tonicphysio.com/wp-admin"
WP_LOGIN = "https://tonicphysio.com/wp-login.php"
USERNAME = "rankrayagency@gmail.com"
APP_PASSWORD = "4isf Zcbd pvGI O1fp lQKB Jz2M"

# Pages to fix
pages = [
    {
        "name": "B-Pulse Pelvic Floor",
        "id": 11603,
        "edit_url": "/wp-admin/post.php?post=11603&action=edit",
        "yoast_title": "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio",
        "yoast_meta": "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
    },
    {
        "name": "Joint Pain and Stiffness",
        "id": 6971,
        "edit_url": "/wp-admin/post.php?post=6971&action=edit",
        "yoast_title": "Joint Pain Treatment Milton | Tonic Physio",
        "yoast_meta": "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
    },
    {
        "name": "Orthopedic Physiotherapy",
        "id": 1791,
        "edit_url": "/wp-admin/post.php?post=1791&action=edit",
        "yoast_title": "Orthopedic Physiotherapy Milton | Tonic Physio",
        "yoast_meta": "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
    },
    {
        "name": "Pediatric Physiotherapy",
        "id": 1793,
        "edit_url": "/wp-admin/post.php?post=1793&action=edit",
        "yoast_title": "Pediatric Physiotherapy Milton | Tonic Physio",
        "yoast_meta": "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
    },
    {
        "name": "Hot Stone Massage",
        "id": 6587,
        "edit_url": "/wp-admin/post.php?post=6587&action=edit",
        "yoast_title": "Hot Stone Massage Milton | Tonic Physio",
        "yoast_meta": "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."
    }
]

def run_browser_command(cmd):
    """Run a browser command and return output."""
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout, result.stderr, result.returncode

def main():
    print("Starting Yoast SEO fix for Tonic Physio...")
    
    # Start browser session
    print("Starting browser session...")
    stdout, stderr, code = run_browser_command("openclaw browser action=start")
    if code != 0:
        print(f"Error starting browser: {stderr}")
        return
    
    time.sleep(3)
    
    # Open login page
    print(f"Opening {WP_LOGIN}...")
    stdout, stderr, code = run_browser_command(f'openclaw browser action=open targetUrl="{WP_LOGIN}"')
    time.sleep(5)
    
    # Get snapshot to see the page
    print("Getting snapshot...")
    stdout, stderr, code = run_browser_command("openclaw browser action=snapshot")
    print(f"Snapshot output: {stdout[:500] if stdout else 'No output'}")
    
    # Try to interact with login form
    print("Attempting login...")
    # Type username
    stdout, stderr, code = run_browser_command('openclaw browser action=act ref="e1" text="rankrayagency@gmail.com"')
    time.sleep(2)
    
    # Get another snapshot
    stdout, stderr, code = run_browser_command("openclaw browser action=snapshot")
    print(f"After username: {stdout[:500] if stdout else 'No output'}")

if __name__ == "__main__":
    main()
