#!/bin/bash

# Working IP and domain
WP_IP="145.79.24.231"
WP_DOMAIN="tonicphysio.com"
WP_BASE="https://${WP_DOMAIN}"
WP_USER="Dan"
WP_PASS='RR#Tonic@2026'
WORKSPACE="/Users/sheikhown/.openclaw/workspace"

# Images to upload
IMAGES=(
  "pediatric-physiotherapy-why-choose.jpg"
  "pediatric-physiotherapy-solutions.jpg"
  "orthopedic-physiotherapy-why-choose.jpg"
  "orthopedic-physiotherapy-solutions.jpg"
)

# Cookie file
COOKIE_FILE="/tmp/wp-cookies-curl.txt"

echo "=== TonicPhysio WordPress Upload via cURL ==="
echo "Working IP: ${WP_IP}"

# Step 1: Get initial cookies (test cookie)
echo "[1] Getting initial cookies..."
curl -s --ciphers ECDHE+AESGCM:ECDHE+CHACHA20 \
  --resolve "${WP_DOMAIN}:443:${WP_IP}" \
  "${WP_BASE}/wp-login.php" \
  -c "${COOKIE_FILE}" \
  -o /dev/null

echo "[2] Attempting login..."

# Step 2: Login and get auth cookies
# The password needs proper handling - let's use a POST with proper encoding
LOGIN_RESPONSE=$(curl -s --ciphers ECDHE+AESGCM:ECDHE+CHACHA20 \
  --resolve "${WP_DOMAIN}:443:${WP_IP}" \
  -X POST "${WP_BASE}/wp-login.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "log=$(echo -n "${WP_USER}" | jq -sRr @uri)" \
  -d "pwd=$(echo -n "${WP_PASS}" | jq -sRr @uri)" \
  -d "wp-submit=Log+In" \
  -d "redirect_to=$(echo -n "${WP_BASE}/wp-admin/" | jq -sRr @uri)" \
  -d "testcookie=1" \
  -b "${COOKIE_FILE}" \
  -c "${COOKIE_FILE}" \
  -D /tmp/wp-login-headers.txt \
  -o /tmp/wp-login-body.txt)

echo "Login response headers:"
cat /tmp/wp-login-headers.txt | grep -i "set-cookie\|location\|http/"

# Check if login succeeded by looking for wordpress_logged_in cookie
if grep -q "wordpress_logged_in" "${COOKIE_FILE}"; then
  echo "[2] Login SUCCESS - auth cookie received"
  grep "wordpress_logged_in" "${COOKIE_FILE}"
else
  echo "[2] Login FAILED - no auth cookie"
  echo "Response body preview:"
  head -c 500 /tmp/wp-login-body.txt
  echo ""
  echo "Trying alternative login method..."
  
  # Try with explicit password handling
  curl -s --ciphers ECDHE+AESGCM:ECDHE+CHACHA20 \
    --resolve "${WP_DOMAIN}:443:${WP_IP}" \
    -X POST "${WP_BASE}/wp-login.php" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    --data-urlencode "log=${WP_USER}" \
    --data-urlencode "pwd=${WP_PASS}" \
    --data-urlencode "wp-submit=Log In" \
    --data-urlencode "redirect_to=${WP_BASE}/wp-admin/" \
    --data-urlencode "testcookie=1" \
    -b "${COOKIE_FILE}" \
    -c "${COOKIE_FILE}" \
    -D /tmp/wp-login-headers2.txt \
    -o /tmp/wp-login-body2.txt
  
  if grep -q "wordpress_logged_in" "${COOKIE_FILE}"; then
    echo "[2] Alternative login SUCCESS"
  else
    echo "[2] Alternative login also FAILED"
    grep -i "error" /tmp/wp-login-body2.txt | head -5
    exit 1
  fi
fi

echo ""
echo "[3] Uploading images..."

# Step 3: Upload images via REST API
declare -A MEDIA_IDS

for img in "${IMAGES[@]}"; do
  echo "[3] Uploading ${img}..."
  FILE_PATH="${WORKSPACE}/${img}"
  
  if [ ! -f "${FILE_PATH}" ]; then
    echo "  ERROR: File not found: ${FILE_PATH}"
    continue
  fi
  
  # Get nonce from admin page
  NONCE=$(curl -s --ciphers ECDHE+AESGCM:ECDHE+CHACHA20 \
    --resolve "${WP_DOMAIN}:443:${WP_IP}" \
    -b "${COOKIE_FILE}" \
    "${WP_BASE}/wp-admin/media-new.php" | \
    grep -oP '_wpnonce" value="\K[^"]+' | head -1)
  
  echo "  Nonce: ${NONCE:0:20}..."
  
  # Upload via async-upload.php
  UPLOAD_RESPONSE=$(curl -s --ciphers ECDHE+AESGCM:ECDHE+CHACHA20 \
    --resolve "${WP_DOMAIN}:443:${WP_IP}" \
    -X POST "${WP_BASE}/wp-admin/async-upload.php" \
    -b "${COOKIE_FILE}" \
    -H "Content-Type: multipart/form-data" \
    -F "action=upload-attachment" \
    -F "_wpnonce=${NONCE}" \
    -F "name=${img}" \
    -F "file=@${FILE_PATH}" \
    -F "_wp_http_referer=/wp-admin/media-new.php")
  
  echo "  Upload response preview: ${UPLOAD_RESPONSE:0:200}"
  
  # Extract media ID from response
  # WordPress returns JSON with the attachment ID
  MEDIA_ID=$(echo "${UPLOAD_RESPONSE}" | grep -oP '"id"\s*:\s*\K\d+' | head -1)
  
  if [ -z "${MEDIA_ID}" ]; then
    # Try alternative extraction from HTML response
    MEDIA_ID=$(echo "${UPLOAD_RESPONSE}" | grep -oP 'post=(\d+)' | head -1 | grep -oP '\d+')
  fi
  
  if [ -n "${MEDIA_ID}" ]; then
    MEDIA_IDS["${img}"]="${MEDIA_ID}"
    echo "  ✓ ${img} => Media ID ${MEDIA_ID}"
  else
    echo "  ✗ Could not extract media ID for ${img}"
    echo "  Full response: ${UPLOAD_RESPONSE}"
  fi
  
  sleep 2
done

echo ""
echo "=== Media IDs ==="
for key in "${!MEDIA_IDS[@]}"; do
  echo "${key}: ${MEDIA_IDS[$key]}"
done

# Save media IDs to file for the browser automation script
cat > /tmp/wp-media-ids.json << EOF
{
$(for key in "${!MEDIA_IDS[@]}"; do
  echo "  \"${key}\": \"${MEDIA_IDS[$key]}\","
done | sed '$ s/,$//')
}
EOF

echo ""
echo "Media IDs saved to /tmp/wp-media-ids.json"
echo "Cookies saved to ${COOKIE_FILE}"
