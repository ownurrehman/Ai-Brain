#!/bin/bash
# Quick health scan - run diagnostics on known issues
# April 27, 2026

echo "=== DNS Check ==="
nslookup api.search.brave.com 8.8.8.8 2>&1 | grep -E "Address|Name|server"
echo ""
echo "=== Firecrawl Config ==="
grep -A5 "firecrawl" ~/.openclaw/openclaw.json | head -10
echo ""
echo "=== Gateway Status ==="
openclaw gateway status 2>/dev/null || echo "gateway status check failed"
echo ""
echo "=== Model Check ==="
echo "gemma4:31b available:"
curl -s http://localhost:11434/api/tags 2>/dev/null | python3 -c "import sys,json;d=json.load(sys.stdin);[print(m['name']) for m in d.get('models',[])]" 2>/dev/null || echo "ollama not accessible"
