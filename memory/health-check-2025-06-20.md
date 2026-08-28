> **Parent Hub:** [[memory/INDEX|🧠 Fleet Memory Hub]] · [[INDEX|🧠 Ai Brain]]

# Health Check Log — Saturday, June 20th, 2026 at 6:02 PM (Asia/Karachi)

## ⚠️ High Load Detected — System Under Pressure

### System Load (Top 5 by CPU)
| Process | PID | %CPU | Elapsed |
|---------|------|------|---------|
| Cursor Helper (Renderer) | 40053 | **33.9%** | 32:29 |
| node | 48803 | **27.4%** | 04:46 |
| sysmond | 781 | **20.2%** | 12-00:09:55 |
| Cursor Helper | 40041 | **13.7%** | 32:32 |
| WindowServer | 620 | **12.2%** | 12-00:10:03 |

### CPU Load Average
- 1 min: **5.24**
- 5 min: **4.61**
- 15 min: **5.02**
- ➜ Load average exceeds core count — sustained heavy use.

### Memory
- Total: **16 GB**
- Pages Free: 12,462 × 16 KB ≈ **~203 MB free**
- Swap Ins: 1,277,703
- Swap Outs: 2,333,172
- ➜ **High memory pressure — swapping heavily.**

### Disk Usage
- Data volume: **255 GB used / 460 GB total** (60% full)
- System volume: **12 GB used / 460 GB total** (7% full)

### Duplicate Processes
- Multiple Cursor Helper processes — legitimate (renderer + GPU + plugin hosts)
- Multiple node processes — MCP servers and dev servers, expected
- Multiple Antigravity IDE helpers — expected (Electron architecture)
- No clear duplicate processes requiring termination.

### Docker
- Running: **postgres:17-alpine** (rankray-dev-pg)
- Running: **redis:7-alpine** (rankray-redis)
- Exited: **postgres:16-alpine** (rankray-e2e-pg) — not removed per rules
- 10 Docker images present.

### Temperature
- Thermal check output heavily truncated by ioreg.
- pmset therm not available.
- Could not extract temperature readings from ioreg output.

### Warnings
1. **🚨 LOAD CRITICAL**: Load averages >5 on a system with likely 8–10 cores means sustained heavy utilization.
2. **🚨 MEMORY PRESSURE**: Only ~203 MB free, heavy swap activity. This is causing slowdowns.
3. **🔥 CURSOR RENDERER**: 33.9% CPU — Cursor IDE renderer consuming significant resources. Likely due to active editing / large project.
4. **NODE HIGH CPU**: One node process at 27.4% — likely a dev server or build process.
5. **THERMAL UNKNOWN**: ioreg returned massive structured data — temperature parsing failed. Suggest running `sudo powermetrics --samplers smc -n 1` manually for thermal.

### Actions Taken
- None. Monitoring only. No files deleted, no processes killed, no cache cleared.

### Recommendations
- Consider closing unused browser tabs and applications to relieve memory pressure.
- Restart Cursor IDE if it continues consuming >30% CPU consistently.
- Run manual thermal check if fan noise is high: `sudo powermetrics --samplers smc -n 1 | grep -i "temperature\|fan"`
