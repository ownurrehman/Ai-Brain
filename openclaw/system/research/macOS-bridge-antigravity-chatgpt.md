
    override init() {
        super.init()
        Timer.scheduledTimer(withTimeInterval: 0.2, repeats: true) { _ in
            self.checkClipboard()
        }
        RunLoop.main.run()
    }
    
    func checkClipboard() {
        if pasteboard.changeCount != lastChangeCount {
            lastChangeCount = pasteboard.changeCount
            
            if let content = pasteboard.string(forType: .string) {
                processContent(content)
            }
        }
    }
    
    func processContent(_ content: String) {
        if content.hasPrefix("#gpt ") {
            let cleanContent = content.replacingOccurrences(of: "#gpt ", with: "")
            let formatted = "```\n\(cleanContent)\n```\n\nPlease analyze this code."
            
            pasteboard.clearContents()
            pasteboard.setString(formatted, forType: .string)
            
            // Activate ChatGPT
            NSWorkspace.shared.openApplication(at: URL(fileURLWithPath: "/Applications/ChatGPT.app"), configuration: NSWorkspace.OpenConfiguration())
            
            // Send keystrokes via AppleScript
            let script = """
            tell application "System Events"
                keystroke "v" using command down
                keystroke return
            end tell
            """
            var error: NSDictionary?
            if let appleScript = NSAppleScript(source: script) {
                appleScript.executeAndReturnError(&error)
            }
        }
    }
}

ClipboardMonitor()
```

### 3.3 Using NSPasteboard with Change Count

```python
# More efficient monitoring using changeCount
from AppKit import NSPasteboard

pb = NSPasteboard.generalPasteboard()
last_count = pb.changeCount()

while True:
    current_count = pb.changeCount()
    if current_count != last_count:
        last_count = current_count
        content = pb.stringForType_(NSStringPboardType)
        if content:
            print(f"New clipboard: {content[:50]}...")
    time.sleep(0.1)
```

**Pros:** Real-time, no keyboard shortcuts needed, works globally  
**Cons:** Requires background process, polling consumes CPU/battery

---

## 4. File-Based Communication Bridges

### 4.1 Directory Watcher Approach

```python
#!/usr/bin/env python3
"""
File-based Bridge using directory watching
Antigravity, ChatGPT communicate via files in ~/bridge/
"""

import os
import json
import time
from pathlib import Path
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

BRIDGE_DIR = Path.home() / ".bridge" / "antigravity-chatgpt"
INBOX = BRIDGE_DIR / "to_chatgpt"
OUTBOX = BRIDGE_DIR / "from_chatgpt"

class BridgeHandler(FileSystemEventHandler):
    def on_created(self, event):
        if event.is_directory:
            return
        self.process_file(Path(event.src_path))
    
    def on_modified(self, event):
        if event.is_directory:
            return
        self.process_file(Path(event.src_path))
    
    def process_file(self, filepath: Path):
        try:
            with open(filepath, 'r') as f:
                data = json.load(f)
            
            action = data.get('action')
            content = data.get('content', '')
            
            if action == 'send_to_chatgpt':
                self.send_to_chatgpt(content)
                filepath.unlink()  # Clean up
                
            elif action == 'request_analysis':
                self.send_analysis_request(data)
                filepath.unlink()
                
        except Exception as e:
            print(f"Error processing {filepath}: {e}")
    
    def send_to_chatgpt(self, content: str):
        import subprocess
        # Copy to clipboard
        subprocess.run(['pbcopy'], input=content.encode())
        # Activate and paste
        subprocess.run(['open', '-a', 'ChatGPT'])
        time.sleep(0.3)
        subprocess.run(['osascript', '-e', 
            'tell application "System Events" to keystroke "v" using command down'])

class FileBridge:
    def __init__(self):
        BRIDGE_DIR.mkdir(parents=True, exist_ok=True)
        INBOX.mkdir(exist_ok=True)
        OUTBOX.mkdir(exist_ok=True)
        
    def start(self):
        observer = Observer()
        handler = BridgeHandler()
        observer.schedule(handler, str(INBOX), recursive=False)
        observer.start()
        
        print(f"Bridge monitor running on {INBOX}")
        try:
            while True:
                time.sleep(1)
        except KeyboardInterrupt:
            observer.stop()
        observer.join()
    
    def send_message(self, code: str, context: dict = None):
        """API for Antigravity extension to call"""
        message = {
            'action': 'send_to_chatgpt',
            'content': code,
            'timestamp': time.time(),
            'context': context or {}
        }
        
        filename = f"msg_{int(time.time() * 1000)}.json"
        filepath = INBOX / filename
        
        with open(filepath, 'w') as f:
            json.dump(message, f, indent=2)
        
        return filepath

if __name__ == "__main__":
    bridge = FileBridge()
    bridge.start()
```

### 4.2 VS Code Extension Side (TypeScript)

```typescript
// File: bridge-client.ts
// VS Code extension for Antigravity

import * as vscode from 'vscode';
import * as path from 'path';
import * as fs from 'fs/promises';
import * as os from 'os';

const BRIDGE_DIR = path.join(os.homedir(), '.bridge', 'antigravity-chatgpt');
const INBOX = path.join(BRIDGE_DIR, 'to_chatgpt');

export class BridgeClient {
    async sendToChatGPT(code: string, options: { 
        prompt?: string;
        language?: string;
    } = {}) {
        const message = {
            action: 'send_to_chatgpt',
            content: code,
            prompt: options.prompt || 'Analyze this code',
            language: options.language || 'typescript',
            timestamp: Date.now(),
            source: 'antigravity'
        };

        const filename = `msg_${Date.now()}.json`;
        const filepath = path.join(INBOX, filename);

        await fs.mkdir(INBOX, { recursive: true });
        await fs.writeFile(filepath, JSON.stringify(message, null, 2));

        // Wait for acknowledgment
        return this.waitForResponse(filename);
    }

    private async waitForResponse(filename: string): Promise<string> {
        const responsePath = path.join(BRIDGE_DIR, 'responses', filename);
        const maxAttempts = 30; // 30 seconds timeout
        
        for (let i = 0; i < maxAttempts; i++) {
            try {
                const response = await fs.readFile(responsePath, 'utf-8');
                await fs.unlink(responsePath); // Clean up
                const data = JSON.parse(response);
                return data.content;
            } catch {
                await new Promise(r => setTimeout(r, 1000));
            }
        }
        throw new Error('Bridge timeout - no response from ChatGPT');
    }
}

// VS Code Command Registration
export function registerCommands(context: vscode.ExtensionContext) {
    const bridge = new BridgeClient();
    
    const sendCommand = vscode.commands.registerCommand(
        'antigravity.sendToChatGPT',
        async () => {
            const editor = vscode.window.activeTextEditor;
            if (!editor) {
                vscode.window.showErrorMessage('No active editor');
                return;
            }

            const selection = editor.document.getText(editor.selection);
            if (!selection) {
                vscode.window.showErrorMessage('No text selected');
                return;
            }

            const language = editor