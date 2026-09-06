#!/bin/bash
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/b7e622d77c19132d/api/tus/public_html"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODcyNjAzNiwiaWF0IjoxNzg4NzA0NDM2fQ.2c6bqWDfhHObQ9IR7Qj1hHhOClmJ4OQBNIWFlYQb2S4"
REST="b63937f1f7858531d06aa2c1f9171b2919e496a90cca54d2e854275306e5caee-b7e622d77c19132d"
ROOT="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com/justccell-theme"
P="dev/wp-content/themes/justccell-theme"

tus_upload() {
  local rel="$1" file="$2" size post patch
  size=$(stat -f%z "$file")
  post=$(curl -sS -o /dev/null -w "%{http_code}" -X POST "${BASE}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" -H "Upload-Length: ${size}" -H "Upload-Offset: 0")
  patch=$(curl -sS -o /dev/null -w "%{http_code}" -X PATCH "${BASE}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" -H "Content-Type: application/offset+octet-stream" \
    -H "Upload-Offset: 0" --data-binary "@${file}")
  echo "${rel} -> post:${post} patch:${patch}"
}

tus_upload "${P}/functions.php" "${ROOT}/functions.php"
tus_upload "${P}/style.css" "${ROOT}/style.css"
tus_upload "${P}/inc/checkout-modernization.php" "${ROOT}/inc/checkout-modernization.php"
tus_upload "${P}/assets/css/woocommerce.css" "${ROOT}/assets/css/woocommerce.css"
echo "ALL_OK"
