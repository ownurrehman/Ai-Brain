#!/bin/bash
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/d220da807055b678/api/tus/public_html"
AUTH="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTM5MjgwODI2MCIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODcxODIxMCwiaWF0IjoxNzg4Njk2NjEwfQ.NFXgAfTucomFwuDlTSgxbEYgN78GuIoJNvN_RsrnOsA"
REST="11b8981a4b32ff566ee2c2d3b38f9573fda29f68028189eefb2ae91fedbe8181-d220da807055b678"
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
tus_upload "wp-content/themes/justccell-theme/inc/forms-settings.php" "${THEME}/inc/forms-settings.php"
tus_upload "wp-content/themes/justccell-theme/inc/inquiry.php" "${THEME}/inc/inquiry.php"
tus_upload "wp-content/themes/justccell-theme/inc/leads-admin.php" "${THEME}/inc/leads-admin.php"
tus_upload "wp-content/themes/justccell-theme/template-parts/inquiry/form-contact.php" "${THEME}/template-parts/inquiry/form-contact.php"
tus_upload "wp-content/themes/justccell-theme/template-parts/inquiry/form.php" "${THEME}/template-parts/inquiry/form.php"
tus_upload "wp-content/themes/justccell-theme/acf-json/group_jc_forms_options.json" "${THEME}/acf-json/group_jc_forms_options.json"

echo "ALL_OK"
