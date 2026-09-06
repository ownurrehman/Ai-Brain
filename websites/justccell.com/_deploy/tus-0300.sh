#!/bin/bash
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/3ab646d0d1c3f935/api/tus/public_html"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODcxNzY0MiwiaWF0IjoxNzg4Njk2MDQyfQ.rF0kmOO-mGPdOS9MESO2JOXlEMCHZ2lpzNxdfPs00SA"
REST="bf0bfa0c523d039941377269b6def20a50a71c778156070ff38af1ae0b036fca-3ab646d0d1c3f935"
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
  if [ "$patch" != "204" ] && [ "$patch" != "200" ]; then
    return 1
  fi
}

tus_upload "wp-content/themes/justccell-theme/functions.php" "${THEME}/functions.php"
tus_upload "wp-content/themes/justccell-theme/style.css" "${THEME}/style.css"
tus_upload "wp-content/themes/justccell-theme/inc/age-gate.php" "${THEME}/inc/age-gate.php"
tus_upload "wp-content/themes/justccell-theme/template-parts/chrome/age-gate.php" "${THEME}/template-parts/chrome/age-gate.php"
tus_upload "wp-content/themes/justccell-theme/assets/js/age-gate.js" "${THEME}/assets/js/age-gate.js"
tus_upload "wp-content/themes/justccell-theme/assets/css/chrome.css" "${THEME}/assets/css/chrome.css"
tus_upload "wp-content/themes/justccell-theme/acf-json/group_jc_storefront.json" "${THEME}/acf-json/group_jc_storefront.json"

echo "ALL_OK"
