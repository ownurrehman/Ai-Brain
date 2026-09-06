"""
mcp_oauth_loopback.py — Phase 3: MCP OAuth loopback server.

Replaces manual HTML redirect + WhatsApp code-sharing for OAuth flows.

What it does:
  1. Listens on a free localhost port (e.g. 8765, 9123, 10500) until an OAuth
     redirect hits `/callback?code=...&state=...`
  2. Renders a friendly success/failure HTML page
  3. Fires a Python callback with (code, state) so the calling script can
     immediately exchange it for a token
  4. Shuts down cleanly after one successful callback (or after timeout)

Replaces the old pattern:
  - python3 -m http.server 8080 (serves a file, not a callback)
  - User pastes redirect URL into WhatsApp
  - User copies the code from URL
  - Script does the token exchange

Inspired by Sand's `startMcpOAuthCallbackListener` (multiple origins, preempt
logic, expiry policy, log forwarding) but simplified to the 90% case.

Usage from a script:
    from mcp_oauth_loopback import wait_for_oauth_code

    code = wait_for_oauth_code(
        expected_state=state_token,
        timeout_seconds=300,
        on_success=lambda c, s: print(f"got code: {c[:10]}..."),
    )
    # exchange code for token here

Usage as standalone CLI:
    python mcp_oauth_loopback.py --port 8765 --expected-state abc123
    # then open the OAuth URL in a browser, the server captures the callback
"""
from __future__ import annotations

import argparse
import http.server
import json
import os
import socket
import sys
import threading
import time
import urllib.parse
from typing import Callable, Optional


# ---------- HTML templates (mimic Sand's success/error pages) ----------
SUCCESS_HTML = """<!doctype html><html><head><meta charset="utf-8">
<title>{provider} connected</title>
<style>
  body {{ font-family: -apple-system, sans-serif; background: #0a0a0a; color: #fff;
         display: flex; align-items: center; justify-content: center; height: 100vh;
         margin: 0; }}
  .card {{ background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px;
          padding: 40px 60px; text-align: center; max-width: 480px; }}
  .check {{ font-size: 48px; margin-bottom: 16px; }}
  h1 {{ margin: 0 0 8px; font-size: 22px; font-weight: 600; }}
  p {{ margin: 8px 0; color: #999; font-size: 14px; line-height: 1.5; }}
  .hint {{ color: #666; font-size: 12px; margin-top: 24px; }}
</style></head>
<body>
  <div class="card">
    <div class="check">&#10003;</div>
    <h1>{provider} connected</h1>
    <p>Authorization complete!</p>
    <p class="hint">You can close this tab and return to your terminal.</p>
  </div>
</body></html>"""

ERROR_HTML = """<!doctype html><html><head><meta charset="utf-8">
<title>{provider} &mdash; Authentication failed</title>
<style>
  body {{ font-family: -apple-system, sans-serif; background: #0a0a0a; color: #fff;
         display: flex; align-items: center; justify-content: center; height: 100vh;
         margin: 0; }}
  .card {{ background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px;
          padding: 40px 60px; text-align: center; max-width: 480px; }}
  .x {{ font-size: 48px; margin-bottom: 16px; color: #f44; }}
  h1 {{ margin: 0 0 8px; font-size: 22px; font-weight: 600; }}
  p {{ margin: 8px 0; color: #999; font-size: 14px; line-height: 1.5; }}
  .hint {{ color: #666; font-size: 12px; margin-top: 24px; }}
</style></head>
<body>
  <div class="card">
    <div class="x">&#10007;</div>
    <h1>{provider} &mdash; Authentication failed</h1>
    <p>{message}</p>
    <p class="hint">Close this tab and try connecting again.</p>
  </div>
</body></html>"""


# ---------- Port finder ----------
def find_free_port(preferred: Optional[int] = None) -> int:
    """Return a free localhost port. If preferred is given and free, use it."""
    if preferred:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            try:
                s.bind(("127.0.0.1", preferred))
                return preferred
            except OSError:
                pass
    # else: ask the OS for any free port
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.bind(("127.0.0.1", 0))
        return s.getsockname()[1]


