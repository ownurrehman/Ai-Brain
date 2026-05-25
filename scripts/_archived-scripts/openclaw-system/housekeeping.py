import os
import shutil
from pathlib import Path

def main():
    base_dir = Path(__file__).parent.parent
    
    # Directories to ensure exist
    dirs_to_create = [
        base_dir / "system" / "scripts",
        base_dir / "headless-browser-scripts",
        base_dir / "reports",
        base_dir / "state" / "temp",
        base_dir / "logs",
        base_dir / "knowledge",
        base_dir / "projects"
    ]
    
    for d in dirs_to_create:
        d.mkdir(parents=True, exist_ok=True)

    # Core files that should NEVER be moved
    core_files = {
        "AGENTS.md", "MEMORY.md", "MEMORY.md.bak", "SOUL.md", "IDENTITY.md", 
        "USER.md", "TOOLS.md", "DREAMS.md", "HEARTBEAT.md", 
        "self-audit-protocol.md", "WORKSPACE-README.md", "1"
    }

    # Iterate through all files in the root of openclaw
    moved_count = 0
    for file_path in base_dir.iterdir():
        if not file_path.is_file():
            continue
            
        filename = file_path.name
        
        # Skip hidden files and core files
        if filename.startswith('.') or filename in core_files:
            continue
            
        target_dir = None
        
        # Rule 1: Reports
        if "report" in filename.lower():
            target_dir = base_dir / "reports"
        
        # Rule 2: Scripts
        elif filename.endswith('.js'):
            target_dir = base_dir / "headless-browser-scripts"
        elif filename.endswith('.py') or filename.endswith('.php'):
            target_dir = base_dir / "system" / "scripts"
            
        # Rule 3: Temp files and state
        elif filename.startswith('temp_') or filename == 'post_update.json':
            target_dir = base_dir / "state" / "temp"
            
        # Rule 4: Screenshots / Logs
        elif filename.endswith('.png') or filename.endswith('.jpg'):
            target_dir = base_dir / "logs"
            
        # Rule 5: Knowledge / Projects (Markdown files that are not core)
        elif filename.endswith('.md'):
            if "protocol" in filename.lower() or "tonicphysio" in filename.lower():
                target_dir = base_dir / "projects"
            else:
                target_dir = base_dir / "knowledge"

        if target_dir:
            try:
                shutil.move(str(file_path), str(target_dir / filename))
                print(f"Moved: {filename} -> {target_dir.relative_to(base_dir)}")
                moved_count += 1
            except Exception as e:
                print(f"Error moving {filename}: {e}")

    if moved_count == 0:
        print("Workspace is clean! No files needed moving.")
    else:
        print(f"Housekeeping complete. Moved {moved_count} files.")

if __name__ == "__main__":
    main()
