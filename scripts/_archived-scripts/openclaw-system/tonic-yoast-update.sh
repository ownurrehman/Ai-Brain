#!/usr/bin/env bash
# Fix Yoast SEO fields for 5 Tonic Physio pages via WordPress REST API

WP_URL="https://tonicphysio.com"
CREDS="rankrayagency@gmail.com:4isf Zcbd pvGI O1fp lQKB Jz2M"

# Page 1: B-Pulse Pelvic Floor (ID: 11603)
echo "Updating Page 1: B-Pulse Pelvic Floor (11603)..."
curl -X POST "${WP_URL}/wp-json/wp/v2/pages/11603" \
  --user "${CREDS}" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {
      "_yoast_wpseo_title": "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio",
      "_yoast_wpseo_metadesc": "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
    }
  }'
echo ""

# Page 2: Joint Pain and Stiffness (ID: 6971)
echo "Updating Page 2: Joint Pain and Stiffness (6971)..."
curl -X POST "${WP_URL}/wp-json/wp/v2/pages/6971" \
  --user "${CREDS}" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {
      "_yoast_wpseo_title": "Joint Pain Treatment Milton | Tonic Physio",
      "_yoast_wpseo_metadesc": "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
    }
  }'
echo ""

# Page 3: Orthopedic Physiotherapy (ID: 1791)
echo "Updating Page 3: Orthopedic Physiotherapy (1791)..."
curl -X POST "${WP_URL}/wp-json/wp/v2/pages/1791" \
  --user "${CREDS}" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {
      "_yoast_wpseo_title": "Orthopedic Physiotherapy Milton | Tonic Physio",
      "_yoast_wpseo_metadesc": "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
    }
  }'
echo ""

# Page 4: Pediatric Physiotherapy (ID: 1793)
echo "Updating Page 4: Pediatric Physiotherapy (1793)..."
curl -X POST "${WP_URL}/wp-json/wp/v2/pages/1793" \
  --user "${CREDS}" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {
      "_yoast_wpseo_title": "Pediatric Physiotherapy Milton | Tonic Physio",
      "_yoast_wpseo_metadesc": "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
    }
  }'
echo ""

# Page 5: Hot Stone Massage (ID: 6587)
echo "Updating Page 5: Hot Stone Massage (6587)..."
curl -X POST "${WP_URL}/wp-json/wp/v2/pages/6587" \
  --user "${CREDS}" \
  -H "Content-Type: application/json" \
  -d '{
    "meta": {
      "_yoast_wpseo_title": "Hot Stone Massage Milton | Tonic Physio",
      "_yoast_wpseo_metadesc": "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."
    }
  }'
echo ""

echo "All pages updated!"
