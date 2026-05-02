"""
gemini_image_gen.py — Generate images via Gemini web app (gemini.google.com) using Playwright.

Usage:
    python gemini_image_gen.py "A futuristic lobster wearing a VR headset"
    python gemini_image_gen.py --setup  # One-time login setup

Output (JSON on stdout):
    {
        "success": true,
        "prompt": "A futuristic lobster wearing a VR headset",
        "image_url": "https://...",
        "downloaded_path": "/tmp/gemini_images/..."
    }

Setup Instructions:
    1. Run: python gemini_image_gen.py --setup
    2. Chrome will open. Log into Gemini with rankrayofficial@gmail.com
    3. Close Chrome when done. The login session will persist for future runs.

Requirements:
    - Playwright (pip install playwright)
    - playwright install chromium
    - Google account with Gemini access
"""

import argparse
import asyncio
import json
import os
import sys
import time
import subprocess
import tempfile
import shutil
from pathlib import Path

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeout

# ── Configuration ────────────────────────────────────────────────
GEMINI_URL = "https://gemini.google.com"
DOWNLOAD_DIR = Path("/tmp/gemini_images")
DEFAULT_TIMEOUT = 120  # seconds — image gen can take a while

# Dedicated profile for OpenClaw automation (avoids conflicts with user's main Chrome)
AUTOMATION_PROFILE = Path.home() / ".openclaw/gemini_automation_profile"


def is_logged_in(page):
    """Check if the user is logged into Gemini."""
    import asyncio
    try:
        # Check for sign-in button
        signin = asyncio.get_event_loop().run_until_complete(
            page.query_selector('button:has-text("Sign in")')
        )
        if signin:
            return False
        
        # Check for textarea (logged in state)
        textarea = asyncio.get_event_loop().run_until_complete(
            page.query_selector('textarea')
        )
        if textarea:
            return True
        
        return False
    except Exception:
        return False


async def wait_for_gemini_ready(page, timeout=DEFAULT_TIMEOUT):
    """Wait until the Gemini chat input box is present."""
    selectors = [
        'textarea[placeholder*="Ask"]',
        'textarea[placeholder*="Enter"]',
        'textarea[placeholder*="Prompt"]',
        'textarea[placeholder*="Message"]',
        'textarea[aria-label*="Ask"]',
        'textarea[aria-label*="Prompt"]',
        'textarea',
    ]
    start = time.time()
    while time.time() - start < timeout:
        for sel in selectors:
            try:
                el = await page.wait_for_selector(sel, timeout=2000)
                if el and await el.is_visible():
                    return sel
            except PlaywrightTimeout:
                continue
        # Check for "Sign in" button
        signin = await page.query_selector('button:has-text("Sign in")')
        if signin and await signin.is_visible():
            raise RuntimeError("Not signed in to Gemini — run with --setup to log in first.")
        await asyncio.sleep(0.5)
    raise RuntimeError("Gemini input box not found within timeout.")


async def type_and_submit(page, prompt: str):
    """Focus the textarea, type the prompt, and submit."""
    sel = await wait_for_gemini_ready(page)
    textarea = await page.query_selector(sel)
    await textarea.click()
    await page.wait_for_timeout(300)
    await textarea.fill("")
    await page.wait_for_timeout(100)
    await textarea.type(prompt, delay=10)
    await page.wait_for_timeout(200)
    # Try pressing Enter; if that doesn't work, look for a send button
    await textarea.press("Enter")
    await page.wait_for_timeout(500)
    # Check if text is still there (Enter didn't submit)
    value = await textarea.input_value()
    if value == prompt:
        # Look for send button
        send_btn = await page.query_selector('button[aria-label*="Send"], button[aria-label*="submit"], button.send-button, [data-test-id="send-button"]')
        if send_btn:
            await send_btn.click()


async def extract_image_url(page, timeout=DEFAULT_TIMEOUT):
    """
    Poll the page for generated image <img> elements in the latest
    assistant response. Return the first high-resolution src.
    """
    start = time.time()
    last_count = 0
    stable_count = 0
    seen_urls = set()

    while time.time() - start < timeout:
        # Look for images with Google user content domains
        imgs = []
        for pattern in ['googleusercontent', 'generativelanguage', 'googleapis', 'gstatic']:
            found = await page.query_selector_all(f'img[src*="{pattern}"]')
            imgs.extend(found)

        # Also look for any reasonably-sized images that might be generated
        if not imgs:
            all_imgs = await page.query_selector_all('img')
            for img in all_imgs:
                width = await img.evaluate('el => el.naturalWidth || el.width || 0')
                if width > 100:
                    imgs.append(img)

        if imgs:
            # Get the last image's src
            img = imgs[-1]
            src = await img.get_attribute("src")
            if src and src.startswith("http"):
                seen_urls.add(src)
                # Check if image count has stabilized (generation complete)
                if len(imgs) == last_count:
                    stable_count += 1
                    if stable_count >= 2:  # Stable for 2 checks
                        return src
                else:
                    stable_count = 0
                    last_count = len(imgs)

        # Check if generation is still in progress
        generating = await page.query_selector('text=/Generating|Creating|Working|Thinking/')
        if generating:
            await asyncio.sleep(1)
            continue

        # Check for error messages
        error = await page.query_selector('text=/error|unable|failed|sorry/i')
        if error:
            error_text = await error.text_content()
            raise RuntimeError(f"Gemini error: {error_text}")

        await asyncio.sleep(1.5)

    # If we timed out but have seen URLs, return the last one
    if seen_urls:
        return list(seen_urls)[-1]

    raise RuntimeError("No generated image found within timeout.")


