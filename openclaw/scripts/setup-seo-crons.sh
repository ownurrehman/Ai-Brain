#!/usr/bin/env zsh
# Setup all SEO cron jobs for Rank Ray agency
# Run: bash scripts/setup-seo-crons.sh

set -euo pipefail

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log() { echo -e "${GREEN}[INFO]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }

# First, list existing crons
log "Current cron jobs:"
openclaw cron list 2>/dev/null || true

# Remove old broken cron if exists
log "Checking for old rankray-10pm cron..."
OLD_JOB=$(openclaw cron list 2>/dev/null | grep "rankray-10pm" | head -1 || true)
if [ -n "$OLD_JOB" ]; then
    OLD_ID=$(echo "$OLD_JOB" | awk '{print $1}')
    log "Removing old rankray-10pm (id: $OLD_ID)..."
    openclaw cron remove "$OLD_ID" 2>/dev/null || true
fi

# Function to add a cron job
add_seo_cron() {
    local name=$1
    local schedule=$2
    local site=$3
    local channel=$4
    local channel_id=$5

    log "Adding $name for $site..."

    # Build the system event text
    local event_text="SEO DAILY: Run comprehensive technical + on-page + content audit for $site. Check: 1) Indexability status 2) Meta titles/descriptions 3) H1 structure 4) Internal links 5) Core Web Vitals 6) Content freshness 7) SERP position tracking. Target channel: $channel. Post summary to channel $channel_id."

    openclaw cron add \
        --name "$name" \
        --schedule "$schedule" \
        --payload-kind systemEvent \
        --payload-text "$event_text" \
        --delivery-mode announce \
        --delivery-channel "$channel_id" \
        --session-target main \
        2>/dev/null || warn "Failed to add $name (may already exist)"
}

# Add all SEO cron jobs
# Note: Using PKT (Asia/Karachi) timezone

add_seo_cron "seo-coinsfera-11am" "0 11 * * *" "coinsfera.com" "#coinsfera" "1156145694730620928"
add_seo_cron "seo-tonicphysio-2pm" "0 14 * * *" "tonicphysio.com" "#tonicphysio" "1156322019072299068"
# khanllp.com REMOVED - CMS access lost 2026-05-14, no longer a client
add_seo_cron "seo-teammotorcycle-8pm" "0 20 * * *" "teammotorcycle.com" "#teammotorcycle" "1475806039600271472"
add_seo_cron "seo-rankray-10pm" "0 22 * * *" "rankray.com" "#rankray" "1156128279430959165"

log "All SEO cron jobs configured!"
log "Verifying..."
openclaw cron list 2>/dev/null || true

log "Done!"
