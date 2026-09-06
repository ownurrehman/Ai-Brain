#!/bin/bash
set -euo pipefail
BASE="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com"
TUS="https://srv1914-files.hstgr.io/rest/4a189bceb1689343/api/tus/public_html/wp-content/themes/justccell-theme"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODczMjUxNiwiaWF0IjoxNzg4NzEwOTE2fQ.atDNLKb8kVN6QziAVoemsWjCyPyhqHLDMBf7or6XNc4"
REST="1daf966903500d53d88cf6dbd9aba0828d4d8d99c9491815416af544603a2b20-4a189bceb1689343"

upload() {
  local src="$1"
  local rel="$2"
  local size
  size=$(stat -f%z "$src")
  curl -sS -o /dev/null -X POST "${TUS}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" \
    -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" \
    -H "Upload-Length: ${size}" \
    -H "Upload-Offset: 0"
  curl -sS -o /dev/null -X PATCH "${TUS}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" \
    -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" \
    -H "Content-Type: application/offset+octet-stream" \
    -H "Upload-Offset: 0" \
    --data-binary "@${src}"
  echo "OK ${rel} (${size} bytes)"
}

upload "${BASE}/justccell-theme/inc/admin-laser-zone.php" "inc/admin-laser-zone.php"
upload "${BASE}/justccell-theme/inc/coming-soon-page.php" "inc/coming-soon-page.php"
upload "${BASE}/_deploy/functions-prod-0303.php" "functions.php"
upload "${BASE}/_deploy/style-prod-0303.css" "style.css"
