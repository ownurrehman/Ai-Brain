#!/bin/bash
set -euo pipefail
BASE="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com"
THEME="${BASE}/justccell-theme"
TUS="https://srv1914-files.hstgr.io/rest/302a33f594317035/api/tus/public_html/wp-content/themes/justccell-theme"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODczMzI4NCwiaWF0IjoxNzg4NzExNjg0fQ.F2V3W3VsT-n9HjNUvTVjX7WNYOkzJQ7ftiX9Nc9gFnc"
REST="3ae4dcde545d3f5f7a279e0bdb630ba8072a3c703e38fe3a40cbab4b3dab77da-302a33f594317035"

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

# Checkout Phase A–B + grid fix (dev 0.9.304–0.9.307) — checkout bundle only
upload "${THEME}/inc/checkout-modernization.php" "inc/checkout-modernization.php"
upload "${THEME}/woocommerce/checkout/form-checkout.php" "woocommerce/checkout/form-checkout.php"
upload "${THEME}/woocommerce/checkout/review-order.php" "woocommerce/checkout/review-order.php"
upload "${THEME}/inc/commerce-pages.php" "inc/commerce-pages.php"
upload "${THEME}/inc/laser-engraving.php" "inc/laser-engraving.php"
upload "${THEME}/assets/css/woocommerce.css" "assets/css/woocommerce.css"
upload "${THEME}/assets/js/checkout-phase-a.js" "assets/js/checkout-phase-a.js"
upload "${BASE}/_deploy/functions-prod-0309.php" "functions.php"
upload "${BASE}/_deploy/style-prod-0309.css" "style.css"
