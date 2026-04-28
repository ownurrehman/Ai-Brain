#!/usr/bin/env python3
"""
Script to check if a given URL returns a 200 OK status.
"""

import requests
import sys

def check_url_status(url):
    """
    Check if the given URL returns a 200 OK status.
    
    Args:
        url (str): The URL to check
        
    Returns:
        bool: True if status is 200 OK, False otherwise
    """
    try:
        response = requests.get(url, timeout=10)
        return response.status_code == 200
    except requests.exceptions.RequestException as e:
        print(f"Error checking URL {url}: {e}")
        return False

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python url_check.py <URL>")
        sys.exit(1)
    
    url = sys.argv[1]
    if check_url_status(url):
        print(f"URL {url} returned status 200 OK")
        sys.exit(0)
    else:
        print(f"URL {url} did not return status 200 OK")
        sys.exit(1)