#!/usr/bin/env python3
"""
Setup script for WordPress AEO Autoblogger skill.
Run this once after configuring .env.wordpress with your real credentials.
"""
import os
import sys
import shutil

# Add skill to path
SKILL_PATH = os.path.expanduser("~/.openclaw/skills/wordpress-aeo-autoblogger")
sys.path.insert(0, SKILL_PATH)

def main():
    env_path = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/.env.wordpress"
    env_target = os.path.join(SKILL_PATH, ".env")

    # Check if .env exists in skill dir
    if not os.path.exists(env_target):
        print(f"[INFO] Copying {env_path} to {env_target}")
        shutil.copy2(env_path, env_target)
        print("[INFO] Please edit ~/.openclaw/skills/wordpress-aeo-autoblogger/.env")
        print("[INFO] and replace placeholder values with real credentials.")
        return

    # Load config
    from dotenv import load_dotenv
    load_dotenv(env_target)

    from config import DEFAULT_CONFIG

    # Check placeholders
    placeholders = []
    if DEFAULT_CONFIG["WP_URL"] == "":
        placeholders.append("WP_URL")
    if DEFAULT_CONFIG["WP_USERNAME"] == "":
        placeholders.append("WP_USERNAME")
    if DEFAULT_CONFIG["WP_APP_PASSWORD"] == "":
        placeholders.append("WP_APP_PASSWORD")
    if DEFAULT_CONFIG["GEMINI_API_KEY"] == "" and DEFAULT_CONFIG["OPENAI_API_KEY"] == "" and DEFAULT_CONFIG["ANTHROPIC_API_KEY"] == "":
        placeholders.append("LLM_API_KEY (GEMINI_API_KEY, OPENAI_API_KEY, or ANTHROPIC_API_KEY)")
    if DEFAULT_CONFIG["TARGET_NICHE"] == "":
        placeholders.append("TARGET_NICHE")

    if placeholders:
        print("[ERROR] The following required values are still placeholder/empty:")
        for p in placeholders:
            print(f"  - {p}")
        print("\nPlease edit ~/.claw/skills/wordpress-aeo-autoblogger/.env")
        sys.exit(1)

    # Validate credentials
    print("[INFO] Validating LLM provider...")
    try:
        from setup import validate_llm_provider
        validate_llm_provider(DEFAULT_CONFIG)
        print("[OK] LLM provider validated.")
    except Exception as e:
        print(f"[ERROR] LLM validation failed: {e}")
        sys.exit(1)

    print("[INFO] Validating WordPress credentials...")
    try:
        from setup import validate_wp_credentials
        validate_wp_credentials(DEFAULT_CONFIG)
        print("[OK] WordPress credentials validated.")
    except Exception as e:
        print(f"[ERROR] WordPress validation failed: {e}")
        sys.exit(1)

    # Initialize databases
    print("[INFO] Initializing databases...")
    from setup import initialize_databases
    initialize_databases()
    print("[OK] Databases initialized.")

    print("\n[SUCCESS] WordPress AEO Autoblogger is ready to use!")
    print("\nNext steps:")
    print("  1. uv run python ~/.claw/skills/wordpress-aeo-autoblogger/daily_worker.py")
    print("  2. Or set up a cron job to run it automatically")

if __name__ == "__main__":
    main()