# ---------- Handler ----------
class _CallbackHandler(http.server.BaseHTTPRequestHandler):
    # class-level config set by the request factory
    expected_state: Optional[str] = None
    provider: str = "Provider"
    result: dict = {}  # filled when callback fires; read by main thread
    fired: Optional[threading.Event] = None
    log: Callable[[str], None] = lambda msg: None

    def log_message(self, format, *args):
        # suppress default stderr noise; route through our log fn
        try:
            self.log(format % args)
        except TypeError:
            pass  # log callable doesn't accept extra args; ignore

    def do_GET(self):
        parsed = urllib.parse.urlparse(self.path)
        if parsed.path not in ("/", "/callback", "/oauth/callback", "/auth/callback"):
            self.send_response(404)
            self.send_header("Content-Type", "text/plain")
            self.end_headers()
            self.wfile.write(b"Not found.")
            return

        qs = urllib.parse.parse_qs(parsed.query)
        # error path
        if "error" in qs:
            err = qs.get("error", ["unknown"])[0]
            self.send_response(400)
            self.send_header("Content-Type", "text/html")
            self.end_headers()
            self.wfile.write(ERROR_HTML.format(
                provider=self.provider, message=f"OAuth error: {err}"
            ).encode())
            self.result["error"] = err
            if self.fired:
                self.fired.set()
            return

        code = qs.get("code", [None])[0]
        state = qs.get("state", [None])[0]

        if not code:
            self.send_response(400)
            self.send_header("Content-Type", "text/html")
            self.end_headers()
            self.wfile.write(ERROR_HTML.format(
                provider=self.provider, message="Missing authorization code"
            ).encode())
            self.result["error"] = "missing_code"
            if self.fired:
                self.fired.set()
            return

        if self.expected_state and state != self.expected_state:
            self.send_response(400)
            self.send_header("Content-Type", "text/html")
            self.end_headers()
            self.wfile.write(ERROR_HTML.format(
                provider=self.provider,
                message=f"State mismatch (expected {self.expected_state[:8]}..., got {state[:8] if state else 'None'}...)"
            ).encode())
            self.result["error"] = "state_mismatch"
            if self.fired:
                self.fired.set()
            return

        # success
        self.send_response(200)
        self.send_header("Content-Type", "text/html")
        self.end_headers()
        self.wfile.write(SUCCESS_HTML.format(provider=self.provider).encode())
        self.result["code"] = code
        self.result["state"] = state
        if self.fired:
            self.fired.set()

    def do_POST(self):
        # Some OAuth flows POST the code (e.g. device-code exchange on callback)
        length = int(self.headers.get("Content-Length", "0"))
        body = self.rfile.read(length).decode() if length else ""
        try:
            data = json.loads(body)
        except Exception:
            data = {}
        code = data.get("code")
        state = data.get("state")
        if code:
            self.result["code"] = code
            self.result["state"] = state
            if self.fired:
                self.fired.set()
            self.send_response(200)
            self.send_header("Content-Type", "text/plain")
            self.end_headers()
            self.wfile.write(b"OK")
        else:
            self.send_response(400)
            self.send_header("Content-Type", "text/plain")
            self.end_headers()
            self.wfile.write(b"missing code")


# ---------- Public API ----------
def wait_for_oauth_code(
    expected_state: Optional[str] = None,
    timeout_seconds: int = 300,
    port: Optional[int] = None,
    provider: str = "Provider",
    on_success: Optional[Callable[[str, Optional[str]], None]] = None,
    on_error: Optional[Callable[[str], None]] = None,
) -> Optional[str]:
    """Block until an OAuth callback hits the loopback. Returns the code, or None on timeout/error.

    Args:
        expected_state: if set, reject callbacks with mismatched state
        timeout_seconds: max wait (default 5 min)
        port: specific port to use (else picks a free one)
        provider: name shown in the success HTML
        on_success: optional callback (code, state) when code is received
        on_error: optional callback (error_msg) on failure
    """
    actual_port = find_free_port(port)
    fired = threading.Event()
    result: dict = {}

    handler = type(f"_Handler_{actual_port}", (_CallbackHandler,), {
        "expected_state": expected_state,
        "provider": provider,
        "result": result,
        "fired": fired,
    })

    server = http.server.HTTPServer(("127.0.0.1", actual_port), handler)
    server.timeout = 1  # poll for shutdown
    print(f"[oauth] loopback listening on http://127.0.0.1:{actual_port}/callback")
    print(f"[oauth] waiting up to {timeout_seconds}s for {provider} callback...")

    # serve in a thread so we can timeout
    server_thread = threading.Thread(target=server.serve_forever, daemon=True)
    server_thread.start()

    fired.wait(timeout=timeout_seconds)
    server.shutdown()
    server.server_close()

    if "error" in result:
        msg = f"OAuth failed: {result['error']}"
        print(f"[oauth] {msg}")
        if on_error:
            on_error(result["error"])
        return None
    if not result.get("code"):
        print(f"[oauth] timed out after {timeout_seconds}s")
        if on_error:
            on_error("timeout")
        return None

    code = result["code"]
    state = result.get("state")
    print(f"[oauth] got code: {code[:10]}... (state: {(state or '')[:8]}...)")
    if on_success:
        on_success(code, state)
    return code


# ---------- CLI ----------
def main():
    ap = argparse.ArgumentParser(description="MCP OAuth loopback server")
    ap.add_argument("--port", type=int, help="preferred port (else auto-pick)")
    ap.add_argument("--expected-state", help="validate state param matches")
    ap.add_argument("--timeout", type=int, default=300)
    ap.add_argument("--provider", default="OAuth")
    ap.add_argument("--print-code", action="store_true",
                    help="print code to stdout (for piping to next command)")
    args = ap.parse_args()

    code = wait_for_oauth_code(
        expected_state=args.expected_state,
        timeout_seconds=args.timeout,
        port=args.port,
        provider=args.provider,
    )
    if code and args.print_code:
        # machine-readable: just the code on stdout
        print(code)
    sys.exit(0 if code else 1)


if __name__ == "__main__":
    main()
