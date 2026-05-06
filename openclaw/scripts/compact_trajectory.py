#!/usr/bin/env python3
"""
Compact session trajectory by removing large data payloads.
Preserves structure but removes bulky content that causes context bloat.
"""
import json
import sys
import os
from datetime import datetime

def compact_trajectory(input_path, output_path=None):
    """Remove large data payloads from trajectory entries."""
    if output_path is None:
        output_path = input_path + ".compact"

    total_in = 0
    total_out = 0
    entries = 0
    compacted = 0

    with open(input_path, 'r') as f_in, open(output_path, 'w') as f_out:
        for line in f_in:
            entries += 1
            size_in = len(line)
            total_in += size_in

            try:
                obj = json.loads(line)

                # Compact large data payloads
                if 'data' in obj and isinstance(obj['data'], dict):
                    data = obj['data']

                    # Compact tool results with large outputs
                    if 'toolResults' in data:
                        for result in data['toolResults']:
                            if 'output' in result and isinstance(result['output'], str):
                                if len(result['output']) > 5000:
                                    result['output'] = f"[COMPACTED: {len(result['output'])} chars]"
                                    compacted += 1
                            if 'result' in result and isinstance(result['result'], str):
                                if len(result['result']) > 5000:
                                    result['result'] = f"[COMPACTED: {len(result['result'])} chars]"
                                    compacted += 1

                    # Compact large text content
                    if 'content' in data and isinstance(data['content'], list):
                        for item in data['content']:
                            if isinstance(item, dict) and 'text' in item:
                                if len(item['text']) > 10000:
                                    item['text'] = f"[COMPACTED: {len(item['text'])} chars]"
                                    compacted += 1

                    # Compact file read outputs
                    if 'fileRead' in data:
                        for fr in data['fileRead']:
                            if 'content' in fr and len(str(fr['content'])) > 5000:
                                fr['content'] = f"[COMPACTED: {len(str(fr['content']))} chars]"
                                compacted += 1

                out_line = json.dumps(obj, separators=(',', ':')) + '\n'
                size_out = len(out_line)
                total_out += size_out
                f_out.write(out_line)

            except json.JSONDecodeError:
                # Skip invalid lines
                f_out.write(line)
                total_out += size_in

    # Replace original with compacted version
    backup_path = input_path + ".backup." + datetime.now().strftime("%Y%m%d-%H%M%S")
    os.rename(input_path, backup_path)
    os.rename(output_path, input_path)

    ratio = (total_out / total_in) * 100 if total_in > 0 else 0
    print(f"Compacted {entries} entries")
    print(f"  Before: {total_in:,} bytes")
    print(f"  After:  {total_out:,} bytes")
    print(f"  Ratio:  {ratio:.1f}%")
    print(f"  Compacted {compacted} large payloads")
    print(f"  Backup: {backup_path}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python compact_trajectory.py <trajectory.jsonl>")
        sys.exit(1)

    compact_trajectory(sys.argv[1])
