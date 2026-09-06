#!/bin/bash
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/38c82f4a8adbfc5c/api/tus/public_html"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODcxNzkxMiwiaWF0IjoxNzg4Njk2MzEyfQ.pafAQ6beUBD3n2HkJFF9JyyHb8ULpEUMR53wTN8mEvQ"
REST="a28d8a97c3128f01c8f13603c69b34bf92a7182e39c035b4ac433a8a934ea6f3-38c82f4a8adbfc5c"
THEME="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com/justccell-theme"

tus_upload() {
  local rel="$1"
  local file="$2"
  local size
  size=$(stat -f%z "$file")
  local post patch
  post=$(curl -sS -o /dev/null -w "%{http_code}" -X POST "${BASE}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" -H "Upload-Length: ${size}" -H "Upload-Offset: 0")
  patch=$(curl -sS -o /dev/null -w "%{http_code}" -X PATCH "${BASE}/${rel}?override=true" \
    -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
    -H "Tus-Resumable: 1.0.0" -H "Content-Type: application/offset+octet-stream" \
    -H "Upload-Offset: 0" --data-binary "@${file}")
  echo "${rel} -> post:${post} patch:${patch}"
}

tus_upload "wp-content/themes/justccell-theme/functions.php" "${THEME}/functions.php"
tus_upload "wp-content/themes/justccell-theme/style.css" "${THEME}/style.css"
tus_upload "wp-content/themes/justccell-theme/acf-json/group_jc_storefront.json" "${THEME}/acf-json/group_jc_storefront.json"
tus_upload "wp-content/themes/justccell-theme/inc/commerce.php" "${THEME}/inc/commerce.php"
tus_upload "wp-content/themes/justccell-theme/inc/cms-import.php" "${THEME}/inc/cms-import.php"
tus_upload "wp-content/themes/justccell-theme/inc/assets.php" "${THEME}/inc/assets.php"
tus_upload "wp-content/themes/justccell-theme/inc/page-layouts.php" "${THEME}/inc/page-layouts.php"
tus_upload "wp-content/themes/justccell-theme/inc/admin-menu.php" "${THEME}/inc/admin-menu.php"
tus_upload "wp-content/themes/justccell-theme/front-page.php" "${THEME}/front-page.php"

echo "ALL_OK"
