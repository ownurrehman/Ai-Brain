
import requests
from bs4 import BeautifulSoup

url_login = "https://cms.khanllp.com/login"
url_post = "https://cms.khanllp.com/mylogin"

headers = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
    "Accept-Language": "en-US,en;q=0.9",
    "Referer": url_login
}

session = requests.Session()

try:
    # 1. Get login page to extract CSRF token
    print(f"Fetching {url_login}...")
    resp = session.get(url_login, headers=headers)
    resp.raise_for_status()
    
    soup = BeautifulSoup(resp.text, 'html.parser')
    token_input = soup.find('input', {'name': '_token'})
    if not token_input:
        print("Could not find CSRF token in login page.")
        exit(1)
    
    csrf_token = token_input.get('value')
    print(f"Found token: {csrf_token}")
    
    # 2. Perform login
    payload = {
        '_token': csrf_token,
        'username': 'own',
        'password': 'Rank#kLLP@2025.ray'
    }
    
    print(f"Posting to {url_post}...")
    post_resp = session.post(url_post, data=payload, headers=headers, allow_redirects=True)
    post_resp.raise_for_status()
    
    # 3. Verify login and find 'Blogs' link
    print("Login request completed. Analyzing dashboard...")
    soup_dashboard = BeautifulSoup(post_resp.text, 'html.parser')
    
    # Look for a link that contains 'blog' (case insensitive)
    blog_link = None
    for a in soup_dashboard.find_all('a', href=True):
        if 'blog' in a.text.lower() or 'blog' in a['href'].lower():
            blog_link = a['href']
            print(f"Found Blogs link: {blog_link}")
            break
            
    if not blog_link:
        print("Could not find a 'Blogs' link on the dashboard.")
        # Check if we're actually logged in or just back at login
        if "log in" in post_resp.text.lower():
            print("FAILED: Redirected back to login page.")
        else:
            print("SUCCESS: Logged in, but 'Blogs' link not found in common patterns.")
        exit(1)

    # 4. Navigate to Blogs page
    print(f"Navigating to {blog_link}...")
    # Ensure we use absolute URL if it's relative
    if blog_link.startswith('/'):
        full_blog_url = "https://cms.khanllp.com" + blog_link
    else:
        full_blog_url = blog_link
        
    blog_resp = session.get(full_blog_url, headers=headers)
    blog_resp.raise_for_status()
    
    # 5. Confirm 'Add New' is accessible
    content_lower = blog_resp.text.lower()
    if "add new" in content_lower:
        print("SUCCESS: 'Add New' blog option is accessible.")
    else:
        print("FAILED: 'Add New' not found on the Blogs page.")
        print("Page snippet:", blog_resp.text[:1000])

except Exception as e:
    print(f"Error occurred: {e}")
    exit(1)
