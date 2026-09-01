import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# Maybe the application password belongs to a different user
# User 14 is "Own-ur-Rehman Sheikh" - maybe the password is for that account
# Let's try the admin-ajax approach

username = 'SheikhOpen'
password = 'T92W7D1oaUYtCUICnCmXC0mb'
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()

headers = {
    'Authorization': f'Basic {credentials}',
    'Content-Type': 'application/json'
}

# Try admin-ajax with action=restnonce to see if we can get a nonce
url = 'https://www.coinsfera.com/wp-admin/admin-ajax.php'
data = 'action=rest-nonce'.encode('utf-8')
req = urllib.request.Request(url, data=data, headers={**headers, 'Content-Type': 'application/x-www-form-urlencoded'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = resp.read().decode()
    print(f"admin-ajax rest-nonce: {result[:200]}")
except urllib.error.HTTPError as e:
    print(f"admin-ajax: {e.code} {e.reason}")
    print(e.read().decode()[:200])

# Try to login via wp-login.php to get cookies
print("\n=== Try wp-login.php ===")
import urllib.parse
login_data = urllib.parse.urlencode({
    'log': 'SheikhOpen',
    'pwd': 'T92W7D1oaUYtCUICnCmXC0mb',
    'wp-submit': 'Log In',
    'redirect_to': 'https://www.coinsfera.com/wp-admin/',
    'testcookie': '1'
}).encode('utf-8')

login_headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cookie': 'wordpress_test_cookie=WP+Cookie+check'
}

req = urllib.request.Request('https://www.coinsfera.com/wp-login.php', data=login_data, headers=login_headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    print(f"Login redirect: {resp.url}")
    cookies = resp.headers.get_all('Set-Cookie')
    if cookies:
        print(f"Cookies: {cookies[:3]}")
except urllib.error.HTTPError as e:
    print(f"Login: {e.code} {e.reason}")
    body = e.read().decode()
    # Check for error messages
    if 'incorrect' in body.lower() or 'error' in body.lower():
        import re
        errors = re.findall(r'<div id="login_error"[^>]*>(.*?)</div>', body, re.DOTALL)
        if errors:
            clean = re.sub(r'<[^>]+>', '', errors[0]).strip()
            print(f"Login error: {clean[:200]}")
    print(f"Body length: {len(body)}")

# Try wp-login without application password - use it as regular password
print("\n=== Try wp-login with spaces ===")
login_data2 = urllib.parse.urlencode({
    'log': 'SheikhOpen',
    'pwd': 'T92W 7D1o aUYt CUIC nCmX C0mb',
    'wp-submit': 'Log In',
    'redirect_to': 'https://www.coinsfera.com/wp-admin/',
    'testcookie': '1'
}).encode('utf-8')

req = urllib.request.Request('https://www.coinsfera.com/wp-login.php', data=login_data2, headers=login_headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    print(f"Login redirect: {resp.url}")
    cookies = resp.headers.get_all('Set-Cookie')
    if cookies:
        print(f"Cookies: {cookies[:3]}")
except urllib.error.HTTPError as e:
    print(f"Login: {e.code} {e.reason}")
    body = e.read().decode()
    import re
    errors = re.findall(r'<div id="login_error"[^>]*>(.*?)</div>', body, re.DOTALL)
    if errors:
        clean = re.sub(r'<[^>]+>', '', errors[0]).strip()
        print(f"Login error: {clean[:200]}")