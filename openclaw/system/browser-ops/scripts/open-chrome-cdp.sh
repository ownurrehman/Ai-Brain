#!/bin/zsh
open -na "Google Chrome" --args --remote-debugging-port=9222 --user-data-dir="$HOME/.openclaw-chrome-cdp"
