#!/bin/zsh
# Parallel SEO Subagent Runner for OpenClaw Cron
# Usage: script <domain> <type> <location> <service-page>

DOMAIN=$1
TYPE=$2
LOCATION=$3
SERVICE_PAGE=$4
DATE=$(date +%Y-%m-%d)
REPORT_DIR="$HOME/.openclaw/workspace/reports/$(echo $DOMAIN | tr '.' '-')"
CACHE_DIR="$HOME/.openclaw/workspace/.subagent-cache"

mkdir -p "$REPORT_DIR" "$CACHE_DIR"

# Clear previous run caches for this domain
rm -f "$CACHE_DIR/${DOMAIN}-"*.txt 2>/dev/null

echo "=== Starting Parallel SEO Analysis for $DOMAIN ==="
echo "Time: $(date)"
echo ""

# Note: In actual OpenClaw execution, these would be sessions_spawn calls
# For now, this serves as the script structure

cat << 'INSTRUCTIONS'

EXECUTE THESE INSTRUCTIONS:

1. Spawn 4 subagents in parallel using sessions_spawn:

Subagent A: 
- Read from: agents/homepage-technical.md
- Pass: domain="$DOMAIN", location="$LOCATION"
- Save output to: .subagent-cache/${DOMAIN}-homepage.txt

Subagent B:
- Read from: agents/keyword-intelligence.md
- Pass: domain="$DOMAIN", type="$TYPE"
- Save output to: .subagent-cache/${DOMAIN}-keywords.txt

Subagent C:
- Read from: agents/internal-linking.md
- Pass: domain="$DOMAIN", service_pages="$SERVICE_PAGE"
- Save output to: .subagent-cache/${DOMAIN}-links.txt

Subagent D:
- Read from: agents/service-page-deep.md
- Pass: domain="$DOMAIN", service_page="$SERVICE_PAGE"
- Save output to: .subagent-cache/${DOMAIN}-service.txt

2. Wait maximum 5 minutes for all to complete

3. Read all 4 cache files and compile into:
   reports/${DOMAIN}/${DATE}.md

4. Generate WhatsApp summary (under 400 words)

END_TIME=$(date)
echo "=== Parallel Run Complete ==="
echo "Report: $REPORT_DIR/${DATE}.md"
echo "Duration: $(date -d @$(($(date +%s) - $(date -d "$(date)" +%s))) +%M:%S minutes)"

INSTRUCTIONS