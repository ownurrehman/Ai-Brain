"""
gemini_image_gen.py — Generate images via Gemini web app (gemini.google.com) using OpenClaw browser.

Usage:
    python gemini_image_gen.py "A futuristic lobster wearing a VR headset"

Output (JSON on stdout):
    {
        "success": true,
        "prompt": "A futuristic lobster wearing a VR headset",
        "image_url": "https://...",
        "downloaded_path": "/tmp/gemini_images/..."
    }

Requirements:
    - OpenClaw browser running (openclaw browser status)
    - Gemini already logged in via OpenClaw browser

Strategy:
    Uses the OpenClaw browser tool which connects to the user's existing
    Chrome browser (with their logged-in session) via CDP.
"""

import argparse
import asyncio
import json
import os
import sys
import time
import subprocess
import tempfile
from pathlib import Path

from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeout

# ── Configuration ────────────────────────────────────────────────
GEMINI_URL = "https://gemini.google.com"
DOWNLOAD_DIR = Path("/tmp/gemini_images")
DEFAULT_TIMEOUT = 180  # seconds — image gen can take a while
CDP_URL = "http://127.0.0.1:18800"


async def find_or_create_gemini_tab(browser):
    """Find existing Gemini tab or create new one."""
    # List all pages
    contexts = browser.contexts
    for context in contexts:
        for page in context.pages:
            if "gemini.google.com" in page.url:
                print(json.dumps({"status": "info", "message": f"Found existing Gemini tab: {page.url}"}))
                return page

    # Create new tab
    context = contexts[0] if contexts else await browser.new_context()
    page = await context.new_page()
    await page.goto(GEMINI_URL)
    return page


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
            raise RuntimeError("Not signed in to Gemini — please log in via the OpenClaw browser first.")
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
    await textarea.press("Enter")
    await page.wait_for_timeout(500)
    # Check if text is still there (Enter didn't submit)
    try:
        value = await textarea.input_value()
        if value == prompt:
            # Look for send button
            send_btn = await page.query_selector('button[aria-label*="Send"], button[aria-label*="submit"], button.send-button, [data-test-id="send-button"]')
            if send_btn:
                await send_btn.click()
    except Exception:
        pass  # Textarea might have been cleared after submission


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
                try:
                    width = await img.evaluate('el => el.naturalWidth || el.width || 0')
                    if width > 100:
                        imgs.append(img)
                except Exception:
                    pass

        if imgs:
            # Get the last image's src
            img = imgs[-1]
            try:
                src = await img.get_attribute("src")
                if src and src.startswith("http"):
                    seen_urls.add(src)
                    # Check if image count has stabilized (generation complete)
                    if len(imgs) == last_count:
                        stable_count += 1
                        if stable_count >= 3:  # Stable for 3 checks
                            return src
                    else:
                        stable_count = 0
                        last_count = len(imgs)
            except Exception:
                pass

        # Check if generation is still in progress
        try:
            generating = await page.query_selector('text=/Generating|Creating|Working|Thinking/')
            if generating:
                await asyncio.sleep(1)
                continue
        except Exception:
            pass

        # Check for error messages
        try:
            error = await page.query_selector('text=/error|unable|failed|sorry/i')
            if error:
                error_text = await error.text_content()
                raise RuntimeError(f"Gemini error: {error_text}")
        except RuntimeError:
            raise
        except Exception:
            pass

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


async def generate_image(prompt: str, download: bool = True):
    """Generate an image using Gemini via OpenClaw browser."""
    async with async_playwright() as playwright:
        try:
            browser = await playwright.chromium.connect_over_cdp(CDP_URL)
        except Exception as exc:
            return {
                "success": False,
                "error": f"Could not connect to OpenClaw browser at {CDP_URL}: {exc}",
                "hint": "Make sure OpenClaw browser is running: openclaw browser status"
            }

        page = await find_or_create_gemini_tab(browser)
        await page.set_viewport_size({"width": 1440, "height": 900})

        try:
            # Refresh if needed
            if "gemini.google.com" not in page.url:
                await page.goto(GEMINI_URL, wait_until="domcontentloaded")
            await page.wait_for_timeout(2000)

            await type_and_submit(page, prompt)

            # Wait for image generation (can take 15-45 seconds)
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
            # Don't close the browser since it's shared with OpenClaw
            pass


def main():
    parser = argparse.ArgumentParser(description="Generate images via Gemini web app using OpenClaw browser")
    parser.add_argument("prompt", help="Image generation prompt")
    parser.add_argument(
        "--no-download", action="store_true", help="Only output the image URL"
    )
    args = parser.parse_args()

    result = asyncio.run(
        generate_image(args.prompt, download=not args.no_download)
    )
    print(json.dumps(result, indent=2))
    sys.exit(0 if result.get("success") else 1)


if __name__ == "__main__":
    main()
