#!/bin/bash
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/a75be179be830acb/api/tus/public_html"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODcyNTY4MSwiaWF0IjoxNzg4NzA0MDgxfQ.Dcv1tlZ1bq8C0oU6IQtfFuqSSpbe2DGMhHusBG4MG4I"
REST="adbaa67ba4c58f3084c293ac3d2a09a3b41dec3a62317e3e718708fc8bcc83ea-a75be179be830acb"
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
tus_upload "${P}/inc/cart-ajax.php" "${ROOT}/inc/cart-ajax.php"
tus_upload "${P}/template-parts/cart/drawer.php" "${ROOT}/template-parts/cart/drawer.php"
tus_upload "${P}/assets/js/cart-drawer.js" "${ROOT}/assets/js/cart-drawer.js"
tus_upload "${P}/assets/js/checkout-phase-a.js" "${ROOT}/assets/js/checkout-phase-a.js"
tus_upload "${P}/assets/css/cart-drawer.css" "${ROOT}/assets/css/cart-drawer.css"
tus_upload "${P}/assets/css/woocommerce.css" "${ROOT}/assets/css/woocommerce.css"
echo "ALL_OK"
