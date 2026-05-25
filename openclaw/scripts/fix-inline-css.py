#!/usr/bin/env python3
"""
Bulk fix inline CSS issues across Rank Ray HQ modules.
Replaces hardcoded/extreme styling with design system tokens.
"""
import os
import re
import sys
from pathlib import Path

REPLACEMENTS = [
    # font-black → font-bold
    (r'\bfont-black\b', 'font-bold'),
    
    # tracking-tighter → tracking-tight
    (r'\btracking-tighter\b', 'tracking-tight'),
    
    # Remove uppercase tracking-widest patterns (military jargon style)
    (r'\s+uppercase tracking-widest', ''),
    (r'\s+uppercase tracking-tight', ''),
    (r'\s+uppercase tracking-tighter', ''),
    
    # Custom shadows → standard shadows
    (r'shadow-\[0_0_20px_rgba\(var\(--primary\),0\.3\)\]', 'shadow-sm'),
    (r'shadow-\[0_0_8px_rgba\(16,185,129,0\.5\)\]', 'shadow-sm'),
    (r'shadow-2xl shadow-primary/20', 'shadow-sm'),
    (r'shadow-2xl shadow-primary/5', 'shadow-sm'),
    (r'shadow-2xl shadow-primary/10', 'shadow-sm'),
    (r'shadow-2xl', 'shadow-sm'),
    
    # text-[8px] / text-[9px] → text-xs
    (r'text-\[8px\]', 'text-xs'),
    (r'text-\[9px\]', 'text-xs'),
    
    # text-[10px] / text-[11px] → text-xs (or text-sm for slightly larger)
    (r'text-\[10px\]', 'text-xs'),
    (r'text-\[11px\]', 'text-xs'),
    
    # Gray colors → muted-foreground
    (r'text-gray-500', 'text-muted-foreground'),
    (r'text-gray-400', 'text-muted-foreground'),
    (r'text-gray-600', 'text-muted-foreground'),
    
    # Slate backgrounds in automation → use tokens
    (r'bg-slate-900/40', 'bg-muted/30'),
    (r'border-slate-800/60', 'border-border/60'),
    
    # Clean up double spaces
    (r'  +', ' '),
]

def fix_file(filepath):
    """Apply replacements to a single file."""
    with open(filepath, 'r') as f:
        content = f.read()
    
    original = content
    for pattern, replacement in REPLACEMENTS:
        content = re.sub(pattern, replacement, content)
    
    if content != original:
        with open(filepath, 'w') as f:
            f.write(content)
        return True
    return False

def main():
    base = Path.home() / "Ai Works - Local/Ai Codes/Ai Brain/openclaw/projects/rankray/Rank Ray HQ/rankray-hq-frontend/src/modules"
    
    if not base.exists():
        print(f"ERROR: {base} not found")
        sys.exit(1)
    
    total_files = 0
    total_changes = 0
    
    for module_dir in sorted(base.iterdir()):
        if not module_dir.is_dir():
            continue
        
        module_name = module_dir.name
        changed_files = []
        
        for tsx_file in module_dir.rglob("*.tsx"):
            if "node_modules" in str(tsx_file):
                continue
            if fix_file(tsx_file):
                changed_files.append(tsx_file.name)
                total_files += 1
        
        if changed_files:
            print(f"✅ {module_name}: {len(changed_files)} files")
            total_changes += len(changed_files)
    
    print(f"\nTotal: {total_changes} files changed")
    
    # Run TypeScript check
    print("\nRunning TypeScript check...")
    os.chdir(base.parent.parent)  # Go to project root
    result = os.system("npx tsc --noEmit 2>&1 | tail -20")
    
if __name__ == "__main__":
    main()
