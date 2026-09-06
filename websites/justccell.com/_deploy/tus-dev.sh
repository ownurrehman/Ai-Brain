#!/bin/bash
# Deploy theme files to dev.justccell.com (public_html/dev/)
# Usage: fill AUTH and REST from hosting_generateUploadURLV1, then run.
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/REPLACE_ME/api/tus/public_html"
AUTH="REPLACE_JWT"
REST="REPLACE_REST_KEY"
THEME="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com/justccell-theme"
PREFIX="dev/wp-content/themes/justccell-theme"

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

# Example batch — edit file list per ship:
tus_upload "${PREFIX}/functions.php" "${THEME}/functions.php"
tus_upload "${PREFIX}/style.css" "${THEME}/style.css"

echo "ALL_OK"
