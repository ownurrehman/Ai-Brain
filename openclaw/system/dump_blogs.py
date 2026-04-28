
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
    # 1. Get login page
    resp = session.get(url_login, headers=headers)
    soup = BeautifulSoup(resp.text, 'html.parser')
    token_input = soup.find('input', {'name': '_token'})
    csrf_token = token_input.get('value')
    
    # 2. Login
    payload = {
        '_token': csrf_token,
        'username': 'own',
        'password': 'Rank#kLLP@2025.ray'
    }
    session.post(url_post, data=payload, headers=headers, allow_redirects=True)
    
    # 3. Find Blogs link
    soup_dashboard = BeautifulSoup(session.get("https://cms.khanllp.com/admin/blogs", headers=headers).text, 'html.parser')
    
    # Dump the whole page to a file for analysis
    with open("blogs_page.html", "w", encoding="utf-8") as f:
        f.write(soup_dashboard.prettify())
    
    print("Page dumped to blogs_page.html")

except Exception as e:
    print(f"Error: {e}")
