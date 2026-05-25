import os
import json
import time
from datetime import datetime, timedelta
from pathlib import Path
from typing import List, Dict, Any, Tuple, Optional, NamedTuple

class FileInfo(NamedTuple):
    path: str
    size: int

class WorkspaceAuditor:
    """Handles the auditing logic for the workspace, config, and memory."""
    
    def __init__(self, workspace_root: str, config_path: str):
        self.workspace_root = Path(workspace_root)
        self.config_path = Path(config_path)

    def audit_system(self) -> Dict[str, Any]:
        """Calculates total size, top 10 largest files, and finds empty directories."""
        total_size = 0
        all_files: List[FileInfo] = []
        empty_dirs: List[str] = []

        try:
            for root, dirs, files in os.walk(self.workspace_root):
                # Use os.scandir internally via os.walk for efficiency in newer Python versions, 
                # but for explicit 'Extreme Engineering', let's implement a manual recursive scan with scandir.
                pass
        except Exception as e:
            return {"error": f"System audit failed: {e}"}

        # Redoing with a manual recursive scan using os.scandir for high performance
        def scan_dir(path: Path):
            nonlocal total_size
            try:
                with os.scandir(path) as it:
                    is_empty = True
                    for entry in it:
                        is_empty = False
                        if entry.is_dir(follow_symlinks=False):
                            scan_dir(Path(entry.path))
                        elif entry.is_file(follow_symlinks=False):
                            f_size = entry.stat().st_size
                            total_size += f_size
                            all_files.append(FileInfo(entry.path, f_size))
                    
                    if is_empty:
                        empty_dirs.append(str(path))
            except PermissionError:
                pass # Skip restricted directories

        scan_dir(self.workspace_root)
        
        # Top 10 largest files
        top_files = sorted(all_files, key=lambda x: x.size, reverse=True)[:10]
        
        return {
            "total_size_bytes": total_size,
            "top_files": top_files,
            "empty_dirs": empty_dirs
        }

    def audit_config(self) -> Dict[str, Any]:
        """Parses openclaw.json and identifies inconsistencies in model mappings or fallbacks."""
        if not self.config_path.exists():
            return {"error": f"Config file not found at {self.config_path}"}

        try:
            with open(self.config_path, 'r') as f:
                config = json.load(f)
            
            issues = []
            # Example logic for 'inconsistencies': 
            # Check if 'agents' mapping has models that aren't in a 'models' list or lack fallbacks.
            # Since the exact schema is unknown, we'll look for common patterns like missing 'fallback' keys 
            # in agent configurations if they are defined.
            
            agents = config.get("agents", {})
            if not agents:
                issues.append("No agents defined in configuration.")
            
            for agent_id, details in agents.items():
                if not isinstance(details, dict):
                    issues.append(f"Agent {agent_id} configuration is not a dictionary.")
                    continue
                
                if "model" not in details:
                    issues.append(f"Agent {agent_id} is missing a primary model mapping.")
                if "fallback" not in details:
                    issues.append(f"Agent {agent_id} is missing a fallback model.")

            return {
                "config_parsed": True,
                "issues": issues,
                "agent_count": len(agents)
            }
        except json.JSONDecodeError as e:
            return {"error": f"Invalid JSON in config file: {e}"}
        except Exception as e:
            return {"error": f"Config audit failed: {e}"}

    def audit_memory(self) -> Dict[str, Any]:
        """Scans the memory/ directory for files untouched in over 30 days."""
        memory_dir = self.workspace_root / "memory"
        if not memory_dir.exists() or not memory_dir.is_dir():
            return {"error": "Memory directory does not exist.", "stale_files": []}

        stale_files = []
        thirty_days_ago = time.time() - (30 * 24 * 60 * 60)

        try:
            with os.scandir(memory_dir) as it:
                for entry in it:
                    if entry.is_file():
                        mtime = entry.stat().st_mtime
                        if mtime < thirty_days_ago:
                            stale_files.append({
                                "path": entry.path,
                                "last_modified": datetime.fromtimestamp(mtime).strftime('%Y-%m-%d')
                            })
            return {
                "stale_files": stale_files,
                "total_memory_files": len(list(memory_dir.iterdir()))
            }
        except Exception as e:
            return {"error": f"Memory audit failed: {e}", "stale_files": []}

class ReportGenerator:
    """Generates a polished Markdown report from audit results."""

    @staticmethod
    def format_size(bytes_size: int) -> str:
        for unit in ['B', 'KB', 'MB', 'GB', 'TB']:
            if bytes_size < 1024:
                return f"{bytes_size:.2f} {unit}"
            bytes_size /= 1024
        return f"{bytes_size:.2f} PB"

    def generate(self, data: Dict[str, Any], output_path: str):
        system = data.get("system", {})
        config = data.get("config", {})
        memory = data.get("memory", {})

        report = ["# 🚀 Workspace Health Dashboard", f"**Generated on:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"]

        # System Audit Section
        report.append("## 🖥️ System Audit")
        if "error" in system:
            report.append(f"❌ {system['error']}")
        else:
            report.append(f"- **Total Workspace Size:** {self.format_size(system['total_size_bytes'])}")
            report.append("\n### 📁 Top 10 Largest Files")
            report.append("| File Path | Size |")
            report.append("| :--- | :--- |")
            for f in system['top_files']:
                report.append(f"| `{f.path}` | {self.format_size(f.size)} |")
            
            report.append("\n### 📭 Empty Directories")
            if system['empty_dirs']:
                for d in system['empty_dirs']:
                    report.append(f"- `{d}`")
            else:
                report.append("None found.")

        # Config Audit Section
        report.append("\n## ⚙️ Config Audit")
        if "error" in config:
            report.append(f"❌ {config['error']}")
        else:
            report.append(f"- **Agents Configured:** {config['agent_count']}")
            report.append("\n### ⚠️ Inconsistencies/Issues")
            if config['issues']:
                for issue in config['issues']:
                    report.append(f"- {issue}")
            else:
                report.append("✅ No inconsistencies found.")

        # Memory Audit Section
        report.append("\n## 🧠 Memory Audit")
        if "error" in memory:
            report.append(f"ℹ️ {memory['error']}")
        else:
            report.append(f"- **Total Memory Files:** {memory['total_memory_files']}")
            report.append("\n### ⏳ Stale Files (>30 Days)")
            if memory['stale_files']:
                report.append("| File Path | Last Modified |")
                report.append("| :--- | :--- |")
                for f in memory['stale_files']:
                    report.append(f"| `{f['path']}` | {f['last_modified']} |")
            else:
                report.append("✅ No stale files found.")

        with open(output_path, 'w') as f:
            f.write("\n".join(report))

def main():
    # Constants
    WORKSPACE_ROOT = "/Users/sheikhown/.openclaw/workspace/nemo"
    CONFIG_PATH = "/Users/sheikhown/.openclaw/openclaw.json"
    REPORT_PATH = "workspace_health_report.md"

    auditor = WorkspaceAuditor(WORKSPACE_ROOT, CONFIG_PATH)
    generator = ReportGenerator()

    print("Running System Audit...")
    system_results = auditor.audit_system()
    
    print("Running Config Audit...")
    config_results = auditor.audit_config()
    
    print("Running Memory Audit...")
    memory_results = auditor.audit_memory()

    final_data = {
        "system": system_results,
        "config": config_results,
        "memory": memory_results
    }

    print(f"Generating report: {REPORT_PATH}...")
    generator.generate(final_data, REPORT_PATH)
    print("Done!")

if __name__ == "__main__":
    main()
