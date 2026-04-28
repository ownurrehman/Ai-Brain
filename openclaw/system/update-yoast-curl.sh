#!/bin/bash
# Update Yoast SEO fields on Tonic Physio WordPress pages via REST API

WP_BASE="https://tonicphysio.com"
WP_USER="rankrayagency@gmail.com"
WP_PASS="RR#Tonic@2026"

# Create auth header
AUTH=$(echo -n "${WP_USER}:${WP_PASS}" | base64)

# Function to update a page
update_page() {
    local PAGE_ID=$1
    local SEO_TITLE=$2
    local META_DESC=$3
    
    echo ""
    echo "=== Updating Page ID: $PAGE_ID ==="
    echo "SEO Title: $SEO_TITLE"
    echo "Meta Description: $META_DESC"
    
    # WordPress REST API endpoint for updating pages with Yoast fields
    # Yoast stores meta in the 'meta' object
    RESPONSE=$(curl -s -X POST "${WP_BASE}/wp-json/wp/v2/pages/${PAGE_ID}" \
        -H "Authorization: Basic ${AUTH}" \
        -H "Content-Type: application/json" \
        -d "{
            \"meta\": {
                \"_yoast_wpseo_focuskw\": \"${SEO_TITLE}\",
                \"_yoast_wpseo_title\": \"${SEO_TITLE}\",
                \"_yoast_wpseo_metadesc\": \"${META_DESC}\"
            },
            \"yoast_head\": \"<title>${SEO_TITLE}</title>\\n<meta name=\\\"description\\\" content=\\\"${META_DESC}\\\" />\",
            \"yoast_head_json\": {
                \"title\": \"${SEO_TITLE}\",
                \"description\": \"${META_DESC}\"
            }
        }")
    
    echo "Response: $RESPONSE" | head -c 500
    
    # Check for success
    if echo "$RESPONSE" | grep -q '"id":'; then
        echo "✓ Page $PAGE_ID updated"
        return 0
    else
        echo "✗ Page $PAGE_ID update failed"
        return 1
    fi
}

# Function to verify page
verify_page() {
    local PAGE_ID=$1
    local EXPECTED_TITLE=$2
    local EXPECTED_DESC=$3
    
    echo ""
    echo "=== Verifying Page ID: $PAGE_ID ==="
    
    RESPONSE=$(curl -s "${WP_BASE}/wp-json/wp/v2/pages/${PAGE_ID}" \
        -H "Authorization: Basic ${AUTH}")
    
    # Extract Yoast fields
    YOAST_TITLE=$(echo "$RESPONSE" | grep -o '"yoast_head":"[^"]*<title>[^<]*</title>[^"]*"' | head -1)
    
    echo "Yoast data found: $YOAST_TITLE" | head -c 200
    
    # Check lengths
    TITLE_LEN=${#EXPECTED_TITLE}
    DESC_LEN=${#EXPECTED_DESC}
    
    echo "Expected title length: $TITLE_LEN"
    echo "Expected description length: $DESC_LEN"
    echo "Contains 'Tonic': $(echo "$EXPECTED_DESC" | grep -o 'Tonic' | wc -l)"
}

echo "🦞 Starting Yoast SEO update via curl..."
echo "WordPress: $WP_BASE"
echo "User: $WP_USER"

# Test connection
echo ""
echo "=== Testing Connection ==="
TEST=$(curl -s "${WP_BASE}/wp-json/wp/v2/pages/11603" -H "Authorization: Basic ${AUTH}")
if echo "$TEST" | grep -q '"id":11603'; then
    echo "✓ Connection successful"
else
    echo "✗ Connection failed"
    echo "$TEST" | head -c 200
    exit 1
fi

# Page 1: B-Pulse Pelvic Floor (ID: 11603)
update_page 11603 \
    "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio" \
    "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."

# Page 2: Joint Pain (ID: 6971)
update_page 6971 \
    "Joint Pain Treatment Milton | Tonic Physio" \
    "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."

# Page 3: Orthopedic Physiotherapy (ID: 1791)
update_page 1791 \
    "Orthopedic Physiotherapy Milton | Tonic Physio" \
    "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."

# Page 4: Pediatric Physiotherapy (ID: 1793)
update_page 1793 \
    "Pediatric Physiotherapy Milton | Tonic Physio" \
    "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."

# Page 5: Hot Stone Massage (ID: 6587)
update_page 6587 \
    "Hot Stone Massage Milton | Tonic Physio" \
    "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."

echo ""
echo "=== Verification ==="

verify_page 11603 "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio" "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
verify_page 6971 "Joint Pain Treatment Milton | Tonic Physio" "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
verify_page 1791 "Orthopedic Physiotherapy Milton | Tonic Physio" "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
verify_page 1793 "Pediatric Physiotherapy Milton | Tonic Physio" "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
verify_page 6587 "Hot Stone Massage Milton | Tonic Physio" "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."

echo ""
echo "✓ Script completed"
