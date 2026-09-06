#!/bin/bash
# Deploy dev-only mu-plugin (cache bypass). NEVER run against production path.
set -euo pipefail
BASE="https://srv1914-files.hstgr.io/rest/REPLACE_ME/api/tus/public_html"
AUTH="REPLACE_JWT"
REST="REPLACE_REST_KEY"
SRC="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com/dev-mu-plugins/justccell-dev-environment.php"
REL="dev/wp-content/mu-plugins/justccell-dev-environment.php"

size=$(stat -f%z "$SRC")
post=$(curl -sS -o /dev/null -w "%{http_code}" -X POST "${BASE}/${REL}?override=true" \
  -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
  -H "Tus-Resumable: 1.0.0" -H "Upload-Length: ${size}" -H "Upload-Offset: 0")
patch=$(curl -sS -o /dev/null -w "%{http_code}" -X PATCH "${BASE}/${REL}?override=true" \
  -H "X-Auth: ${AUTH}" -H "X-Auth-Rest: ${REST}" \
  -H "Tus-Resumable: 1.0.0" -H "Content-Type: application/offset+octet-stream" \
  -H "Upload-Offset: 0" --data-binary "@${SRC}")
echo "${REL} -> post:${post} patch:${patch}"
echo "ALL_OK"
