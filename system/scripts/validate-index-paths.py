#!/usr/bin/env python3
"""
Ai Brain INDEX.md Path Validator
Checks ONLY paths that are literally written inside INDEX.md.
Does NOT walk filesystem — reads what INDEX.md says and verifies those exact paths exist.
"""
import re, os, sys

INDEX = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/INDEX.md"
LOG = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/config/path-audit-log.md"

if not os.path.exists(INDEX):
    print(f"FATAL: INDEX.md not found at {INDEX}")
    sys.exit(1)

with open(INDEX, 'r') as f:
    content = f.read()

# Extract paths inside markdown code blocks and backticks
# Only extract full file/directory paths under /Users/...
paths = set()

# Match paths in backticks like `/Users/.../something.md`
for m in re.finditer(r'`(/Users/[^`]+)`', content):
    p = m.group(1).rstrip('/')
    if p:
        paths.add(p)

# Match markdown table cells that look like paths
for m in re.finditer(r'\| *`?(/Users/[^|`\n]+)`? *\|', content):
    p = m.group(1).rstrip('/') if m.group(1) else None
    if p:
        paths.add(p)

# Verify each path
missing = []
for p in sorted(paths):
    if not os.path.exists(p):
        missing.append(p)

# Write audit log
log_lines = ["# Ai Brain Path Audit", f""]
if missing:
    log_lines.append(f"**MISSING PATHS ({len(missing)}):**")
    for m in missing:
        log_lines.append(f"- [ ] `{m}`")
    log_lines.append("")
    log_lines.append("**ACTION REQUIRED:** Update agent memory with corrected paths from INDEX.md.")
else:
    log_lines.append("All referenced paths verified alive. No action needed.")

with open(LOG, 'w') as f:
    f.write('\n'.join(log_lines) + '\n')

if missing:
    print(f"ALERT: {len(missing)} missing paths found. See {LOG}")
    for m in missing:
        print(f"  MISSING: {m}")
    sys.exit(1)
else:
    print(f"All {len(paths)} paths verified. Ai Brain structure healthy.")
    sys.exit(0)
