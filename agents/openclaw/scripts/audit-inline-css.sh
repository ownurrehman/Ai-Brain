#!/bin/bash
cd "$HOME/Ai Works - Local/Ai Codes/Ai Brain/openclaw/projects/rankray/Rank Ray HQ/rankray-hq-frontend"

echo "=== AUDIT: Hardcoded inline styles across modules ==="
echo ""

for module in finance hrm crm automation tasks projects seo publishing marketing dashboard inbox analytics admin billing auth; do
  if [ -d "src/modules/$module" ]; then
    count=$(grep -rn "font-black\|tracking-tighter\|tracking-widest\|uppercase tracking\|text-\[8px\]\|text-\[9px\]\|text-\[10px\]\|text-\[11px\]\|shadow-\[0_0\|shadow-2xl" src/modules/$module --include="*.tsx" 2>/dev/null | grep -v node_modules | wc -l)
    if [ "$count" -gt 0 ]; then
      echo "⚠️  $module: $count issues"
      grep -rn "font-black\|tracking-tighter\|tracking-widest\|uppercase tracking\|text-\[8px\]\|text-\[9px\]\|text-\[10px\]\|text-\[11px\]\|shadow-\[0_0\|shadow-2xl" src/modules/$module --include="*.tsx" 2>/dev/null | grep -v node_modules | head -5
      echo ""
    fi
  fi
done

echo "=== Modules with no issues ==="
for module in finance hrm crm automation tasks projects seo publishing marketing dashboard inbox analytics admin billing auth; do
  if [ -d "src/modules/$module" ]; then
    count=$(grep -rn "font-black\|tracking-tighter\|tracking-widest\|uppercase tracking\|text-\[8px\]\|text-\[9px\]\|text-\[10px\]\|text-\[11px\]\|shadow-\[0_0\|shadow-2xl" src/modules/$module --include="*.tsx" 2>/dev/null | grep -v node_modules | wc -l)
    if [ "$count" -eq 0 ]; then
      echo "✅ $module"
    fi
  fi
done
