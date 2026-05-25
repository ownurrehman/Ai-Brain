#!/bin/bash

WP="https://tonicphysio.com/wp-json/wp/v2"
AUTH="$(echo -n 'Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ' | base64)"

echo "Pushing Blog 5..."

BLOG5_HTML=$(cat blog5_ra_physiotherapy.html)

BODY5=$(jq -n \
  --arg title "Rheumatoid Arthritis and Physiotherapy Management in Milton" \
  --arg slug "rheumatoid-arthritis-physiotherapy-milton" \
  --arg content "$BLOG5_HTML" \
  --arg yoast_title "Rheumatoid Arthritis Physiotherapy Management Milton | Tonic Physio" \
  --arg yoast_desc "Expert rheumatoid arthritis physiotherapy in Milton. Reduce joint pain with personalized care at Tonic Physio." \
  '{
    title: $title,
    slug: $slug,
    content: $content,
    status: "draft",
    meta: {
      _yoast_wpseo_title: $yoast_title,
      _yoast_wpseo_metadesc: $yoast_desc
    }
  }')

RESP5=$(curl -s -X POST "$WP/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic $AUTH" \
  -d "$BODY5")

ID5=$(echo "$RESP5" | jq -r '.id')
SLUG5=$(echo "$RESP5" | jq -r '.slug')
TITLE5=$(echo "$RESP5" | jq -r '.title.raw // .title.rendered')
LINK5=$(echo "$RESP5" | jq -r '.link')

echo "Blog 5: ID=$ID5 | Slug=$SLUG5 | Link=$LINK5"

echo ""
echo "Pushing Blog 6..."

BLOG6_HTML=$(cat blog6_deep_tissue_athletes.html)

BODY6=$(jq -n \
  --arg title "Deep Tissue Massage Benefits for Athletes in Milton" \
  --arg slug "deep-tissue-massage-benefits-athletes-milton" \
  --arg content "$BLOG6_HTML" \
  --arg yoast_title "Deep Tissue Massage Benefits for Athletes in Milton | Tonic Physio" \
  --arg yoast_desc "Deep tissue massage for athletes in Milton. Speed recovery and prevent injury at Tonic Physio." \
  '{
    title: $title,
    slug: $slug,
    content: $content,
    status: "draft",
    meta: {
      _yoast_wpseo_title: $yoast_title,
      _yoast_wpseo_metadesc: $yoast_desc
    }
  }')

RESP6=$(curl -s -X POST "$WP/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic $AUTH" \
  -d "$BODY6")

ID6=$(echo "$RESP6" | jq -r '.id')
SLUG6=$(echo "$RESP6" | jq -r '.slug')
TITLE6=$(echo "$RESP6" | jq -r '.title.raw // .title.rendered')
LINK6=$(echo "$RESP6" | jq -r '.link')

echo "Blog 6: ID=$ID6 | Slug=$SLUG6 | Link=$LINK6"

echo ""
echo "Pushing Blog 7..."

BLOG7_HTML=$(cat blog7_hot_stone_vs_swedish.html)

BODY7=$(jq -n \
  --arg title "Hot Stone Massage vs Swedish Massage: Which is Right for You" \
  --arg slug "hot-stone-massage-vs-swedish-milton" \
  --arg content "$BLOG7_HTML" \
  --arg yoast_title "Hot Stone Massage vs Swedish Massage Milton | Tonic Physio" \
  --arg yoast_desc "Hot stone vs Swedish massage in Milton. Find the right therapy for you at Tonic Physio." \
  '{
    title: $title,
    slug: $slug,
    content: $content,
    status: "draft",
    meta: {
      _yoast_wpseo_title: $yoast_title,
      _yoast_wpseo_metadesc: $yoast_desc
    }
  }')

RESP7=$(curl -s -X POST "$WP/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic $AUTH" \
  -d "$BODY7")

ID7=$(echo "$RESP7" | jq -r '.id')
SLUG7=$(echo "$RESP7" | jq -r '.slug')
TITLE7=$(echo "$RESP7" | jq -r '.title.raw // .title.rendered')
LINK7=$(echo "$RESP7" | jq -r '.link')

echo "Blog 7: ID=$ID7 | Slug=$SLUG7 | Link=$LINK7"

echo ""
echo "=== SUMMARY ==="
echo "Blog 5: ID=$ID5, Slug=$SLUG5, Title=$TITLE5, Link=$LINK5"
echo "Blog 6: ID=$ID6, Slug=$SLUG6, Title=$TITLE6, Link=$LINK6"
echo "Blog 7: ID=$ID7, Slug=$SLUG7, Title=$TITLE7, Link=$LINK7"

# If any ID is "null", print the raw response for debugging
if [ "$ID5" = "null" ] || [ "$ID6" = "null" ] || [ "$ID7" = "null" ]; then
  echo ""
  echo "ERROR: One or more posts failed to create."
  if [ "$ID5" = "null" ]; then echo "Blog 5 response: $RESP5"; fi
  if [ "$ID6" = "null" ]; then echo "Blog 6 response: $RESP6"; fi
  if [ "$ID7" = "null" ]; then echo "Blog 7 response: $RESP7"; fi
  exit 1
fi

echo ""
echo "All 3 blog posts pushed successfully!"