async def download_image(page, url: str, dest_dir: Path) -> Path:
    """Download the image using the browser's fetch + disk write."""
    dest_dir.mkdir(parents=True, exist_ok=True)
    ext = ".png"
    if ".jpg" in url or ".jpeg" in url:
        ext = ".jpg"
    elif ".webp" in url:
        ext = ".webp"
    dest = dest_dir / f"gemini_{int(time.time())}{ext}"

    b64_data = await page.evaluate(
        """async (url) => {
            const response = await fetch(url, { credentials: 'include' });
            const blob = await response.blob();
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result.split(',')[1]);
                reader.readAsDataURL(blob);
            });
        }""",
        url,
    )
    dest.write_bytes(__import__("base64").b64decode(b64_data))
    return dest


async def setup_profile():
    """One-time setup: launch Chrome with automation profile for user to log in."""
    print(json.dumps({
        "status": "setup_required",
        "message": "Opening Chrome with automation profile. Please log into Gemini and close Chrome when done.",
        "profile_path": str(AUTOMATION_PROFILE)
    }, indent=2))
    sys.stdout.flush()

    async with async_playwright() as playwright:
        # Launch headed Chrome with automation profile
        context = await playwright.chromium.launch_persistent_context(
            user_data_dir=str(AUTOMATION_PROFILE),
            headless=False,
            executable_path="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
            args=[
                "--no-first-run",
                "--no-default-browser-check",
                "--disable-blink-features=AutomationControlled",
                "--disable-infobars",
                "--disable-popup-blocking",
                "--disable-notifications",
            ],
        )
        page = await context.new_page()
        await page.goto(GEMINI_URL)
        print(json.dumps({
            "status": "waiting",
            "message": "Chrome is open. Please log in to Gemini and close Chrome when done."
        }, indent=2))
        sys.stdout.flush()

        # Wait for the page to be closed (user closes browser)
        try:
            while True:
                pages = context.pages
                if not pages or all(p.is_closed() for p in pages):
                    break
                await asyncio.sleep(1)
        except Exception:
            pass

        print(json.dumps({
            "status": "complete",
            "message": "Setup complete! You can now run image generation.",
            "profile_path": str(AUTOMATION_PROFILE)
        }, indent=2))
        return {"success": True, "setup": True}


async def generate_image(prompt: str, headless: bool = True, download: bool = True):
    """Generate an image using Gemini web app."""
    if not AUTOMATION_PROFILE.exists() or not any(AUTOMATION_PROFILE.iterdir()):
        return {
            "success": False,
            "error": (
                f"Automation profile not found at {AUTOMATION_PROFILE}. "
                "Run with --setup first to create it and log in."
            ),
            "setup_required": True,
        }

    async with async_playwright() as playwright:
        # Launch Chrome with the automation profile
        context = await playwright.chromium.launch_persistent_context(
            user_data_dir=str(AUTOMATION_PROFILE),
            headless=headless,
            executable_path="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
            args=[
                "--no-first-run",
                "--no-default-browser-check",
                "--disable-blink-features=AutomationControlled",
                "--disable-infobars",
                "--disable-popup-blocking",
                "--disable-notifications",
            ],
        )
        page = context.pages[0] if context.pages else await context.new_page()
        await page.set_viewport_size({"width": 1440, "height": 900})

        try:
            await page.goto(GEMINI_URL, wait_until="domcontentloaded")
            await page.wait_for_timeout(3000)

            # Check if logged in
            signin = await page.query_selector('button:has-text("Sign in")')
            if signin and await signin.is_visible():
                return {
                    "success": False,
                    "error": "Not signed in to Gemini. The session may have expired. Run with --setup to log in again.",
                    "setup_required": True,
                }

            await type_and_submit(page, prompt)

            # Wait for image generation (can take 10-30 seconds)
            await page.wait_for_timeout(5000)

            # Poll for image
            image_url = await extract_image_url(page)

            result = {
                "success": True,
                "prompt": prompt,
                "image_url": image_url,
                "downloaded_path": None,
            }

            if download:
                downloaded = await download_image(page, image_url, DOWNLOAD_DIR)
                result["downloaded_path"] = str(downloaded)

            return result

        except Exception as exc:
            # Capture screenshot for debugging
            try:
                debug_path = DOWNLOAD_DIR / f"debug_{int(time.time())}.png"
                await page.screenshot(path=str(debug_path))
            except Exception:
                debug_path = None
            return {
                "success": False,
                "prompt": prompt,
                "error": str(exc),
                "debug_screenshot": str(debug_path) if debug_path else None,
            }
        finally:
            await context.close()


def main():
    parser = argparse.ArgumentParser(description="Generate images via Gemini web app")
    parser.add_argument("prompt", nargs="?", help="Image generation prompt")
    parser.add_argument(
        "--setup", action="store_true", help="One-time setup to create profile and log in"
    )
    parser.add_argument(
        "--no-download", action="store_true", help="Only output the image URL"
    )
    parser.add_argument(
        "--headed", action="store_true", help="Show browser UI for debugging"
    )
    args = parser.parse_args()

    if args.setup:
        result = asyncio.run(setup_profile())
        sys.exit(0)

    if not args.prompt:
        parser.error("Prompt is required (unless using --setup)")

    result = asyncio.run(
        generate_image(args.prompt, headless=not args.headed, download=not args.no_download)
    )
    print(json.dumps(result, indent=2))
    sys.exit(0 if result.get("success") else 1)


if __name__ == "__main__":
    main()
